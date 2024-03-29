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

/**	The root of all the model classes. These classes are only a thin wrapper 
 *	around some basic associative arrays, the only reason they exist is to 
 *	provide some regular patterns for different frequently-used chunks of data.
 *
 *	@package vBulletinAPI
 */
abstract class DataObject {
	/** The backing store for all the properties. */
	protected $data = array();

	/** The types for each of the properties. */
	protected $type = array();

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to fetch.
	 */
	public function __get($name) {
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		throw new Exception("No such property: $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to set.
	 *	@param	mixed $value	The value for the property.
	 */
	public function __set($name, $value) {
		if (array_key_exists($name, $this->data)) {
			$this->data[$name] = $value;
			if (preg_match("/^[a-z]/", $this->type[$name]) && $this->type[$name] !== "mixed") {
				if (!settype($this->data[$name], $this->type[$name])) {
					throw new Exception("Can't coerce {$this->data[$name]} into a {$this->type[$name]}");
				}
			}
			return;
		}
		throw new Exception("No such property: $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to check.
	 */
	public function __isset($name) {
		if (array_key_exists($name, $this->data)) {
			return isset($this->data[$name]);
		}
		throw new Exception("No such property: $name");
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@param	string $name	The name of the property to unset.
	 */
	public function __unset($name) {
		if (array_key_exists($name, $this->data)) {
			$default_values = $this->defaultPropertyValues();
			if (array_key_exists($name, $default_values)) {
				$this->data[$name] = $default_values[$name];
			} else {
				$this->data[$name] = NULL;
			}
		} else {
			throw new Exception("No such property: $name");
		}
	}

	/**	Magic method, see PHPs documentation for this.
	 *
	 *	@return	string	A string form of this object.
	 */
	public function __toString() {
		$exported_form = $this->export();
		$class_name = $exported_form['__class'];
		unset($exported_form['__class']);
		foreach ($exported_form as $key => $value) {
			$exported_form[$key] = "$value";
		}
		return preg_replace("/^Array/", "$class_name Object", print_r($exported_form, TRUE));
	}

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array();
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
		$data_to_export = array();
		$defaults = $this->defaultPropertyValues();
		foreach ($this->data as $key => $value) {
			if (array_key_exists($key, $defaults)) {
				if ($value !== $defaults[$key]) {
					$data_to_export[$key] = $value;
				}
			} else {
				$data_to_export[$key] = $value;
			}
		}
		$data_to_export['__class'] = get_class($this);
		return self::exportArray($data_to_export);
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

	/** Include a {@link DataObject} class.
	 *
	 *	@param	string $class_name	The name of the class to include.
	 */
	public static function includeClass($class_name) {
		$class_file = dirname(__FILE__) . "/$class_name.php";
		if (file_exists($class_file)) {
			if (!require_once($class_file)) {
				throw new Exception("No such class '$class_file'");
			}
		} else {
			throw new Exception("No such class '$class_file'");
		}
	}
}

?>
