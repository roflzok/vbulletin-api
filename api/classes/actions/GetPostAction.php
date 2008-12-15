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

/** The base Action. */
require_once("base/Action.php");
/** Utilities class. */
require_once(dirname(dirname(__FILE__)) . "/Utils.php");
/** Bridge to vBulletin. */
require_once(dirname(dirname(__FILE__)) . "/VBulletin.php");
/** Post object. */
require_once(dirname(dirname(__FILE__)) . "/data/Post.php");
/** PostEdit object. */
require_once(dirname(dirname(__FILE__)) . "/data/PostEdit.php");
/** User object. */
require_once(dirname(dirname(__FILE__)) . "/data/User.php");

/**	Get a single post.
 *
 *	@package vBulletinAPI
 */
class GetPostAction
extends Action
{
	/** Define the arguments that this action will accept.
	 *
	 *	@return	array	See {@link Action::argumentSpec()} for details of the 
	 *					format.
	 */
	protected function argumentSpec() {
		return array(
			"postId" => array(
				"description" => "The ID of the post to get",
				"validationFunction" => "is_integer"
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
			"Post" => "A structure describing the post"
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Fetch the contents and meta-data for a post.";
	}

	/** Fetch the contents of the post.
	 *
	 *	@param	array $arguments	The arguments to the action. There is only 
	 *								one argument, the post ID specified with a 
	 *								key of 'postId'.
	 *	@return	Post				The post requested. If the post does not 
	 *								exist or you are forbidden from viewing it 
	 *								an exception will be thrown.
	 */
	protected function doIt($arguments) {
		$post_id = $arguments['postId'];

		// FIXME:
		$can_see_deleted_posts = FALSE;
		$can_see_hidden_posts = FALSE;
		$can_see_infractions = FALSE;
		$can_see_ip_addresses = FALSE;
		$can_see_report_threads = FALSE;

		// Get the post info
		$post_info = VBulletin::call("fetch_postinfo", $post_id);
		if ($post_info == NULL) {
			throw new Exception("Post does not exist");
		}

		// Work out the status of the post
		$status = 0;
		if (array_key_exists('visible', $post_info)) {
			if ($post_info['visible'] == 0) {
				if ($can_see_hidden_posts) {
					$status |= Post::$STATUS_INVISIBLE;
				} else {
					throw new Exception("Post does not exist");
				}
			}
		}
		if (array_key_exists('isdeleted', $post_info)) {
			if ($post_info['isdeleted']) {
				if ($can_see_deleted_posts) {
					$status |= Post::$STATUS_DELETED;
				} else {
					throw new Exception("Post does not exist");
				}
			}
		}
		if (array_key_exists('attach', $post_info)) {
			if ($post_info['attach']) {
				$status |= Post::$STATUS_HAS_ATTACHMENT;
			}
		}

		// Construct the post
		$post = new Post($post_id);
		if (array_key_exists('threadid', $post_info)) {
			$post->threadId = $post_info['threadid'];
		}
		if	(
				array_key_exists('userid', $post_info) &&
				array_key_exists('username', $post_info)
			) 
		{
			$post->author = new User(
				$post_info['userid'], $post_info['username']
			);
		}
		if (array_key_exists('dateline', $post_info)) {
			$post->createTime = Utils::epochTimeToDateTime(
				$post_info['dateline']
			);
		}
		if (array_key_exists('pagetext', $post_info)) {
			$post->text = $post_info['pagetext'];
		}
		if (array_key_exists('title', $post_info)) {
			if ($post_info['title']) {
				$post->title = $post_info['title'];
			}
		}
		$post->status = $status;
		if	(
				array_key_exists('edit_dateline', $post_info) &&
				array_key_exists('edit_userid', $post_info)
			) 
		{
			if ($post_info['edit_dateline'] != NULL) {
				$edit = new PostEdit(
					Utils::epochTimeToDateTime($post_info['edit_dateline']),
					new User($post_info['edit_userid'])
				);
				if (array_key_exists('edit_reason', $post_info)) {
					$edit->reason = $post_info['edit_reason'];
				}
				$post->edited = $edit;
			}
		}
		if (array_key_exists('iconid', $post_info)) {
			if ($post_info['iconid'] > 0) {
				$post->icon = new Icon($post_info['iconid']);
			}
		}
		if ($can_see_infractions) {
			if (array_key_exists('infraction', $post_info)) {
				if ($post_info['infraction']) {
					$post->infraction = new Infraction($post_info['infraction']);
				}
			}
		}
		if ($can_see_report_threads) {
			if (array_key_exists('reportthreadid', $post_info)) {
				if ($post_info['reportthreadid']) {
					$post->reportThread = new Thread($post_info['reportthreadid']);
				}
			}
		}
		if ($can_see_ip_addresses) {
			if (array_key_exists('ipaddress', $post_info)) {
				$post->ip = $post_info['ipaddress'];
			}
		}

		return $post;
	}
}

?>
