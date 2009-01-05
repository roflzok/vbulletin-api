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
/* XXX Autogenerated - do not edit XXX*/
/**	@package vBulletinAPI
 *	@filesource
 */

/** Base data class. */
require_once("DataObject.php");

/** Represent all or part of a forum in a vBulletin system.
 *
 *	@property	int $id 			The ID of the forum within vBulletin
 *	@property	string $name		The forum name.
 *	@property	string $description The description of what the forum is for.
 *	@property	mixed $parent		The parent object for this forum. It could
 *									be either a {@link Site}, a {@link
 *									Category} or a {@link Forum}.
 *	@property	int $status 		A bitmask describing many properties of the
 *									forum. See the STATUS_ constants for all
 *									the status bits that may be set.
 *	@property	int $privacy		Whether the forum is private or not and if
 *									so, whether or not the posts count towards
 *									a post count.
 *	@property	array $threads		An array where the keys are the date of
 *									last update and the values are {@link
 *									Thread} objects.
 *	@property	array $subForums	An array of {@link Forum}s which are
 *									children of this forum.
 *	@property	string $link		If the forum is simply a link through to
 *									somewhere else, this is the URL.
 *	@property	string $password	The password for the forum, if any.
 *	@property	int $daysToDisplay	The number of days worth of posts to
 *									display.
 *	@package	vBulletinAPI
 */
class Forum
extends DataObject
{
	/** The forum is active. */
	public static $STATUS_ACTIVE = 1;

	/** The forum allows new posts to be made. */
	public static $STATUS_ALLOW_POSTING = 2;

	/** The forum is a forum and not a category. */
	public static $STATUS_IS_FORUM = 4;

	/** New posts in the forum must be moderated. */
	public static $STATUS_NEW_POSTS_MUST_BE_MODERATED = 8;

	/** New threads in the forum must be moderated. */
	public static $STATUS_NEW_THREADS_MUST_BE_MODERATED = 16;

	/** Attachments must be approved before becoming visible. */
	public static $STATUS_ATTACHMENTS_MUST_BE_MODERATED = 32;

	/** BBCode is allowed in posts in this forum. */
	public static $STATUS_ALLOW_BBCODE = 64;

	/** Images are allowed in posts in this forum. */
	public static $STATUS_ALLOW_IMAGES = 128;

	/** HTML is allowed in posts in this forum. */
	public static $STATUS_ALLOW_HTML = 256;

	/** Smilies are allowed in posts in this forum. */
	public static $STATUS_ALLOW_SMILIES = 512;

	/** Thread/post icons are allowed in this forum. */
	public static $STATUS_ALLOW_ICONS = 1024;

	/** Threads in this forum may be rated. */
	public static $STATUS_ALLOW_RATINGS = 2048;

	/** Posts in this forum count towards post counts. */
	public static $STATUS_POSTS_COUNT = 4096;

	/** The forum can have a password. */
	public static $STATUS_CAN_HAVE_PASSWORD = 8192;

	/** Index these posts for search. */
	public static $STATUS_INDEX_POSTS = 16384;

	/** Style is forced by admin, not selected by user. */
	public static $STATUS_STYLE_OVERRIDE = 32768;

	/** This forum is shown on the forum jump mennu. */
	public static $STATUS_SHOW_ON_FORUM_JUMP = 65536;

	/** Threads in this forum must have a prefix. */
	public static $STATUS_PREFIX_REQUIRED = 131072;

	/** The privacy of the forum is the default value. */
	public static $PRIVACY_DEFAULT = 0;

	/** The forum is private. */
	public static $PRIVACY_PRIVATE = 1;

	/** The forum is public but the post counts are hidden. */
	public static $PRIVACY_PUBLIC_HIDE_POST_COUNTS = 2;

	/** The forum is public and the post counts are shown. */
	public static $PRIVACY_PUBLIC_SHOW_POST_COUNTS = 3;

	/** Create a new {@link Forum}.
	 *
	 *	@param	int $id 			The ID of the forum within vBulletin
	 *	@param	string $name		The forum name.
	 *	@param	string $description The description of what the forum is for.
	 *	@param	mixed $parent		The parent object for this forum. It could
	 *								be either a {@link Site}, a {@link
	 *								Category} or a {@link Forum}.
	 *	@param	int $status 		A bitmask describing many properties of the
	 *								forum. See the STATUS_ constants for all
	 *								the status bits that may be set.
	 *	@param	int $privacy		Whether the forum is private or not and if
	 *								so, whether or not the posts count towards
	 *								a post count.
	 *	@param	array $threads		An array where the keys are the date of
	 *								last update and the values are {@link
	 *								Thread} objects.
	 *	@param	array $subForums	An array of {@link Forum}s which are
	 *								children of this forum.
	 *	@param	string $link		If the forum is simply a link through to
	 *								somewhere else, this is the URL.
	 *	@param	string $password	The password for the forum, if any.
	 *	@param	int $daysToDisplay	The number of days worth of posts to
	 *								display.
	 */
	public function __construct($id, $name = "", $description = "", $parent = NULL, $status = 0, $privacy = 0, $threads = array(), $subForums = array(), $link = "", $password = NULL, $daysToDisplay = -1) {
		$this->data['id'] = $id;
		$this->type['id'] = "int";
		$this->data['name'] = $name;
		$this->type['name'] = "string";
		$this->data['description'] = $description;
		$this->type['description'] = "string";
		$this->data['parent'] = $parent;
		$this->type['parent'] = "mixed";
		$this->data['status'] = $status;
		$this->type['status'] = "int";
		$this->data['privacy'] = $privacy;
		$this->type['privacy'] = "int";
		$this->data['threads'] = $threads;
		$this->type['threads'] = "array";
		$this->data['subForums'] = $subForums;
		$this->type['subForums'] = "array";
		$this->data['link'] = $link;
		$this->type['link'] = "string";
		$this->data['password'] = $password;
		$this->type['password'] = "string";
		$this->data['daysToDisplay'] = $daysToDisplay;
		$this->type['daysToDisplay'] = "int";
	}

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array(
			"name" => "",
			"description" => "",
			"parent" => NULL,
			"status" => 0,
			"privacy" => 0,
			"threads" => array(),
			"subForums" => array(),
			"link" => "",
			"password" => NULL,
			"daysToDisplay" => -1,
		);
	}
	
	/** Get the names of the required constructor parameters in the order in
	 *	which they must appear in the constructor.
	 *
	 *	@return	array	An array containing the names of the properties which
	 *					must appear in order in the constructor parameters.
	 */
	public static function requiredConstructorParams() {
		return array(
			"id",
		);
	}
}
?>
