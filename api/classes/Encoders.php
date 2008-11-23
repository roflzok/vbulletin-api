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
 * A factory for getting the correct encoder/decoder for a given request. The 
 * encoder/decoder will implement {@link Encoder}.
 *
 * @package vBulletinAPI
 */
class Encoders {
	/** The pool of encoders to choose from. */
	private static $encoders = array();

	/**
	 * Use the contents of a request and the Content-Type MIME type that went with 
	 * it to select the most appropriate encoder/decoder to handle it.
	 * 
	 * @param	string $request_contents	The raw text which has been sent to 
	 *										the API end point script.
	 * @return	Encoder						An encoder/decoder which will 
	 *										handler the content. If a suitable 
	 *										one cannot be found, then an 
	 *										exception will be thrown.
	 */
	public static function getEncoder($request_contents) {
		// Load the available encoders
		self::loadEncoders(dirname(__FILE__) . "/encoders");

		// Find out the content type
		$content_type = NULL;
		if (array_key_exists('CONTENT_TYPE', $_SERVER)) {
			$content_type = $_SERVER['CONTENT_TYPE'];
		}

		// Find the encoder that can handle the input and return it.
		//
		// Fail and throw an exception unless exactly one encoder matches.
		$encoder = NULL;
		foreach (self::$encoders as $enc) {
			if ($enc->canDecode($content_type, $request_contents)) {
				if ($encoder == NULL) {
					$encoder = $enc;
				} else {
					throw new Exception("Multiple valid encoders found, can't choose");
				}
			}
		}
		if ($encoder != NULL) {
			return $encoder;
		} else {
			throw new Exception("No valid encoder found");
		}
	}

	/**	Get all of the encoders that are available.
	 *
	 *	@return	An array of all the encoders.
	 */
	public static function getAllEncoders() {
		// Load the available encoders
		self::loadEncoders(dirname(__FILE__) . "/encoders");

		return self::$encoders;
	}

	
	/** Load all of the available encoders.
	 *
	 *	@param	string $encoders_dir	The full path to the directory which 
	 *									contains the encoder class files.
	 */
	private static function loadEncoders($encoders_dir) {
		if (self::$encoders == NULL) {
			$suffix = ".php";
			if ($dirhandle = opendir($encoders_dir)) {
				while (($file = readdir($dirhandle)) !== FALSE) {
					if (substr($file, -(strlen($suffix))) === $suffix) {
						$encoder_name = substr($file, 0, strlen($file) - strlen($suffix));
						if (include_once("$encoders_dir/$file")) {
							try {
								$encoder = new $encoder_name();
								if ($encoder instanceof Encoder) {
									self::$encoders[$encoder_name] = new $encoder_name();
								}
							} catch (Exception $e) {
								// Ignore, it's not going to go into the encoder list
							}
						}
					}
				}
				closedir($dirhandle);
			}
			ksort(self::$encoders);
			reset(self::$encoders);
			self::$encoders = array_values(self::$encoders);
		}
	}
}

?>
