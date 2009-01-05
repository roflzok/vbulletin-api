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

/** DataObject class. */
require_once(dirname(dirname(dirname(__FILE__))) . "/data/DataObject.php");

/** The interface to which all encoder/decoders must conform to.
 *
 * @package vBulletinAPI
 */
abstract class Encoder {
	/** Encode a message.
	 *
	 *	@param	string $raw_data	A PHP value which must be a scalar type, an 
	 *								array, a DateTime object or a subclass of 
	 *								{@link DataObject}.
	 *	@return	string				A string which is an encoded representation 
	 *								of the raw data.
	 */
	public abstract function encode($raw_data);

	/**	Encode an exception to represent a fault to be sent to the client.
	 *
	 *	@param	Exceptione $e	The Exception to encode.
	 *	@return	string			A string which is an encoded representation of 
	 *							the exception.
	 */
	public abstract function encodeException(Exception $e);

	/** Decode an encoded message and turn it into a PHP structure.
	 *
	 *	@param	string $encoded_data	A string which is an encoded 
	 *									representation of the raw message.
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
	public final function decode($encoded_data) {
		$return_value = $this->decodeRequest($encoded_data);
		$return_value['params'] = self::decodeObjects($return_value['params']);
		return $return_value;
	}

	/** The method to override to actually implement the decoding.
	 *
	 *	@param	string $encoded_data	A string which is an encoded 
	 *									representation of the raw message.
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
	protected abstract function decodeRequest($encoded_data);

	/** Decode any objects found in a structure. The objects must be instances 
	 *	of classes which are subclasses of DataObject.
	 *
	 *	@param	mixed $structure	A PHP structure to be recursively 
	 *								processed.
	 *	@return	mixed				The input with the objects converted from 
	 *								array form into object form.
	 */
	private static function decodeObjects($structure) {
		if (is_array($structure)) {
			// Recurse down through all the items in the structure and 
			// decode them first.
			foreach ($structure as $key => $value) {
				$structure[$key] = self::decodeObjects($value);
			}

			// Now convert this to a class if appropriate
			if (array_key_exists('__class', $structure)) {
				// Get the special parameters
				$class_name = $structure['__class'];
				unset($structure['__class']);

				// Include the class
				DataObject::includeClass($class_name);
				
				// Find the required constructor parameters and make sure 
				// they're present
				$constructor_params = eval("return $class_name::requiredConstructorParams();");

				// Stringify the parameters which will be in the constructor
				$params = array();
				foreach ($constructor_params as $name) {
					if (!array_key_exists($name, $structure)) {
						throw new Exception("Property '$name' is required to construct objects of type '$class_name'");
					}

					$params[] = var_export($structure[$name], TRUE);
					unset($structure[$name]);
				}

				// Create the object
				$obj = eval("return new $class_name(" . join(", ", $params) . ");");
				
				// Set all the other properties of the object
				foreach ($structure as $key => $value) {
					$obj->$key = $value;
				}
				
				// Replace the structure with the new object.
				$structure = $obj;
			}
		}
		return $structure;
	}

	/** If the encoder wishes to handle a request itself rather than passing it 
	 * on to an action then it can elect to handle it here. An regular {@link 
	 * Action} is searched for first, so this method can only handle actions 
	 * which are not defined.
	 *
	 *	@param	array $request	A structure in the same form as is returned by 
	 *							{@link decode()}.
	 *	@return mixed			Some form of structure which can be passed to 
	 *							{@link encode()} before being sent to the 
	 *							client.
	 */
	public function execute($request) {
		throw new Exception("No such action '" . $request['action'] . "'");
	}

	/** Decide whether this encoder can be used to decode a message.
	 *
	 *	@param	string $content_type	The HTTP Content-Type header which was 
	 *									sent with the request.
	 *	@param	string $request_data	The full text of the request made.
	 *	@return	boolean					True if this can decode the given 
	 *									input, false otherwise.
	 */
	public function canDecode($content_type, $request_data) {
		foreach ($this->getMimeTypes() as $mime_type) {
			if ($content_type === $mime_type) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/** Get the MIME type which best describes the output of {@link encode}.
	 *
	 *	@return	string	A valid MIME type.
	 */
	public function getMimeType() {
		$mime_types = $this->getMimeTypes();
		return $mime_types[0];
	}

	/** Get the MIME types handled by this Encoder.
	 *
	 *	The first of these MIME types is the primary MIME type for the 
	 *	encoding.
	 *
	 *	@return	array	An array of strings, each of which is a valid MIME type 
	 *					for this encoding.
	 */
	protected abstract function getMimeTypes();

	/** Describe this encoder for the purposes of documentation
	 *
	 *	@return	string	A string with a short, high-level description of this 
	 *					encoder.
	 */
	public abstract function getDescription();

	/** Describe this encoder in HTML.
	 *
	 *	@return	string	Some HTML ddescribing this encoder.
	 */
	public function toHTML() {
		$encoder_name = get_class($this);
		$description = $this->getDescription();
		$input_encodings = join(", ", $this->getMimeTypes());
		$output_encoding = $this->getMimeType();
		return <<<HTML
<h3>$encoder_name</h3>
<div class="description">
	<div>$description</div>
	<table>
		<tr>
			<th>Accepted MIME types for input</th>
			<td>$input_encodings</td>
		</tr>
		<tr>
			<th>MIME type for output</th>
			<td>$output_encoding</td>
		</tr>
	</table>
</div>
HTML;
	}
}

?>
