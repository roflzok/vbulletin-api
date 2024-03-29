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
 * @filesource
 * @package vBulletinAPI
 */

/** The actions factory. */
require_once("Actions.php");
/** A vBulletin Category. */
require_once("data/Category.php");
/** A vBulletin Forum. */
require_once("data/Forum.php");

/**
 * A set of utility functions.
 *
 * @package vBulletinAPI
 */
class Utils {
	/**
	 * Convert a UNIX epoch time into a DateTime object.
	 *
	 * @param	int $epoch_time	The UNIX epoch time to convert into a DateTime.
	 * @return	DateTime		A DateTime object corresponding to the epoch
	 *							time provided.
	 */
	public static function epochTimeToDateTime($epoch_time) {
		return new DateTime(
			gmstrftime("%Y-%m-%d %T", $epoch_time),
			new DateTimeZone("UTC")
		);
	}

	/**
	 * Given an ID, get the {@link Forum} or {@link Category} or {@link Site} 
	 * associated with that ID or throw an exception if the ID does not 
	 * correspond to an object of those types.
	 *
	 * @param	int	$id				The ID to fetch the object for.
	 * @param	Site|Category|Forum	The object corresponding to the given ID.
	 */
	public static function getObject($id) {
		if ($id == -1) {
			$get_site_action = Actions::getAction("getSite");
			return $get_site_action->execute();
		} else {
			$is_forum_action = Actions::getAction("isForum");
			if ($is_forum_action->execute(array("forumId" => $id))) {
				return new Forum($id);
			} else {
				$is_category_action = Actions::getAction("isCategory");
				if ($is_category_action->execute(array("categoryId" => $id))) {
					return new Category($id);
				} else {
					throw new Exception(
						"ID '$id' does not correspond to any forum or category."
					);
				}
			}
		}
	}
}

?>
