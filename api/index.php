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
 *	@package vBulletinAPI
 *	@filesource
 */

/** Actions factory. */
require_once("classes/Actions.php");
/** Encoders factory. */
require_once("classes/Encoders.php");

// Describe the actions
$actions_html = "";
foreach (Actions::getAllActions() as $action) {
	$actions_html .= $action->toHTML();
}

// Describe the encoders
$encoders_html = "";
foreach (Encoders::getAllEncoders() as $encoder) {
	$encoders_html .= $encoder->toHTML();
}

print <<<HTML
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>vBulletin API</title>
		<style type="text/css">
body {
	font-family:	Verdana, Helvetica, "sans-serif";
	font-size:		10pt;
}
table {
	margin-top:	1em;
	border-collapse:	collapse;
	border:	1px solid #888888;
}
td {
	border:	1px solid #888888;
	padding:			0.5em;
}
th {
	border:	1px solid #888888;
	padding:			0.5em;
}
h3 {
	margin-left:	2em;
}
.description {
	margin-left:	4em;
}
		</style>
	</head>

	<body>
		<h1>vBulletin API</h1>

		<h2>Actions</h2>
$actions_html

		<h2>Encoders</h2>
$encoders_html
	</body>
</html>
HTML;

?>
