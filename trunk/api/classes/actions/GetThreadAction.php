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

/** Include base class. */
include_once("base/Action.php");
/** vBulletin thread. */
include_once(dirname(dirname(__FILE__)) . "/data/Thread.php");
/** vBulletin post. */
include_once(dirname(dirname(__FILE__)) . "/data/Post.php");
/** vBulletin user. */
include_once(dirname(dirname(__FILE__)) . "/data/User.php");

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
	protected function returnSpec() {
		return array(
			"Thread" => "A structure containing all or part of a thread"
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	protected function getDescription() {
		return "Fetch the posts and meta-data for a thread";
	}

	/** Fetch the contents of the thread.
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link argumentSpec()} 
	 *								for details.
	 *	@return	Thread				A {@link Thread} object containing some or 
	 *								all of the posts in a thread and some data 
	 *								about the thread itself.
	 *	@todo						This function needs to actually call the 
	 *								vBulletin functions.
	 */
	protected function doIt($arguments) {
		$thread_id = $arguments['threadId'];
		$start = $arguments['start'];
		$count = $arguments['count'];

		// XXX Fake data XXX
		$fake_user = new User(103325, "IRLConor");
		$fake_post_time = new DateTime();
		$fake_posts = array();
		for ($i = $start; $i < $count; $i++) {
			$fake_post_contents = "Post number $i here.";
			$fake_posts[$i] = new Post(($thread_id + $i), $fake_user, $fake_post_time, $fake_post_contents);
		}
		
		return new Thread($thread_id, $fake_posts);
	}
}

?>
