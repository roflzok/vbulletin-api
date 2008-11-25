#!/usr/bin/perl
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

# A set of tests for the vBulletin API

use strict;
use warnings;

use Cwd qw(abs_path);
use File::Find;
use File::Spec;
use Test::Harness;

# This program takes one argument, the URL of an installed copy of the
# vBulletin API.
if ($ARGV[0]) {
	$ENV{'VBULLETIN_API_URL'} = $ARGV[0];
} else {
	print "Usage: $0 <url>\n";
	exit 1;
}

# Find the tests sub directory
my $this_program = abs_path($0);
my ($volume, $program_dir, $program_file) = File::Spec->splitpath($this_program);
my $test_path = File::Spec->catpath($volume, $program_dir, "tests");

# Find all the tests
my @test_files;
find(
	sub {
		if (/\.t$/o) {
			push @test_files, $File::Find::name;
		}
	},
	$test_path
);

# Run the tests
runtests(@test_files);
