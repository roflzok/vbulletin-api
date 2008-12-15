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

/** Configuration file. */
require_once(dirname(dirname(__FILE__)) . "/config.php");

/** A facade for vBulletin so that other classes may call its functions.
 *
 *	@package vBulletinAPI
 */
class VBulletin {
	/** vBulletin makes use of many global variables, we store them here so 
	 *	that they can be re-used later.
	 */
	private static $vbulletin_globals = array();

	/** A marker to prevent this facade from being initialised more than once.
	 */
	private static $vbulletin_is_initialised = FALSE;

	/** Initialise the connection to vBulletin.
	 *
	 *	@param	string $vbulletin_path	The path to the root of a vBulletin 
	 *									install.
	 */
	private static function init($vbulletin_path) {
		if (!self::$vbulletin_is_initialised) {
			if ($vbulletin_path !== "" && is_dir($vbulletin_path)) {
				define('VB_AREA', 'Forum');
				$cwd = getcwd();
				chdir($vbulletin_path);
				require_once("$vbulletin_path/includes/init.php");
				chdir($cwd);

				self::$vbulletin_globals['vbulletin'] = $vbulletin;
			} else {
				throw new Exception("Path to vBulletin is not configured");
			}
			self::$vbulletin_is_initialised = TRUE;
		}
	}

	/** Call a vBulletin function.
	 *
	 *	@param	string $function_name	The name of the vBulletin function to 
	 *									call. 
	 *	@param	mixed $param,...		The parameters to pass to the vBulletin 
	 *									function
	 *	@return	mixed					The return value of the vBulletin 
	 *									function.
	 */
	public static function call() {
		$func_args = func_get_args();

		// Make sure the bridge is initialised.
		self::init($GLOBALS['VBULLETIN_PATH']);

		// Pull in the vBulletin globals
		foreach (self::$vbulletin_globals as $name => $value) {
			$GLOBALS[$name] = $value;
		}

		// Find the function and call it.
		switch ($func_args[0]) {
			case "fetch_post_ids":
				return self::fetchPostIDs($func_args[1], $func_args[2], $func_args[3], $func_args[4], $func_args[5]);
			case "fetch_postinfo":
				return fetch_postinfo($func_args[1]);
			case "fetch_threadinfo":
				return fetch_threadinfo($func_args[1]);
			default:
				throw new Exception("No vBulletin stub defined for {$func_args[0]}");
		}
	}

	/** Pseudo-vBulletin function to get the post IDs associated with a given 
	 *	thread.
	 *
	 *	@param	int	$thread_id	The ID of the thread to fetch the posts from.
	 *	@return	array			An array of post IDs in chronological order, 
	 *							oldest first.
	 */
	private static function fetchPostIDs($thread_id, $start, $count, $can_see_deleted_posts, $can_see_hidden_posts) {
		if (preg_match("/^\d+$/", $thread_id)) {
			// Convenience alias
			$db = $GLOBALS['vbulletin']->db;

			// Remove the posts that aren't allowed to be seen 
			$filter = "";
			if ((!$can_see_deleted_posts) && (!$can_see_hidden_posts)) {
				$filter = " AND visible = 1";
			} else if (!$can_see_deleted_posts) {
				$filter = " AND visible < 2";
			} else if (!$can_see_hidden_posts) {
				$filter = " AND visible > 0";
			}

			// Fix the limit bounds
			$start--;
			if ($start < 0) {
				$start = 0;
			}
			if ($count < 0) {
				$count = PHP_INT_MAX;
			}

			// Read all the post numbers
			$dbres = $db->query_read(
				"SELECT postid, dateline" .
				" FROM " . TABLE_PREFIX . "post" .
				" WHERE threadid = " . $thread_id .
				$filter .
				" ORDER BY dateline" .
				" LIMIT $start, $count"
			);
			$posts = array();
			$post_count = 0;
			while ($post = $db->fetch_array($dbres)) {
				$posts[$post_count]['id'] = $post['postid'];
				$posts[$post_count]['createTime'] = $post['dateline'];
				$post_count++;
			}
			$db->free_result($dbres);

			if (count($posts) > 0) {
				return $posts;
			} else {
				throw Exception("No such thread");
			}
		} else {
			throw Exception("Bad thread ID passed.");
		}
	}
}
?>
