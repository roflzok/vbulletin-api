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
/* XXX Autogenerated - do not edit XXX*/
/**	@package vBulletinAPI
 *	@filesource
 */

/** The base class. */
include_once("DataObject.php");

/** Represent a post in a vBulletin system.
 *
 *	@property	int $id 			The ID of the post within vBulletin.
 *	@property	int $threadId		The ID of the thread within vBulletin in
 *									which this post resides.
 *	@property	User $author		The author of the post.
 *	@property	DateTime $timestamp The time and date that the post was
 *									created.
 *	@property	string $text		The text of the post, in BBCode format.
 *	@property	string $title		The title of the post, if any.
 *	@package	vBulletinAPI
 */
class Post
extends DataObject
{
	/** Create a new {@link Post}.
	 *
	 *	@param	int $id 			The ID of the post within vBulletin.
	 *	@param	int $threadId		The ID of the thread within vBulletin in
	 *								which this post resides.
	 *	@param	User $author		The author of the post.
	 *	@param	DateTime $timestamp The time and date that the post was
	 *								created.
	 *	@param	string $text		The text of the post, in BBCode format.
	 *	@param	string $title		The title of the post, if any.
	 */
	public function __construct($id, $threadId, User $author, DateTime $timestamp, $text = "", $title = "") {
		$this->data['id'] = $id;
		$this->data['threadId'] = $threadId;
		$this->data['author'] = $author;
		$this->data['timestamp'] = $timestamp;
		$this->data['text'] = $text;
		$this->data['title'] = $title;
	}

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array(
			"text" => "",
			"title" => "",
		);
	}
}
?>