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
 * @filesource
 * @package vBulletinAPI
 */

/**
 * A factory for getting the correct action class for the given action name.
 *
 * An action is simply a wrapper around a method that takes an associative 
 * array, acts upon its contents and returns another associative array. All 
 * actions will be a subclass of the base {@link Action} class.
 *
 * @package vBulletinAPI
 */
class Actions {
	/**
	 * Get the action class corresponding to the given action name.
	 *
	 * @param string $action_name	The name of the action to retrieve. If this is 
	 *								"fooBar" the class FooBarAction will be 
	 *								looked up and if it exists an instance 
	 *								returned. If the action class requested 
	 *								does not exist an exception will be thrown.
	 * @return Action				An instance of the Action requested.
	 */
	public static function getAction($action_name) {
		if (!(preg_match("/^[a-z][a-zA-Z0-9]*$/", $action_name))) {
			throw new Exception("Bad action name '$action_name'");
		}

		$action_class_name = ucfirst($action_name) . "Action";
		$action_class_file = dirname(__FILE__) . "/actions/$action_class_name.php";

		if (@include($action_class_file)) {
			try {
				$action = new $action_class_name();
				if ($action instanceof Action) {
					return $action;
				} else {
					throw new Exception("No such action '$action_name'");
				}
			} catch (Exception $e) {
				throw new Exception("No such action '$action_name'");
			}
		} else {
			throw new Exception("No such action '$action_name'");
		}
	}

	/** Get all the actions currently available.
	 *
	 *	@return	array	An array of all the {@link Action}s currently defined, 
	 *					ordered by name. 
	 */
	public static function getAllActions() {
		$action_suffix = "Action.php";
		$actions = array();
		if ($dirhandle = opendir(dirname(__FILE__) . "/actions")) {
			while (($file = readdir($dirhandle)) !== FALSE) {
				if (substr($file, -(strlen($action_suffix))) === $action_suffix) {
					$action_name = substr($file, 0, strlen($file) - strlen($action_suffix));
					$action_name[0] = strtolower($action_name[0]);
					try {
						$actions[$action_name] = self::getAction($action_name);
					} catch (Exception $e) {
						// Ignore, we're only going to deal with the working Actions
					}
				}
			}
		}
		ksort($actions);
		reset($actions);
		return array_values($actions);
	}
}

?>
