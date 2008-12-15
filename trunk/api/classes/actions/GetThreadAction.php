<?php
/*
 * Copyright (c) 2008, Conor McDermottroe
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, 
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice, 
 *    this list of conditions and the following disclaimer in the documentation 
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of the author nor the names of any contributors to the 
 *    software may be used to endorse or promote products derived from this 
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 */
/**
 *	@package	vBulletinAPI
 *	@filesource
 */

require_once("base/Action.php");
require_once(dirname(dirname(__FILE__)) . "/Utils.php");
require_once(dirname(dirname(__FILE__)) . "/VBulletin.php");
require_once(dirname(dirname(__FILE__)) . "/data/Icon.php");
require_once(dirname(dirname(__FILE__)) . "/data/Poll.php");
require_once(dirname(dirname(__FILE__)) . "/data/Post.php");
require_once(dirname(dirname(__FILE__)) . "/data/Thread.php");
require_once(dirname(dirname(__FILE__)) . "/data/User.php");

/**	Get a thread or part of a thread.
 *
 *	@package vBulletinAPI
 */
class GetThreadAction
extends Action
{
	/** Define the arguments that this action will accept.
	 *
	 *	@return	array	See {@link Action::argumentSpec()} for details of the 
	 *					format.
	 */
	protected function argumentSpec() {
		return array(
			"threadId" => array(
				"description" => "The ID of the thread to get",
				"validationFunction" => "is_integer"
			),
			"start" => array(
				"description" => "The index of the lowest post you want to appear in the results. Posts are indexed starting at 1.",
				"validationFunction" => "is_integer",
				"defaultValue" => 1
			),
			"count" => array(
				"description" => "The maximum number of posts to retrieve",
				"validationFunction" => "is_integer",
				"defaultValue" => 10
			)
		);
	}

	/** Specify the return value of the action.
	 *
	 *	@return	array	An associative array where the key is the PHP type of 
	 *					the return and the value is a string describing it 
	 *					further.
	 */
	public function returnSpec() {
		return array(
			"Thread" => "A structure containing all or part of a thread"
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Fetch the posts and meta-data for a thread";
	}

	/** Fetch the contents of the thread.
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link argumentSpec()} 
	 *								for details.
	 *	@return	Thread				A {@link Thread} object containing some or 
	 *								all of the posts in a thread and some data 
	 *								about the thread itself.
	 */
	protected function doIt($arguments) {
		$thread_id = $arguments['threadId'];
		$start = $arguments['start'];
		$count = $arguments['count'];

		// FIXME
		$can_see_deleted_posts = FALSE;
		$can_see_hidden_posts = FALSE;
		$can_see_deleted_threads = FALSE;

		$thread_info = VBulletin::call("fetch_threadinfo", $thread_id);
		if ($thread_info === NULL) {
			throw new Exception("The thread does not exist.");
		}

		// Calculate the status of the thread
		$thread_status = 0;
		if ($thread_info['isdeleted']) {
			if ($can_see_deleted_threads) {
				$thread_status |= Thread::$STATUS_DELETED;
			} else {
				throw new Exception("The thread does not exist.");
			}
		}
		if ($thread_info['open'] == 0) {
			$thread_status |= Thread::$STATUS_LOCKED;
		}
		if ($thread_info['sticky']) {
			$thread_status |= Thread::$STATUS_STICKY;
		}
		if ($thread_info['attach']) {
			$thread_status |= Thread::$STATUS_HAS_ATTACHMENTS;
		}
		if ($can_see_hidden_posts) {
			if ($thread_info['hiddencount']) {
				$thread_status |= Thread::$STATUS_HAS_HIDDEN_POSTS;
			}
		}
		if ($can_see_deleted_posts) {
			if ($thread_info['deletedcount']) {
				$thread_status |= Thread::$STATUS_HAS_DELETED_POSTS;
			}
		}

		// Create the thread and add some properties (if they're available).
		$thread = new Thread($thread_id);
		$thread->status = $thread_status;
		if (array_key_exists('forumid', $thread_info)) {
			$thread->forumId = $thread_info['forumid'];
		}
		if (array_key_exists('title', $thread_info)) {
			$thread->title = $thread_info['title'];
		}
		if (array_key_exists('dateline', $thread_info)) {
			$thread->createTime = Utils::epochTimeToDateTime($thread_info['dateline']);
		}
		if (array_key_exists('lastpost', $thread_info)) {
			$thread->lastUpdateTime = Utils::epochTimeToDateTime($thread_info['lastpost']);
		}
		if (array_key_exists('replycount', $thread_info)) {
			$thread->numPosts = $thread_info['replycount'] + 1;
		}
		if (array_key_exists('views', $thread_info)) {
			$thread->numViews = $thread_info['views'];
		}
		if (array_key_exists('pollid', $thread_info)) {
			if ($thread_info['pollid'] > 0) {
				$thread->poll = new Poll($thread_info['pollid']);
			}
		}
		if (array_key_exists('iconid', $thread_info)) {
			if ($thread_info['iconid'] > 0) {
				$thread->icon = new ThreadIcon($thread_info['iconid']);
			}
		}
		if	(
				array_key_exists('votenum', $thread_info) &&
				array_key_exists('votetotal', $thread_info)
			)
		{
			if ($thread_info['votenum'] > 0) {
				$thread->numStars = (
					$thread_info['votetotal'] / 
					$thread_info['votenum']
				);
			}
		}

		// Find the requested posts.
		$thread_posts = VBulletin::call('fetch_post_ids', $thread_id, $start, $count, $can_see_deleted_posts, $can_see_hidden_posts);
		if ($thread_posts) {
			// Use the GetPostAction to fetch the post information
			$getPostAction = Actions::getAction("getPost");

			$posts = array();
			foreach ($thread_posts as $thread_post) {
				$posts[] = $getPostAction->execute(array("postId" => (int)$thread_post['id']));
			}

			$thread->posts = $posts;
		}

		return $thread;
	}
}

?>
