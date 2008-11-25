#!/usr/bin/perl
# vim:ft=perl
#
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

use strict;
use warnings;

use RPC::XML;
use RPC::XML::Client;
use Test::More;

# Some data describing the methods to test
my %METHODS = (
	"getPost" => {
		"signature" => ["struct", "struct"],
		"good" => [
			{"postId" => 1},			
			{"postId" => 2},			
		],
		"bad" => [
			{},
			{"postId" => "green"},
		],
	},
	"getThread" => {
		"signature" => ["struct", "struct"],
		"good" => [
			{"threadId" => 1},			
			{"threadId" => 2},			
		],
		"bad" => [
			{},
			{"threadId" => "green"},
		],
	},
	"system.listMethods" => {
		"signature" => ["array"],
	},
	"system.methodHelp" => {
		"signature" => ["array", "string"],
	},
	"system.methodSignature" => {
		"signature" => ["array", "string"],
	},
);

# Calculate the number of tests
my $num_tests = 3; # Fixed tests
foreach my $method (keys %METHODS) {
	$num_tests += 2; # introspection methods
	if ($METHODS{$method}{"good"}) {
		$num_tests += (@{$METHODS{$method}{"good"}});
	}
	if ($METHODS{$method}{"bad"}) {
		$num_tests += (@{$METHODS{$method}{"bad"}});
	}
}
plan tests => $num_tests;

# Must have the URL of the vBulletin API
ok(defined($ENV{'VBULLETIN_API_URL'}), "ENV{VBULLETIN_API_URL} defined");

# The client we'll use 
my $xml_rpc_client = new RPC::XML::Client($ENV{'VBULLETIN_API_URL'});
isa_ok($xml_rpc_client, "RPC::XML::Client", "new RPC::XML::Client()");

# Test system.listMethods
{
	my $response = $xml_rpc_client->send_request("system.listMethods");
	if ($response->isa("RPC::XML::fault")) {
		fail("system.listMethods()");
	} else {
		$response = $response->value;
		my @expected_method_names = sort keys %METHODS;
		is_deeply($response, \@expected_method_names, "system.listMethods()");
	}
}

# Test system.methodHelp
{
	foreach my $method (sort keys %METHODS) {
		my $response = $xml_rpc_client->send_request("system.methodHelp", $method);
		if ($response->isa("RPC::XML::fault")) {
			fail("system.methodHelp()");
		} else {
			$response = $response->value;
			ok((length($response) > 10), "system.methodHelp($method)");
		}
	}
}

# Test system.methodSignature
{
	foreach my $method (sort keys %METHODS) {
		my $response = $xml_rpc_client->send_request("system.methodSignature", $method);
		if ($response->isa("RPC::XML::fault")) {
			fail("system.methodHelp()");
		} else {
			$response = $response->value;
			is_deeply($response->[0], $METHODS{$method}{'signature'}, "system.methodHelp()");
		}
	}
}

# Test the methods based on the "good" and "bad" data
{
	foreach my $method (sort keys %METHODS) {
		foreach my $test_data (@{$METHODS{$method}{"good"}}) {
			my $response = $xml_rpc_client->send_request($method, $test_data);
			isa_ok($response, "RPC::XML::struct");
		}
		foreach my $test_data (@{$METHODS{$method}{"bad"}}) {
			my $response = $xml_rpc_client->send_request($method, $test_data);
			isa_ok($response, "RPC::XML::fault");
		}
	}
}
