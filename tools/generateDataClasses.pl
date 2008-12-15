#!/usr/bin/perl
# __BEGIN_COPYRIGHT__
# Copyright (c) 2008, Conor McDermottroe
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without 
# modification, are permitted provided that the following conditions are met:
#
# 1. Redistributions of source code must retain the above copyright notice, 
#    this list of conditions and the following disclaimer.
# 2. Redistributions in binary form must reproduce the above copyright notice, 
#    this list of conditions and the following disclaimer in the documentation 
#    and/or other materials provided with the distribution.
# 3. Neither the name of the author nor the names of any contributors to the 
#    software may be used to endorse or promote products derived from this 
#    software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
# ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
# POSSIBILITY OF SUCH DAMAGE.
# __END_COPYRIGHT__

# A code generator to create the data classes.

use strict;
use warnings;

use Text::Wrap;

# The raw information for the data classes.
my %DATA_CLASSES = (
	"Category" => {
		"CLASS_DESCRIPTION" => "Represent all or part of a category in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The ID of the category within vBulletin",
			},
			{
				"name" => "name",
				"type" => "string",
				"description" => "The category name.",
				"default" => "\"\"",
			},
			{
				"name" => "parent",
				"type" => "mixed",
				"description" => "The parent object for this category. It could be either a {\@link Site} or a {\@link Category}.",
				"default" => "NULL",
			},
			{
				"name" => "forums",
				"type" => "array",
				"description" => "An array of the {\@link Forum}s that belong in this {\@link Category}.",
				"default" => "array()",
			},
		],
	},
	"Forum" => {
		"CLASS_DESCRIPTION" => "Represent all or part of a forum in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The ID of the forum within vBulletin",
			},
			{
				"name" => "name",
				"type" => "string",
				"description" => "The forum name.",
				"default" => "\"\"",
			},
			{
				"name" => "parent",
				"type" => "mixed",
				"description" => "The parent object for this forum. It could be either a {\@link Site}, a {\@link Category} or a {\@link Forum}.",
				"default" => "NULL",
			},
			{
				"name" => "threads",
				"type" => "array",
				"description" => "An array where the keys are the date of last update and the values are {\@link Thread} objects.",
				"default" => "array()",
			},
			{
				"name" => "subForums",
				"type" => "array",
				"description" => "An array of {\@link Forum}s which are children of this forum.",
				"default" => "array()",
			},
		],
	},
	"Icon" => {
		"CLASS_DESCRIPTION"	=> "Represent an icon in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The icon's vBulletin ID.",
			},
			{
				"name" => "url",
				"type" => "string",
				"description" => "The url to the image for the icon.",
				"default" => "\"\"",
			},
		],
	},
	"Infraction" => {
		"CLASS_DESCRIPTION"	=> "Represent an infraction in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The infraction's vBulletin ID.",
			},
		],
	},
	"Poll" => {
		"CLASS_DESCRIPTION"	=> "Represent a poll in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The poll's vBulletin ID.",
			},
		],
	},
	"Post" => {
		"CLASS_DESCRIPTION"	=> "Represent a post in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The ID of the post within vBulletin.",
			},
			{
				"name" => "threadId",
				"type" => "int",
				"description" => "The ID of the thread within vBulletin in which this post resides.",
				"default" => "0",
			},
			{
				"name" => "author",
				"type" => "User",
				"description" => "The author of the post.",
				"default" => "NULL",
			},
			{
				"name" => "createTime",
				"type" => "DateTime",
				"description" => "The time and date that the post was created.",
				"default" => "NULL",
			},
			{
				"name" => "text",
				"type" => "string",
				"description" => "The text of the post, in BBCode format.",
				"default" => "\"\"",
			},
			{
				"name" => "title",
				"type" => "string",
				"description" => "The title of the post, if any.",
				"default" => "\"\"",
			},
			{
				"name" => "status",
				"type" => "int",
				"description" => "The status of the post",
				"default" => "0",
			},
			{
				"name" => "edited",
				"type" => "PostEdit",
				"description" => "The last edit made to the post, if any.",
				"default" => "NULL",
			},
			{
				"name" => "icon",
				"type" => "Icon",
				"description" => "An icon for the post",
				"default" => "NULL",
			},
			{
				"name" => "infraction",
				"type" => "Infraction",
				"description" => "Any infraction given for the post.",
				"default" => "NULL",
			},
			{
				"name" => "reportThread",
				"type" => "Thread",
				"description" => "The thread created when this post was reported.",
				"default" => "NULL",
			},
			{
				"name" => "ip",
				"type" => "string",
				"description" => "The IP address the post was made from.",
				"default" => "\"\"",
			},
		],
		"CONSTANTS" => [
			{
				"name" => "STATUS_INVISIBLE",
				"value" => "1",
				"description" => "The post is invisible.",
			},
			{
				"name" => "STATUS_DELETED",
				"value" => "2",
				"description" => "The post is deleted.",
			},
			{
				"name" => "STATUS_HAS_ATTACHMENT",
				"value" => "4",
				"description" => "The post is deleted.",
			},
		],
	},
	"PostEdit" => {
		"CLASS_DESCRIPTION" => "A struct for describing an edit to a Post.",
		"PROPERTIES" => [
			{
				"name" => "time",
				"type" => "DateTime",
				"description" => "The time at which the edit occurred",
			},
			{
				"name" => "editor",
				"type" => "User",
				"description" => "The User who edited the post",
			},
			{
				"name" => "reason",
				"type" => "string",
				"description" => "The reason given for editing the post",
				"default" => "\"\"",
			},
		],
	},
	"SearchQuery" => {
		"CLASS_DESCRIPTION"	=> "A query object for the search function.",
		"PROPERTIES" => [
			{
				"name" => "keywords",
				"type" => "array",
				"description" => "Keywords to search for.",
				"default" => "array()",
			},
			{
				"name" => "user",
				"type" => "User",
				"description" => "Restrict to this user.",
				"default" => "NULL",
			},
			{
				"name" => "minDate",
				"type" => "DateTime",
				"description" => "The earliest date/time to search for.",
				"default" => "NULL",
			},
			{
				"name" => "maxDate",
				"type" => "DateTime",
				"description" => "The latest date/time to search for.",
				"default" => "NULL",
			},
			{
				"name" => "minReplies",
				"type" => "int",
				"description" => "The minimum number of replies that must be in the thread containing each returned post.",
				"default" => "0",
			},
			{
				"name" => "maxReplies",
				"type" => "int",
				"description" => "The maximum number of replies that must be in the thread containing each returned post.",
				"default" => "4294967295",
			},
		],
	},
	"Site" => {
		"CLASS_DESCRIPTION"	=> "Represent the top-level of a vBulletin installation.",
		"PROPERTIES" => [
			{
				"name" => "name",
				"type" => "string",
				"description" => "The name of the site.",
			},
			{
				"name" => "categories",
				"type" => "array",
				"description" => "An array of the {\@link Category}s that make up the vBulletin instance.",
			},
		],
	},
	"Thread" => {
		"CLASS_DESCRIPTION"	=> "Represent all or part of a thread in a vBulletin system.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The ID of the thread within vBulletin.",
			},
			{
				"name" => "forumId",
				"type" => "int",
				"description" => "The ID of the forum the thread is in.",
				"default" => "-1",
			},
			{
				"name" => "title",
				"type" => "string",
				"description" => "The thread title.",
				"default" => "\"\"",
			},
			{
				"name" => "createTime",
				"type" => "DateTime",
				"description" => "The time of thread creation.",
				"default" => "NULL",
			},
			{
				"name" => "lastUpdateTime",
				"type" => "DateTime",
				"description" => "The time the thread was last updated.",
				"default" => "NULL",
			},
			{
				"name" => "status",
				"type" => "int",
				"description" => "A bitmask describing the status of this thread",
				"default" => "0",
			},
			{
				"name" => "numPosts",
				"type" => "int",
				"description" => "The number of posts in the thread (NOT the number of posts being returned).",
				"default" => "-1",
			},
			{
				"name" => "numViews",
				"type" => "int",
				"description" => "The number of times the thread has been viewed.",
				"default" => "-1",
			},
			{
				"name" => "posts",
				"type" => "array",
				"description" => "An array where the keys are post numbers and the values are {\@link Post} objects.",
				"default" => "array()",
			},
			{
				"name" => "poll",
				"type" => "Poll",
				"description" => "The poll (if any) which is attached to this thread.",
				"default" => "NULL",
			},
			{
				"name" => "icon",
				"type" => "Icon",
				"description" => "The icon (if any) for this thread.",
				"default" => "NULL",
			},
			{
				"name" => "numStars",
				"type" => "int",
				"description" => "The star rating for this thread.",
				"default" => "-1",
			},
		],
		"CONSTANTS" => [
			{
				"name" => "STATUS_LOCKED",
				"value" => "1",
				"description" => "The thread is locked.",
			},
			{
				"name" => "STATUS_DELETED",
				"value" => "2",
				"description" => "The thread is deleted.",
			},
			{
				"name" => "STATUS_STICKY",
				"value" => "4",
				"description" => "The thread is sticky.",
			},
			{
				"name" => "STATUS_HAS_ATTACHMENTS",
				"value" => "8",
				"description" => "The thread contains posts with attachments",
			},
			{
				"name" => "STATUS_HAS_DELETED_POSTS",
				"value" => "16",
				"description" => "The thread contains deleted posts",
			},
			{
				"name" => "STATUS_HAS_HIDDEN_POSTS",
				"value" => "32",
				"description" => "The thread contains hidden posts",
			},
		],
	},
	"User" => {
		"CLASS_DESCRIPTION"	=> "Represent a vBulletin user.",
		"PROPERTIES" => [
			{
				"name" => "id",
				"type" => "int",
				"description" => "The user's vBulletin ID.",
			},
			{
				"name" => "name",
				"type" => "string",
				"description" => "The name of the user.",
				"default" => "\"\"",
			},
			{
				"name" => "realname",
				"type" => "string",
				"description" => "The user's real name.",
				"default" => "\"\"",
			},
		],
	},
);

# Read in the copyright statement from this file.
my @copyright_statement;
if (open(THIS, $0)) {
	my $record = 0;
	while (<THIS>) {
		chomp;
		if (/__BEGIN_COPYRIGHT__/o && @copyright_statement == 0) {
			$record = 1;
		} elsif (/__END_COPYRIGHT__/o) {
			$record = 0;
		} else {
			if ($record) {
				s/^# ?//o;
				push @copyright_statement, $_;
			}
		}
	}
	close(THIS);
}

# Read the file template
my @template;
while (<DATA>) {
	chomp;
	push @template, $_;
}

# Expand the %DATA_CLASSES hash to include all the data we need and then write
# out the files.
foreach my $class_name (keys %DATA_CLASSES) {
	$DATA_CLASSES{$class_name}{"COPYRIGHT"} = "/*\n * " . join("\n * ", @copyright_statement) . "\n */";
	$DATA_CLASSES{$class_name}{"COPYRIGHT"} =~ s/\*\s+$/\*/mg;
	$DATA_CLASSES{$class_name}{"CLASSNAME"} = $class_name;

	# Find out how long the longest type + property name is, for alignment purposes
	my $prop_type_and_name_length = 0;
	foreach (@{$DATA_CLASSES{$class_name}{"PROPERTIES"}}) {
		my $length = length($_->{"type"}) + 2 + length($_->{"name"});
		if ($length > $prop_type_and_name_length) {
			$prop_type_and_name_length = $length;
		}
	}

	my @property_descriptions;
	my @param_descriptions;
	my @params;
	my @constructor_body;
	my @defaults;
	foreach (@{$DATA_CLASSES{$class_name}{"PROPERTIES"}}) {
		my %prop = %{$_};

		# For wrapping
		my $prop_type_and_name = $prop{"type"} . ' $' . $prop{"name"};
		my $indent = "\t" x (int(($prop_type_and_name_length - length($prop_type_and_name)) / 4) + 1);

		# The constructor parameter
		my $param = "\$$prop{name}";
		if ($prop{"type"} =~ /^[A-Z]/o) {
			$param = "$prop{type} $param";
		}
		if (defined($prop{"default"})) {
			push @defaults, [$prop{"name"}, $prop{"default"}];
			$param .= " = $prop{default}";
		}
		push @params, $param;
		
		# The assignment in the constructor body
		push @constructor_body, "\t\t\$this->data['$prop{name}'] = \$$prop{name};";
		push @constructor_body, "\t\t\$this->type['$prop{name}'] = \"$prop{type}\";";
		
		# Comments for both the class-level and constructor level doc comments.
		my $prop_desc = " *\t\@property\t$prop_type_and_name$indent$prop{description}";
		my $param_desc = "\t *\t\@param\t$prop_type_and_name$indent$prop{description}";
		{
			local $Text::Wrap::columns = 80;
			local $Text::Wrap::tabstop = 4;
			$indent .= "\t" x int(length($prop_type_and_name) / 4);
			push @property_descriptions, wrap("", " *\t\t\t\t$indent", $prop_desc);
			push @param_descriptions, wrap("", "\t *\t\t\t$indent", $param_desc);
		}
	}

	my $class_constants = "";
	my @class_constants;
	foreach (@{$DATA_CLASSES{$class_name}{"CONSTANTS"}}) {
		my %const = %{$_};

		my $const;
		$const  = "\t/** $const{description} */\n";
		$const .= "\tpublic static \$$const{name} = $const{value};\n";

		push @class_constants, $const;
	}
	if (@class_constants) {
		$class_constants = "\n" . join("\n", @class_constants);
	}

	my $defaults = "";
	if (@defaults) {
		$defaults .= <<'METHODHEAD';

	/** Default values for the properties. These will be used to minimise the 
	 *	data to be sent over the wire.
	 *
	 *	@return	array	Default values for properties which have them.
	 */
	protected function defaultPropertyValues() {
		return array(
METHODHEAD
		foreach (@defaults) {
			my $name = $_->[0];
			my $val = $_->[1];
			$defaults .= "\t\t\t\"$name\" => $val,\n";
		}
		
		$defaults .= <<METHODTAIL
		);
	}
METHODTAIL
	}

	$DATA_CLASSES{$class_name}{"PROPERTY_DESCRIPTIONS"} = join("\n", @property_descriptions);
	$DATA_CLASSES{$class_name}{"CONSTRUCTOR_PARAM_DESCRIPTIONS"} = join("\n", @param_descriptions);
	$DATA_CLASSES{$class_name}{"CONSTRUCTOR_PARAMS"} = join(", ", @params);
	$DATA_CLASSES{$class_name}{"CONSTRUCTOR_BODY"} = join("\n", @constructor_body);
	$DATA_CLASSES{$class_name}{"CLASS_CONSTANTS"} = $class_constants;
	$DATA_CLASSES{$class_name}{"DEFAULT_PROPERTY_VALUES_METHOD"} = $defaults;

	write_file($class_name, $DATA_CLASSES{$class_name}, \@template);
}

sub write_file {
	my $class_name = shift;
	my $class_data = shift;
	my $template = shift;

	if (open(FILE, ">$class_name.php")) {
		foreach (@{$template}) {
			my $line = $_;
			foreach my $var (keys %{$class_data}) {
				my $var_val = $class_data->{$var};
				$line =~ s/\$\{$var\}/$var_val/g;
			}
			print FILE "$line\n";
		}
		close(FILE);
	}
}

__DATA__
<?php
${COPYRIGHT}
/* XXX Autogenerated - do not edit XXX*/
/**	@package vBulletinAPI
 *	@filesource
 */

require_once("DataObject.php");

/** ${CLASS_DESCRIPTION}
 *
${PROPERTY_DESCRIPTIONS}
 *	@package	vBulletinAPI
 */
class ${CLASSNAME}
extends DataObject
{${CLASS_CONSTANTS}
	/** Create a new {@link ${CLASSNAME}}.
	 *
${CONSTRUCTOR_PARAM_DESCRIPTIONS}
	 */
	public function __construct(${CONSTRUCTOR_PARAMS}) {
${CONSTRUCTOR_BODY}
	}
${DEFAULT_PROPERTY_VALUES_METHOD}}
?>
