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

/** Base data class. */
require_once("DataObject.php");

/** Represent all or part of a forum in a vBulletin system.
 *
 *	@property	int $id 		The ID of the forum within vBulletin
 *	@property	string $name		The forum name.
 *	@property	mixed $parent	The parent object for this forum. It could be
 *								either a {@link Site}, a {@link Category} or a
 *								{@link Forum}.
 *	@property	array $threads	An array where the keys are the date of last
 *								update and the values are {@link Thread}
 *								objects.
 *	@property	array $subForums	An array of {@link Forum}s which are
 *									children of this forum.
 *	@package	vBulletinAPI
 */
class Forum
extends DataObject
{
	/** Create a new {@link Forum}.
	 *
	 *	@param	int $id 		The ID of the forum within vBulletin
	 *	@param	string $name		The forum name.
	 *	@param	mixed $parent	The parent object for this forum. It could be
	 *							either a {@link Site}, a {@link Category} or a
	 *							{@link Forum}.
	 *	@param	array $threads	An array where the keys are the date of last
	 *							update and the values are {@link Thread}
	 *							objects.
	 *	@param	array $subForums	An array of {@link Forum}s which are
	 *								children of this forum.
	 */
	public function __construct($id, $name = "", $parent = NULL, $threads = array(), $subForums = array()) {
		$this->data['id'] = $id;
		$this->type['id'] = "int";
		$this->data['name'] = $name;
		$this->type['name'] = "string";
		$this->data['parent'] = $parent;
		$this->type['parent'] = "mixed";
		$this->data['threads'] = $threads;
		$this->type['threads'] = "array";
		$this->data['subForums'] = $subForums;
		$this->type['subForums'] = "array";
	}

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array(
			"name" => "",
			"parent" => NULL,
			"threads" => array(),
			"subForums" => array(),
		);
	}
}
?>
