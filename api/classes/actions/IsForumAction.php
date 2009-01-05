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
/** Bridge to vBulletin. */
require_once(dirname(dirname(__FILE__)) . "/VBulletin.php");
/** Forum class. */
require_once(dirname(dirname(__FILE__)) . "/data/Forum.php");

/**	Check if a given ID is a valid ID for a forum.
 *
 *	@package vBulletinAPI
 */
class IsForumAction
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
				"description" => "The ID of the forum to check for.",
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
			"boolean" => "True if the ID is valid for a forum, false otherwise."
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Check if a given ID is a valid forum ID.";
	}

	/** Check if the ID is a valid forum ID.
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link 
	 *								argumentSpec()} for details.
	 *	@return	boolean				True if the ID is a forum ID, false 
	 *								otherwise.
	 */
	protected function doIt($arguments) {
		$forum_id = $arguments['forumId'];

		$forum_info = VBulletin::call("fetch_foruminfo", $forum_id);
		if ($forum_info === FALSE) {
			return FALSE;
		}
		if (array_key_exists('options', $forum_info)) {
			if ($forum_info['options'] & Forum::$STATUS_IS_FORUM) {
				return TRUE;
			}
		}
		return FALSE;
	}
}

?>
