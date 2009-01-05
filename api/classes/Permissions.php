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

/**
 * Wrapper for querying the various permissions enforced in vBulletin.
 *
 * @package vBulletinAPI
 */
class Permissions {
	/** The specified user can see deleted posts in the given thread.
	 *
	 *	@param	User $user		The user.
	 *	@param	Thread $thread	The thread.
	 *	@return	boolean			True if the user can see deleted posts in that 
	 *							thread, false otherwise.
	 */
	public static function canSeeDeletedPosts(User $user, Thread $thread) {
		return FALSE; // TODO
	}
	
	/** The specified user can see deleted threads in the given forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see deleted threads in that 
	 *							forum, false otherwise.
	 */
	public static function canSeeDeletedThreads(User $user, Forum $forum) {
		return FALSE; // TODO
	}
	
	/** The specified user can see hidden posts in the given thread.
	 *
	 *	@param	User $user		The user.
	 *	@param	Thread $thread	The thread.
	 *	@return	boolean			True if the user can see hidden posts in that 
	 *							thread, false otherwise.
	 */
	public static function canSeeHiddenPosts(User $user, Thread $thread) {
		return FALSE; // TODO
	}
	
	/** The specified user can see hidden threads in the given forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see hidden threads in that 
	 *							forum, false otherwise.
	 */
	public static function canSeeHiddenThreads(User $user, Forum $forum) {
		return FALSE; // TODO
	}
	
	/** The specified user can see infractions in the given forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see infractions in that 
	 *							forum, false otherwise.
	 */
	public static function canSeeInfractions(User $user, Forum $forum) {
		return FALSE; // TODO
	}

	/** The specified user can see IP addresses in the given forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see IP addresses in that 
	 *							forum, false otherwise.
	 */
	public static function canSeeIpAddresses(User $user, Forum $forum) {
		return FALSE; // TODO
	}
	
	/** The specified user can see threads made by other posters in the given 
	 *	forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see threads in that forum 
	 *							made by other posters, false otherwise.
	 */
	public static function canSeeOtherPostersThreads(User $user, Forum $forum) {
		return TRUE; // TODO
	}

	/** The specified user can see links to reported post threads in the given 
	 *	forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can see links to reported post 
	 *							threads in the given forum.
	 */
	public static function canSeeReportThreads(User $user, Forum $forum) {
		return FALSE; // TODO
	}

	/** The specified user can view the given category.
	 *
	 *	@param	User $user			The user.
	 *	@param	Category $category	The category.
	 *	@return	boolean				True if the user can view the category,
	 *								false otherwise.
	 */
	public static function canViewCategory(User $user, Category $category) {
		return TRUE; // TODO
	}

	/** The specified user can view the given forum.
	 *
	 *	@param	User $user		The user.
	 *	@param	Forum $forum	The forum.
	 *	@return	boolean			True if the user can view the forum, false 
	 *							otherwise.
	 */
	public static function canViewForum(User $user, Forum $forum) {
		return TRUE; // TODO
	}
}

?>
