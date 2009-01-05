<?php
/*
 * Copyright (c) 2008, 2009 Conor McDermottroe
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

/** The base Action. */
require_once("base/Action.php");
/** Utility functions. */
require_once(dirname(dirname(__FILE__)) . "/Utils.php");
/** Bridge to vBulletin. */
require_once(dirname(dirname(__FILE__)) . "/VBulletin.php");
/** Forum class. */
require_once(dirname(dirname(__FILE__)) . "/data/Forum.php");
/** User class. */
require_once(dirname(dirname(__FILE__)) . "/data/User.php");

/**	Get a forum including a list of some or all of the threads within it.
 *
 *	@package vBulletinAPI
 */
class GetForumAction
extends Action
{
	/** Define the arguments that this action will accept.
	 *
	 *	@return	array	See {@link Action::argumentSpec()} for details of the 
	 *					format.
	 */
	protected function argumentSpec() {
		return array(
			"forumId" => array(
				"description" => "The ID of the forum to fetch the details for.",
				"validationFunction" => "is_integer"
			),
			"numThreads" => array(
				"description" => "The number of threads to display",
				"validationFunction" => "is_integer",
				"defaultValue" => 10
			),
			"skipThreads" => array(
				"description" => "The number of threads to skip before adding them to the returned structure",
				"validationFunction" => "is_integer",
				"defaultValue" => 0
			),
			"password" => array(
				"description" => "If the forum needs a password, set it here to access the forum.",
				"defaultValue" => NULL
			),
			"user" => array(
				"description" => "The user who is making the request",
				"defaultValue" => new User(0)
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
			"Forum" => "A structure containing all or part of a forum"
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Fetch the threads and meta-data for a forum";
	}

	/** Fetch the contents of the forum.
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link 
	 *								argumentSpec()} for details.
	 *	@return	Forum				A {@link Forum} object containing some or 
	 *								all of the threads in a forum and some data 
	 *								about the forum itself.
	 */
	protected function doIt($arguments) {
		$forum_id = $arguments['forumId'];
		$start = $arguments['skipThreads'];
		$count = $arguments['numThreads'];
		$password = $arguments['password'];
		$user = $arguments['user'];

		$forum_info = VBulletin::call("fetch_foruminfo", $forum_id);
		if ($forum_info === FALSE) {
			throw new Exception("No such forum");
		}
		$is_forum_action = Actions::getAction("isForum");
		if (!$is_forum_action->execute(array("forumId" => $forum_id))) {
			throw new Exception("No such forum");
		}
		if (array_key_exists('password', $forum_info)) {
			if ($password !== NULL && $password !== $forum_info['password']) {
				throw new Exception("The forum requires a password");
			}
		}

		// Build the basic details of the forum
		$forum = new Forum($forum_id);
		if (array_key_exists('title', $forum_info)) {
			$forum->name = htmlspecialchars_decode($forum_info['title']);
		}
		if (array_key_exists('description', $forum_info)) {
			$forum->description = htmlspecialchars_decode(
				$forum_info['description']
			);
		}
		if (array_key_exists('options', $forum_info)) {
			$forum->status = $forum_info['options'];
		}
		if (array_key_exists('showprivate', $forum_info)) {
			$forum->privacy = $forum_info['showprivate'];
		}
		if (array_key_exists('daysprune', $forum_info)) {
			$forum->daysToDisplay = $forum_info['daysprune'];
		}
		if (array_key_exists('link', $forum_info)) {
			$forum->link = $forum_info['link'];
		}

		// Sort out the parent and children of the forum
		if (array_key_exists('parentid', $forum_info)) {
			$forum->parent = Utils::getObject($forum_info['parentid']);
		}
		if (array_key_exists('childlist', $forum_info)) {
			$sub_forums = array();
			foreach (explode(",", $forum_info['childlist']) as $child_id) {
				$child_id = (int)$child_id;
				if ($child_id !== -1 && $child_id !== $forum_id) {
					$sub_forums[] = Utils::getObject($child_id);
				}
			}
			$forum->subForums = $sub_forums;
		}

		// Get the threads for the forum
		$thread_ids = VBulletin::fetchThreadIDs($forum, $user, $start, $count);
		$threads = array();
		$get_thread_action = Actions::getAction("getThread");
		foreach ($thread_ids as $thread_id) {
			$thread = $get_thread_action->execute(
				array(
					"threadId" => $thread_id,
					"count" => 1
				)
			);
			unset($thread->posts);
			$threads[] = $thread;
		}
		$forum->threads = $threads;

		return $forum;
	}
}

?>
