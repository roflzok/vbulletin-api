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

/**	The root of all the model classes. These classes are only a thin wrapper 
 *	around some basic associative arrays, the only reason they exist is to 
 *	provide some regular patterns for different frequently-used chunks of data.
 *
 *	@package vBulletinAPI
 */
abstract class DataObject {
	/** The backing store for all the properties. */
	protected $data = array();

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to fetch.
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		throw new Exception("No such property $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to set.
	 *	@param	mixed $value	The value for the property.
	 */
	public function __set($name, $value) {
		if (array_key_exists($name, $this->data)) {
			$this->data[$name] = $value;
		}
		throw new Exception("No such property $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to check.
	 */
	public function __isset($name) {
		if (array_key_exists($name, $this->data)) {
			return isset($this->data[$name]);
		}
		throw new Exception("No such property $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to unset.
	 */
	public function __unset($name) {
		if (array_key_exists($name, $this->data)) {
			$this->data[$name] = NULL;
		}
		throw new Exception("No such property $name");
	}

	/** Export this object as an array with all the properties as elements and 
	 *	converting everything within that array to an array or a scalar.
	 *
	 *	Objects found in the array will be handled in the following way: 
	 *	instances of {@link DataObject} will be exported using {@link 
	 *	DataObject::export()}, instances of DateTime will be passed through 
	 *	as-is, and all other objects will cause an exception to be thrown.
	 *
	 *	@return	array	An array as described above.
	 */
	public function export() {
		return self::exportArray($this->data);
	}

	/**	Export an array just like {@link DataObject::export()}. 
	 *
	 *	@param	array $array	The array to export.
	 *	@return	array			As described in {@link DataObject::export()}.
	 */
	private static function exportArray($array) {
		$ret_val = array();
		foreach ($array as $key => $value) {
			if (is_object($value)) {
				if ($value instanceof DataObject) {
					$ret_val[$key] = $value->export();
				} else if ($value instanceof DateTime) {
					$ret_val[$key] = $value;
				} else {
					throw new Exception("Un-exportable object encountered");
				}
			} else if (is_array($value)) {
				$ret_val[$key] = self::exportArray($value);
			} else {
				$ret_val[$key] = $value;
			}
		}
		ksort($ret_val);
		reset($ret_val);
		return $ret_val;
	}
}

?>
