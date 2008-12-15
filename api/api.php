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
 * The end-point for handling requests.
 * 
 * @package		vBulletinAPI
 * @filesource
 */

/** The Action factory. */
require_once("classes/Actions.php");
/** The Encoder factory. */
require_once("classes/Encoders.php");

// Get the POST data
$request = file_get_contents('php://input');

// Get the encoder/decoder which will decode the request and encode the 
// response
$encoder = NULL;
try {
	$encoder = Encoders::getEncoder($request);
} catch (Exception $e) {
	header("HTTP/1.0 400 Bad Request");
	print $e->getMessage();
	exit(0);
}

try {
	// Decode the input
	$request = $encoder->decode($request);

	// Find the appropriate action
	$action = NULL;
	try {
		$action = Actions::getAction($request['action']);
	} catch (Exception $e) {
		// Try to get the encoder to handle it, it could be an 
		// encoding-specific action.
		$response = $encoder->execute($request);
		$response = $encoder->encode($response);
		output_and_exit($encoder->getMimeType(), $response);
	}

	// Fiddle the parameters to fit the way we do things
	$params = $request['params'];
	if (count($params) == 0) {
		$params = array();
	} else if (count($params) == 1 && is_array($params[0])) {
		$params = $params[0];
	} else {
		throw new Exception("There should be at most one parameter which should be an associative array");
	}
	
	// Execute the action and return the result
	$response = $action->execute($params);
	
	// Encode the response
	$response = $encoder->encode($response);

	// Output the result
	output_and_exit($encoder->getMimeType(), $response);
} catch (Exception $e) {
	output_and_exit($encoder->getMimeType(), $encoder->encodeException($e));
}

/**
 * Output some content and terminate the script.
 *
 * @param	string $mime_type	The MIME type to send to the client in the HTTP 
 *								Content-Type header
 * @param	string $content		The content of the response to send to the client.
 */
function output_and_exit($mime_type, $content) {
	header("Content-Type: $mime_type");
	header("Content-Length: " . strlen($content));
	print $content;
	exit(0);
}

?>
