#!/usr/bin/perl

use strict;
use Cwd;
use Utils;
use File::Copy;

MAIN : {
	my ($file) = $ARGV[0];
	my $format = Utils::epigenomic_file_format($file);
	printf($format);
} 
