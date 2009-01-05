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

/** Base encoder class. */
require_once("base/Encoder.php");

/** An encoder/decoder for JSON-RPC messages.
 *
 *	@package vBulletinAPI
 */
class JSONRPC
extends Encoder
{
	/** The ID passed in the last request.
	 *
	 *	On a call to {@link decode()} this is set to the id of the request (if
	 *	present) and to NULL otherwise. On a call to {@link encode()}, if this 
	 *	is not null the ID will be added to the response object and this will
	 *	be reset to NULL.
	 */
	private static $last_request_id = NULL;
	
	/** Decide whether this encoder can be used to decode a message.
	 *
	 *	@param	string $content_type	The HTTP Content-Type header which was 
	 *									sent with the request.
	 *	@param	string $request_data	The full text of the request made.
	 *	@return	boolean					True if this can decode the given 
	 *									input, false otherwise.
	 */
	public function canDecode($content_type, $request_data) {
		if (parent::canDecode($content_type, $request_data)) {
			return TRUE;
		}

		// Try to guess by looking at the content
		if (preg_match("/\"jsonrpc\"\s*:\s*\"2.0\"\s*,/", $request_data)) {
			return TRUE;
		}

		return FALSE;
	}

	/** Decode an JSON-RPC encoded message and turn it into a PHP structure.
	 *
	 *	@param	string $encoded_data	A string which is a full JSON-RPC 
	 *									message.
	 *	@return	array					An associative array with two keys,
	 *									"action" and "params". The value 
	 *									associated with "action" is a string 
	 *									and is the name of the action to 
	 *									execute. The value associated with 
	 *									"params" is an associative array where 
	 *									the keys are the names of the 
	 *									parameters to the action and their 
	 *									values are the values of those 
	 *									parameters.
	 */
	protected function decodeRequest($encoded_data) {
		$decoded_data = json_decode($encoded_data, TRUE);
		if ($decoded_data === NULL) {
			throw new Exception("Malformed JSON in input");
		}

		// Ensure that the required elements are present
		foreach (array("jsonrpc", "method") as $required_field) {
			if (!array_key_exists($required_field, $decoded_data)) {
				throw new Exception("Missing required field \"$required_field\"");
			}
		}

		// We only speak JSON-RPC 2.0
		if (!($decoded_data['jsonrpc'] === "2.0")) {
			throw new Exception("Only JSON-RPC 2.0 is accepted");
		}

		// Record the ID if it has been sent
		if (array_key_exists("id", $decoded_data)) {
			if (is_integer($decoded_data['id'])) {
				self::$last_request_id = $decoded_data['id'];
			} else {
				throw new Exception("Malformed ID sent with the request");
			}
		}

		return array(
			'action' => $decoded_data['method'],
			'params' => $decoded_data['params']
		);
	}

	/** Encode an arbitrary PHP value as a JSON-RPC response.
	 *
	 *	@param	mixed $raw_data	The PHP value to encode.
	 *	@return	string			The JSON for the response
	 */
	public function encode($raw_data) {
		// Create the result structure.
		$ret_val = array(
			"jsonrpc" => "2.0",
			"result" => $this->encodeValue($raw_data)
		);

		// Add the id if there was one
		if (self::$last_request_id !== NULL) {
			$ret_val['id'] = self::$last_request_id;
			self::$last_request_id = NULL;
		}

		// Encode and return
		return json_encode($ret_val);
	}

	/** Traverse a PHP data structure and convert it into a combination of
	 *	arrays and scalars. 
	 *
	 *	@param	mixed $value	The data structure to encode.
	 *	@return	mixed			The value, with all objects flattened into 
	 *							arrays.
	 */
	private function encodeValue($value) {
		if ($value === NULL) {
			return NULL;
		} else if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->encodeValue($v);
			}
			return $value;
		} else if (is_object($value)) {
			if ($value instanceof DataObject) {
				return $this->encodeValue($value->export());
			} else if ($value instanceof DateTime) {
				return gmstrftime("%Y%m%dT%T", $value->format('U'));
			} else {
				throw new Exception("Cannot encode object of type " . get_class($value));
			}
		} else {
			return $value;
		}
	}

	/**	Encode an exception to represent a fault to be sent to the client.
	 *
	 *	@param	Exceptione $e	The Exception to encode.
	 *	@return	string			A string which is an encoded representation of 
	 *							the exception.
	 *	@todo					The code field is always 100, we should 
	 *							probably fix that.
	 */
	public function encodeException(Exception $e) {
		$message = $e->getMessage();
		$code = 100;
		return <<<JSON
{
	"jsonrpc": "2.0",
	"error": {
		"code": $code,
		"message": "$message",
	}
}

JSON;
	}

	/** Get the MIME types most likely to be used with XML-RPC.
	 *
	 *	The first of these is text/xml which is the correct MIME type according 
	 *	to the XML-RPC spec.
	 *
	 *	@return	array	An array of strings, each of which is a MIME type
	 */
	protected function getMimeTypes() {
		return array("application/json");
	}

	/** Describe what this encoder does encode/decode.
	 *
	 *	@return	string	A description of this encoder.
	 */
	public function getDescription() {
		return <<<DESC
			Encodes and decodes JSON-RPC messages. The version of JSON-RPC used
			is that of the <a
			href="http://groups.google.com/group/json-rpc/web/json-rpc-1-2-proposal">2.0
			Specification proposal</a>.
DESC;
	}
}

?>
