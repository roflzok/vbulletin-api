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
 *	@package	vBulletinAPI
 *	@filesource
 */

require_once("base/Encoder.php");

/** An encoder/decoder for XML-RPC messages.
 *
 *	@package vBulletinAPI
 */
class XMLRPC
extends Encoder
{
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
		if (preg_match("#<methodCall>.*</methodCall>\s*$#s", $request_data)) {
			return TRUE;
		}

		return FALSE;
	}

	/** Decode an XML-RPC encoded message and turn it into a PHP structure.
	 *
	 *	@param	string $encoded_data	A string which is a full XML-RPC 
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
	public function decode($encoded_data) {
		$xml_data = simplexml_load_string($encoded_data);

		$action_name = $this->decodeString($xml_data->methodName[0]);
		$params = array();
		foreach ($xml_data->params->param as $param) {
			$params[] = $this->decodeValue($param->value);
		}

		return array(
			'action' => $action_name,
			'params' => $params
		);
	}

	/** Decode anything that can appear in a <value> tag.
	 *
	 *	@param	SimpleXMLElement $value	The <value> tag as a SimpleXMLElement.
	 *	@return	array					A PHP structure mirroring the <value>'s 
	 *									structure. See the various decode 
	 *									methods for descriptions of how each 
	 *									XML-RPC structure is converted into a 
	 *									PHP one.
	 */
	private function decodeValue($value) {
		$children = $value->children();
		if (count($children) != 1) {
			throw new Exception("<value> elements must only have one child element");
		}

		$child = $children[0];
		$child_type = $child->getName();
		switch ($child_type) {
			case "array":
				return $this->decodeArray($child);
			case "base64":
				return $this->decodeBase64($child);
			case "boolean":
				return $this->decodeBoolean($child);
			case "dateTime.iso8601":
				return $this->decodeDateTimeIso8601($child);
			case "double":
				return $this->decodeDouble($child);
			case "i4":
				return $this->decodeInt($child);
			case "int":
				return $this->decodeInt($child);
			case "nil":
				return NULL;
			case "string":
				return $this->decodeString($child);
			case "struct":
				return $this->decodeStruct($child);
		}

		throw new Exception("Unknown child of <value>: <$child_type>");
	}

	/** Decode an XML-RPC <array> element.
	 *
	 *	@param	SimpleXMLElement $array	The <array> element as a 
	 *									SimpleXMLElement.
	 *	@return	array					An array containing the values of the 
	 *									<array>
	 */
	private function decodeArray($array) {
		$ret_val = array();
		foreach ($array->value as $value) {
			$ret_val[] = $this->decodeValue($value);
		}
		return $ret_val;
	}

	/** Decode an XML-RPC <base64> element.
	 *
	 *	@param	SimpleXMLElement $base64	The <base64> element as a 
	 *										SimpleXMLElement.
	 *	@return	string						A string containing the decoded 
	 *										contents of the base64. Be careful, 
	 *										this resulting string may not be 
	 *										human readable text, it could be 
	 *										binary data.
	 */
	private function decodeBase64($base64) {
		return base64_decode($this->decodeString($base64));
	}

	/**	Decode an XML-RPC <boolean> element.
	 *
	 *	@param	SimpleXMLElement $boolean	The <boolean> element as a 
	 *										SimpleXMLElement.
	 *	@return	boolean						True if the <boolean> was a true 
	 *										value, false otherwise.
	 */
	private function decodeBoolean($boolean) {
		return ($this->decodeInt($boolean)) ? TRUE : FALSE;
	}

	/** Decode an XML-RPC <dateTime.iso8601> element.
	 *
	 *	@param	SimpleXMLElement $date_time	The <dateTime.iso8601> element as a 
	 *										SimpleXMLElement.
	 *	@return	DateTime					A DateTime object representing the 
	 *										date and time in the 
	 *										<dateTime.iso8601> element. The 
	 *										supplied time will be assumed to be 
	 *										in UTC.
	 */
	private function decodeDateTimeIso8601($date_time) {
		return date_create(
			$this->decodeString($date_time), new DateTimeZone('UTC')
		);
	}

	/**	Decode an XML-RPC <double> element.
	 *
	 *	@param	SimpleXMLElement $double	The <double> element as a 
	 *										SimpleXMLElement.
	 *	@return	double						A PHP double value with the same 
	 *										value as sepecified in the <double>.
	 */
	private function decodeDouble($double) {
		return (double)($this->decodeString($double));
	}

	/**	Decode an XML-RPC <int> or <i4> element.
	 *
	 *	@param	SimpleXMLElement $int	The <int> or <i4> element as a 
	 *									SimpleXMLElement.
	 *	@return	int						A PHP int value with the same 
	 *									value as sepecified in the <int> or <i4>.
	 */
	private function decodeInt($int) {
		return (int)($this->decodeString($int));
	}

	/** Decode an XML-RPC <string>.
	 *
	 *	@param	SimpleXMLElement $string	The <string> as a SimpleXMLElement.
	 *	@return	string						The contents of the <string>.
	 */
	private function decodeString($string) {
		return (string)$string;
	}

	/** Decode an XML-RPC <struct>.
	 *
	 *	@param	SimpleXMLElement $struct	The <struct> as a SimpleXMLElement.
	 *	@return	array						An associative array where each 
	 *										element represents a <member> in 
	 *										the <struct> and the keys are the 
	 *										<name> and the values are the 
	 *										<value>.
	 */
	private function decodeStruct($struct) {
		$ret_val = array();
		foreach ($struct->member as $member) {
			$name = $member->name . "";
			$value = $this->decodeValue($member->value);
			$ret_val[$name] = $value;
		}
		return $ret_val;
	}

	/** Encode an arbitrary PHP value as an XML-RPC <methodResponse>.
	 *
	 *	@param	mixed $raw_data	The PHP value to encode.
	 *	@return	string			The XML for the <methodResponse>.
	 */
	public function encode($raw_data) {
		$xml = new SimpleXMLElement("<methodResponse />");
		$params = $xml->addChild("params");
		$param = $params->addChild("param");
		$value = $param->addChild("value");
		$this->encodeValue($value, $raw_data);
		return $xml->asXML();
	}

	/** Encode a raw PHP value in XML-RPC syntax and insert it into the 
	 *	SimpleXMLElement provided.
	 *
	 *	@param	SimpleXMLElement $xml	The element to add the result to.
	 *	@param	mixed $value			The PHP value to encode.
	 */
	private function encodeValue($xml, $value) {
		if ($value === NULL) {
			$xml->addChild("nil");
		} else if (is_array($value)) {
			// Detect the difference between an <array> and a <struct>
			$is_hash = FALSE;
			for ($i = 0; $i < count($value); $i++) {
				if (!array_key_exists($i, $value)) {
					$is_hash = TRUE;
					break;
				}
			}

			if ($is_hash) {
				$this->encodeStruct($xml, $value);
			} else {
				$this->encodeArray($xml, $value);
			}
		} else if (is_bool($value)) {
			$xml->addChild("boolean", $value ? 1 : 0);
		} else if (is_double($value)) {
			$xml->addChild("double", $value);
		} else if (is_integer($value)) {
			$xml->addChild("int", $value);
		} else if (is_object($value)) {
			$this->encodeObject($xml, $value);
		} else if (is_string($value)) {
			$xml->addChild("string", $value);
		} else {
			throw new Exception("Unknown type for encoding: " . gettype($value));
		}
	}

	/** Encode a PHP array in XML-RPC syntax and insert it into the 
	 *	SimpleXMLElement provided.
	 *
	 *	@param	SimpleXMLElement $xml	The element to add the result to.
	 *	@param	array $value			The PHP array to encode.
	 */
	private function encodeArray($xml, $value) {
		$array = $xml->addChild("array");
		$data = $array->addChild("data");
		foreach ($value as $entry) {
			$val = $data->addChild("value");
			$this->encodeValue($val, $entry);
		}
	}

	/** Encode a PHP object in XML-RPC syntax and insert it into the 
	 *	SimpleXMLElement provided.
	 *
	 *	@param	SimpleXMLElement $xml	The element to add the result to.
	 *	@param	object $value			The PHP object to encode.
	 */
	private function encodeObject($xml, $value) {
		if ($value instanceof DataObject) {
			$this->encodeValue($xml, $value->export());
		} else if ($value instanceof DateTime) {
			$xml->addChild("dateTime.iso8601", gmstrftime("%Y%m%dT%T", $value->format('U')));
		} else {
			throw new Exception("Cannot encode object of type '" . get_class($value) . "'");
		}
	}

	/** Encode a PHP associative array in XML-RPC syntax and insert it into the 
	 *	SimpleXMLElement provided.
	 *
	 *	@param	SimpleXMLElement $xml	The element to add the result to.
	 *	@param	array $value			The PHP associative array to encode.
	 */
	private function encodeStruct($xml, $value) {
		$struct = $xml->addChild("struct");
		foreach ($value as $k => $v) {
			$member = $struct->addChild("member");
			$member->addChild("name", $k);
			$val = $member->addChild("value");
			$this->encodeValue($val, $v);
		}
	}

	/**	Encode an exception to represent a fault to be sent to the client.
	 *
	 *	@param	Exceptione $e	The Exception to encode.
	 *	@return	string			A string which is an encoded representation of 
	 *							the exception.
	 *	@todo					Currently the fault code field is always 0, 
	 *							perhaps we should differentiate the errors by 
	 *							fault code.
	 */
	public function encodeException(Exception $e) {
		$fault_code = 0;
		$fault_string = htmlspecialchars($e->getMessage());
		return <<<XML
<?xml version="1.0" ?>
<methodResponse>
	<fault>
		<value>
			<struct>
				<member>
					<name>faultCode</name>
					<value><int>$fault_code</int></value>
				</member>
				<member>
					<name>faultString</name>
					<value><string>$fault_string</string></value>
				</member>
			</struct>
		</value>
	</fault>
</methodResponse>

XML;
	}

	/**	Handle the system.* methods for XML-RPC.
	 *
	 *	@param	array $request	A structure that came out of {@link decode()}.
	 *	@return					A structure that can be passed to {@link 
	 *							encode()}.
	 */
	public function execute($request) {
		if ($request['action'] == "system.listMethods") {
			return $this->executeListMethods();
		} else if ($request['action'] == "system.methodSignature") {
			if (count($request['params']) == 1) {
				return $this->executeMethodSignature($request['params'][0]);
			} else {
				throw new Exception("Bad parameters for system.methodSignature");
			}
		} else if ($request['action'] == "system.methodHelp") {
			if (count($request['params']) == 1) {
				return $this->executeMethodHelp($request['params'][0]);
			} else {
				throw new Exception("Bad parameters for system.methodHelp");
			}
		} else {
			parent::execute($request);
		}
	}

	/** Implement the system.listMethods method. Lists all the methods 
	 *	available to the end-user.
	 *
	 *	@return	array	An array of strings which are method names.
	 */
	private function executeListMethods() {
		$actions = Actions::getAllActions();
		$action_names = array(
			"system.listMethods",
			"system.methodSignature",
			"system.methodHelp"
		);
		foreach ($actions as $action) {
			$action_names[] = $action->getName();
		}
		sort($action_names);
		reset($action_names);
		return $action_names;
	}

	/** Implement the system.methodSignature method. Lists all the available 
	 *	signatures for a method.
	 *
	 *	@param	string $method_name	The name of the method to query for.
	 *	@return	array				An array of method signatures for the given 
	 *								method. Each element of that array is an 
	 *								array where the first element is the return 
	 *								type and the remainder of the elements are 
	 *								the parameters to the method.
	 */
	private function executeMethodSignature($method_name) {
		switch ($method_name) {
			case "system.listMethods":
				return array(array("array"));
			case "system.methodSignature":
				return array(array("array", "string"));
			case "system.methodHelp":
				return array(array("array", "string"));
			default: 
				$action = Actions::getAction($method_name);
				$return_type = array_shift(array_keys($action->returnSpec()));
				return array(array($this->phpTypeToXMLRPCType($return_type), "struct"));
		}
	}

	/** Implement the system.methodHelp method. This gives a short description 
	 *	of what each method does.
	 *
	 *	@param	string $method_name	The name of the method to query for.
	 *	@return	string				A string containing a description of the 
	 *								method.
	 */
	private function executeMethodHelp($method_name) {
		switch ($method_name) {
			case "system.listMethods":
			case "system.methodSignature":
			case "system.methodHelp":
				return "System method, see http://xmlrpc-c.sourceforge.net/introspection.html for details.";
			default: 
				$action = Actions::getAction($method_name);
				return $action->getDescription();
		}
	}

	/**	Given the name of a PHP type, return the XML-RPC equivalent.
	 *
	 *	@param	string $type_name	The name of the PHP type.
	 *	@return	string				The name of the corresponding XML-RPC type.
	 */
	private function phpTypeToXMLRPCType($type_name) {
		switch ($type_name) {
			case "boolean":
			case "double":
			case "string":
				return $type_name;
			case "integer":
				return "int";
			case "array":
				return "array";
			default: // Object types
				return "struct";
		}
	}

	/** Get the MIME types most likely to be used with XML-RPC.
	 *
	 *	The first of these is text/xml which is the correct MIME type according 
	 *	to the XML-RPC spec.
	 *
	 *	@return	array	An array of strings, each of which is a MIME type
	 */
	protected function getMimeTypes() {
		return array("text/xml", "application/xml", "application/xml+rpc");
	}

	/** Describe what this encoder does encode/decode.
	 *
	 *	@return	string	A description of this encoder.
	 */
	public function getDescription() {
		return <<<DESC
Encodes/decodes XML-RPC messages. See the <a 
href="http://www.xmlrpc.com/spec">XML-RPC spec</a>
for details. Two extensions to the spec, the <a 
href="http://ontosys.com/xml-rpc/extensions.php">&lt;nil /&gt; extension</a> 
and the <a 
href="http://xmlrpc-c.sourceforge.net/introspection.html">introspection 
extension</a> are implemented.
DESC;
	}
}

?>
