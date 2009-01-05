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
/** Category class. */
require_once(dirname(dirname(__FILE__)) . "/data/Category.php");
/** Forum class. */
require_once(dirname(dirname(__FILE__)) . "/data/Forum.php");
/** Thread class. */
require_once(dirname(dirname(__FILE__)) . "/data/Thread.php");
/** User class. */
require_once(dirname(dirname(__FILE__)) . "/data/User.php");

/**	Get the parent of a given object.
 *
 *	@package vBulletinAPI
 */
class GetParentAction
extends Action
{
	/** Define the arguments that this action will accept.
	 *
	 *	@return	array	See {@link Action::argumentSpec()} for details of the 
	 *					format.
	 */
	protected function argumentSpec() {
		return array(
			"object" => array(
				"description" => "The object to find the parent of"
			),
			"user" => array(
				"description" => "The currently logged-in user.",
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
			"mixed" => "An object which is the parent of the given object or NULL if it has no parent."
		);
	}

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public function getDescription() {
		return "Get the parent of a given object.";
	}

	/** Get the parent of the given object.
	 *
	 *	@param	array $arguments	The arguments to the action. See {@link 
	 *								argumentSpec()} for details.
	 *	@return	mixed				The parent object of the given object or 
	 *								NULL if it has no parent.
	 */
	protected function doIt($arguments) {
		$object = $arguments['object'];
		$user = $arguments['user'];

		if ($object instanceof Site) {
			return NULL;
		} else if (($object instanceof Category) || ($object instanceof Forum)) {
			if ($object->parent !== NULL) {
				return $object->parent;
			}

			$forum_info = VBulletin::call('fetch_foruminfo', $object->id);
			if ($forum_info['parentid'] === -1) {
				$get_site_action = Actions::getAction("getSite");
				return $get_site_action->execute(array("user" => $user));
			}
			
			$parent_info = VBulletin::call('fetch_foruminfo', $forum_info['parentid']);

			if ($parent_info['options'] & Forum::$STATUS_IS_FORUM) {
				return new Forum($forum_info['parentid']);
			} else {
				return new Category($forum_info['parentid']);
			}
		} else if ($object instanceof Thread) {
			if ($object->forum !== NULL) {
				return $object->forum;
			}

			$thread_info = VBulletin::call('fetch_threadinfo', $object->id);
			return new Forum($thread_info['forumid']);
		} else if ($object instanceof Post) {
			if ($object->thread !== NULL) {
				return $object->thread;
			}

			$post_info = VBulletin::call('fetch_postinfo', $object->id);
			return new Thread($post_info['threadid']);
		} else {
			throw new Exception('Objects of type "' . get_class($object) . '" don\'t have parents');
		}
	}
}

?>
