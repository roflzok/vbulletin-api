<?php
/*
 * Copyright (c) 2009 Conor McDermottroe
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
/** User class. */
require_once(dirname(dirname(__FILE__)) . "/data/User.php");

/**	Log in to the site.
 *
 *	@package vBulletinAPI
 */
class LoginAction
extends Action
{
	/** Define the arguments that this action will accept.
	 *
	 *	@return	array	See {@link Action::argumentSpec()} for details of the 
	 *					format.
	 */
	protected function argumentSpec() {
		return array(
			"user" => array(
				"description" => "The user to log in as"
			)
		);
	}

	/** Specify the return value of the action.
	 *
	 *	@return	array	A copy of the {@link User} which was passed in, but now 
	 *					logged in.
	 */
	public function returnSpec() {
		return array(
			"User" => "The logged-in User"
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Log in to the site";
	}

	/** Log in to the site
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link 
	 *								argumentSpec()} for details.
	 *	@return	User				A copy of the {@link User} which was passed 
	 *								in, but now logged in.
	 */
	protected function doIt($arguments) {
		$user = $arguments['user'];

		// TODO

		return $user;
	}
}

?>
