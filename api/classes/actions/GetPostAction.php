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
/** vBulletin post. */
include_once(dirname(dirname(__FILE__)) . "/data/Post.php");
/** vBulletin user. */
include_once(dirname(dirname(__FILE__)) . "/data/User.php");

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
	 *	@todo						This function needs to actually call the 
	 *								vBulletin functions and return the post 
	 *								properly.
	 */
	protected function doIt($arguments) {
		$post_id = $arguments['postId'];

		// Fake data, replace with the results of a call to the vBulletin API
		$poster_id = 103325;
		$poster_name = "IRLConor";
		$post_contents = "Hello World!";
		
		return new Post($post_id, new User($poster_id, $poster_name), new DateTime(), $post_contents);
	}
}

?>
