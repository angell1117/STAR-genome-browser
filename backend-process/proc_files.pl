#!/usr/bin/perl

use strict;
use Cwd;
use Utils;
use File::Copy;

my $skip = 1;
my $upload_backup = "/data/upload-backup/";
my $upload_unknow = "/data/upload-unknow/";
my $data_dir = "/data/upload-raw-data/";
my $db_dir = "/data/annoj-upload/";

sub get_dir {
  my ($dirname, $db_dir) = @_;
  if(-f $dirname)
  {
        return;
  }
  if($dirname eq '.' || $dirname eq '..') {return;}

  opendir DIR, $dirname or die "Can not open $dirname.$!";
  my @files = readdir DIR;
  closedir DIR;

  foreach my $file(@files) 
  {
    if(-d $dirname.$file) 
    {
      #get_dir($file, $db_dir);
    }
    if(-f $dirname.$file) 
    {
      get_file($dirname, $file, $db_dir);
    }
  }
}
sub get_file {
  my ($dirname, $file, $db_dir) = @_;

  #format meta file  
  if($file =~ m/\.format_$/) {return;}
    my $filename = $dirname.$file;

  my $specify_format;
  if( -e "$filename.format_")
  {
    open(FILE, "$filename.format_");
    while(my $line = <FILE>)
    {
      chomp($line);
      if($line ne "")
      {
        $specify_format = $line;
        last;
      }
    }
   close(FILE);
  }
              
  my $format = Utils::epigenomic_file_format($filename);
  if($specify_format =~ m/BED/) {$format = "BED";}
  if($specify_format =~ m/\d+:\d+/) {$format = $specify_format;}

  if($format eq "unknow")
  {
    Utils::WriteLog("$filename is unknow!\n");
    move($filename, $upload_unknow) || warn "Could not move file:$!";
    return;
  }

  #process data file and deposit into database
  my $cmd = "./ngsproc -a create -t $file -i $filename -f $format -d $db_dir";

  my $ret;
  $ret = system($cmd);
  if($ret != 0) { Utils::WriteLog("Failed: $cmd\n"); }
  else{ Utils::WriteLog("Succeed: $cmd\n"); }
  move($filename, $upload_backup);
  move("$filename.format_", $upload_backup);
}

MAIN : {
  if (! -e $data_dir){mkdir($data_dir,0755);}
  if (! -e $db_dir){mkdir($db_dir,0755);}
  if (! -e $upload_backup){mkdir($upload_backup,0755);}
  if (! -e $upload_unknow){mkdir($upload_unknow,0755);}

  while(1)
  {
    get_dir($data_dir, $db_dir);
    sleep(2);
  }
} 
