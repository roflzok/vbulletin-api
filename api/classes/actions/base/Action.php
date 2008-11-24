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
 *	@filesource
 *	@package vBulletinAPI
 */

/**	A base action to be extended for each method exposed in the API.
 *
 *	@package vBulletinAPI
 */
abstract class Action
{
	/**	Specify the arguments to the action.
	 *
	 *	The associative array returned has keys which are the names of the 
	 *	arguments and the values to each key will be an associative array 
	 *	describing the properties of that argument. Allowable property names 
	 *	are "description" (mandatory, value must be a string),
	 *	"validationPattern" (optional, value must be a valid regular
	 *	expression), "validationFunction" (optional, value must be either a
	 *	function name or a two-element array [classname, functionName]. The
	 *	function must take one argument, of any type, and must return a boolean;
	 *	true if the argument is valid, false otherwise.  Any exception thrown
	 *	will be interpreted as if the function returned false) and
	 *	"defaultValue" (optional, value may be any type).
	 *
	 *	@return	array	An associative array which describes the arguments to 
	 *					the action.
	 */
	protected abstract function argumentSpec();

	/** Specify the return value of the action.
	 *
	 *	@return	array	An associative array where the key is the PHP type of 
	 *					the return and the value is a string describing it 
	 *					further.
	 */
	public abstract function returnSpec();

	/** Describe the action briefly.
	 *
	 *	@return	string	A description of what the action does.
	 */
	public abstract function getDescription();

	/** Actually perform the action. Redefine this to implement the action 
	 *	itself.
	 *
	 *	@param	array $arguments	The arguments to the action.
	 *	@return	mixed				The return value of the action.
	 */
	protected abstract function doIt($arguments);

	/** Get the name of the action.
	 *
	 *	@return	string	The name of this action.
	 */
	public final function getName() {
		$action_name = get_class($this);
		$action_name = preg_replace("/Action$/", "", $action_name);
		$action_name[0] = strtolower($action_name[0]);
		return $action_name;
	}

	/** The public interface to executing the action.
	 *
	 *	@param	array $arguments	The arguments to the action.
	 *	@return	mixed				The return value of the action.
	 */
	public final function execute($arguments) {
		// Fetch the argument specification and defaults
		$argument_spec = $this->argumentSpec();

		// Consider null arguments to be empty arguments
		if ($arguments == NULL) {
			$arguments = array();
		}

		// Make sure the arguments are presented as an array
		if (!is_array($arguments)) {
			throw new Exception("Arguments to an action must be an associative array");
		}

		// Check that all of the arguments are present and correct
		foreach ($argument_spec as $argument_name => $argument_properties) {
			// Check present, if not use any available default
			if (!array_key_exists($argument_name, $arguments)) {
				if (array_key_exists('defaultValue', $argument_properties)) {
					$arguments[$argument_name] = $argument_properties['defaultValue'];
				} else {
					throw new Exception("Missing required argument '$argument_name'");
				}
			}

			// Ensure that the argument matches the validation regex
			if (array_key_exists('validationPattern', $argument_properties)) {
				if (!preg_match($argument_properties['validationPattern'], $arguments[$argument_name])) {
					throw new Exception("Invalid argument value for '$argument_name'");
				}
			}

			// If a validation function exists for this argument, run it on the argument now.
			if (array_key_exists('validationFunction', $argument_properties)) {
				$valid = FALSE;
				try {
					$valid = call_user_func(
						$argument_properties['validationFunction'],
						$arguments[$argument_name]
					);
				} catch (Exception $e) {
					// Don't care what the exception was, this is going down as a missed validation
					$valid = FALSE;
				}
				if (!$valid) {
					throw new Exception("Invalid argument value for '$argument_name'");
				}
			}
		}

		// Check and reject excess arguments
		foreach ($arguments as $name => $value) {
			if (!array_key_exists($name, $argument_spec)) {
				throw new Exception("Surplus argument '$name'");
			}
		}

		// Do the action and return the result
		return $this->doIt($arguments);
	}

	/** Describe this action in HTML form.
	 *
	 *	@return	string	A <div> containing a description of the action.
	 */
	public final function toHTML() {
		$action_name = $this->getName();

		$arguments = $this->argumentSpec();
		ksort($arguments);
		reset($arguments);

		$return_spec = $this->returnSpec();
		if (count($return_spec) > 0) {
			$return_type = array_shift(array_keys($return_spec));
			$return_desc = array_shift(array_values($return_spec));
		} else {
			$return_type = "void";
			$return_desc = "This action returns nothing";
		}

		$short_form = "$return_type $action_name({";
		$short_form_args = array();
		foreach ($arguments as $argument_name => $arg_spec) {
			$short_form_args[] = "$argument_name => ...";
		}
		$short_form .= join(", ", $short_form_args) . "})";

		$description = $this->getDescription();

		if ($arguments) {
			$args = "";
			foreach ($arguments as $argument_name => $arg_spec) {
				$argument_default = $arg_spec['defaultValue'];
				$argument_description = $arg_spec['description'];
				$args .= <<<HTML
					<tr>
						<td>$argument_name</td>
						<td>$argument_default</td>
						<td>$argument_description</td>
					</tr>
HTML;
			}
			$arguments = <<<HTML
				<table>
					<tr>
						<th>Argument Name</th>
						<th>Default Value</th>
						<th>Description</th>
					</tr>
					$args
				</table>
HTML;
		} else {
			$arguments = "This method accepts no arguments";
		}

		return <<<HTML
			<h3>$action_name</h3>
			<div class="description">
				$short_form - $description
				$arguments
				<table>
					<tr><th>Return type</th><th>Description</th></tr>
					<tr><td>$return_type</td><td>$return_desc</td></tr>
				</table>	
			</div>
HTML;
	}
}

?>
