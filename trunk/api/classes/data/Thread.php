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

/** Represent all or part of a thread in a vBulletin system.
 *
 *	@property	int $id 			The ID of the thread within vBulletin.
 *	@property	int $forumId		The ID of the forum the thread is in.
 *	@property	string $title		The thread title.
 *	@property	DateTime $timestamp The time of thread creation.
 *	@property	int $numPosts		The number of posts in the thread (NOT the
 *									number of posts being returned).
 *	@property	array $posts		An array where the keys are post numbers
 *									and the values are {@link Post} objects.
 *	@property	boolean $open		True if the thread is open, false
 *									otherwise.
 *	@package	vBulletinAPI
 */
class Thread
extends DataObject
{
	/** Create a new {@link Thread}.
	 *
	 *	@param	int $id 			The ID of the thread within vBulletin.
	 *	@param	int $forumId		The ID of the forum the thread is in.
	 *	@param	string $title		The thread title.
	 *	@param	DateTime $timestamp The time of thread creation.
	 *	@param	int $numPosts		The number of posts in the thread (NOT the
	 *								number of posts being returned).
	 *	@param	array $posts		An array where the keys are post numbers
	 *								and the values are {@link Post} objects.
	 *	@param	boolean $open		True if the thread is open, false
	 *								otherwise.
	 */
	public function __construct($id, $forumId, $title, DateTime $timestamp, $numPosts, $posts, $open = TRUE) {
		$this->data['id'] = $id;
		$this->data['forumId'] = $forumId;
		$this->data['title'] = $title;
		$this->data['timestamp'] = $timestamp;
		$this->data['numPosts'] = $numPosts;
		$this->data['posts'] = $posts;
		$this->data['open'] = $open;
	}

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array(
			"open" => TRUE,
		);
	}
}
?>
