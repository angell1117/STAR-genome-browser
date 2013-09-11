#Utils.pm
package Utils;

sub WriteLog
{
        my $content = shift;

        my $date = `date +%Y%m%d`;
        my $time = `date`;
        chomp $date;
        chomp $time;

        open(FILE,">>log$date") || die("could not open log$date!");
        printf FILE "[$time]:$content\n";
        close(FILE);
}
sub GetHostname
{
	my $ip = `/sbin/ifconfig | grep 'inet ' | head -n 1 | awk '{print \$2}' | sed -e 's/addr\://'`;
	my $hostname = `host $ip`;
	my $pos = rindex($hostname,"pointer");
	$hostname = substr($hostname,$pos+8);
	$pos = rindex($hostname,".");
	$hostname = substr($hostname,0,$pos);
	return $hostname;
}
sub RunningInstance
{
	my @pid = `ps aux |grep monitor.pl|grep -v grep|awk '{print \$2}'`;
	my $num = @pid;
	if($num > 1)
	{
        	printf("Another instance is running!\n");
       	 	exit;
	}
}

sub epigenomic_file_format {
  my ($fname) = @_;
  open (MAP, $fname) or die "Cannot open file $fname: $!\n";
  my $cnt = 0;
  my $format = "unknow";
  my $format1 = "unknow";
  my $format2 = "unknow";
  my @types;
  while (my $line = <MAP>){
	$line =~ tr/[A-Z]/[a-z]/; 
	$line =~ s/\s+$//;
	my @columns = split(/\t/, $line);
	if(@columns <= 1){
		@columns = split(/ /, $line);
	}
	$cnt++;
	if($line  =~ /chrom=/ ){
		$format = "wig";
	}
	if($cnt >= 20 && $cnt < 40){
	  if($columns[0] =~ /^[acgtn]+$/ && $columns[1] =~ /^chr/  && $columns[2] =~ /^\d+$/ && 
	    $columns[3] =~ /^\d+$/ && ($columns[4] eq '+' || $columns[4] eq "-")) {
		my $start = $columns[2] + 0;
		my $end = $columns[3] + 0;
		if($start < $end){
			$format = "2:3:4:5:-1:1"; 
			$types[0]++;
			next;
		}
	  }
	  if($columns[0] =~ /^\d+$/ && $columns[1] =~ /^\d+$/ && $columns[2] =~ /^\d+$/ && 
	    ($columns[3] eq "0" || $columns[3] eq "1")) {
		my $chrom = $column[0] + 0;
		my $start = $columns[1] + 0;
		my $end = $columns[2] + 0;
		if($chrom < 30 && (($columns[3] eq "0" && $end <= $start) || ($columns[3] eq "1" && $end >= $start))){
			$format = "1:2:3:4:-1:-1"; 
			$types[1]++;
			$types[10]++;
			next;
		}
	  }
	  if(@columns == 3 && $columns[0] =~ /^chr/ && $columns[1] =~ /^\d+$/ && $columns[2] =~ /^(\+|-|r|f)$/) {
		$format = "1:2:-1:3:-1:-1"; 
		$types[2]++;
		next;
	  }
	  if($columns[1] =~ /^[acgtn]+$/ && $columns[2] =~ /^(u(0|1|2)|r(0|1|2)|qc|nm|rm|\d+:\d+:\d+)$/ ) {
		$format = "eland";
		$types[4]++;
		next;
	  }
	  if($columns[12] =~ /^\d+$/ && ($columns[13] eq 'f' || $columns[13] eq "r")) {
		$format = "elandexport";
		$types[3]++;
		next;
	  }
	  if(($columns[0] =~ /^\d+$/ || $columns[0] =~ /^chr/ ) && $columns[4] =~ /^[acgtn]+$/ && 
		$columns[3] =~ /^\d+$/ && $columns[2] =~ /^\d+$/ && $columns[1] =~ /^(\+|-)$/) {
		$format = "1:3:4:2:-1:5"; 
		$types[12]++;
		my $val = -1;
	  	if($columns[5] =~ /^\d+$/) {
			$val = 6;
			$format2 = "1:3:4:2:6:5"; 
			$types[13]++;
		}
		next;
	  }
	  if($columns[4] =~ /^[acgtn]+$/ && $columns[2] =~ /^(chr|chromosome_)?([23][rl]|[ixvyuml]+|\d+)$/ && $columns[3] =~ /^\d+$/ && $columns[1] =~ /^(\+|-)$/) {
		$format = "3:4:-1:2:-1:5"; 
		$types[5]++;
		#$format =  "bowtie";
		next;
	  }
	  if($columns[2] =~ /^chr/ && $columns[5] =~ /^\d+$/ && $columns[6] =~ /^\d+$/ && $columns[7] =~ /(\+|-)/ && $columns[10] =~ /^[acgtn]+$/) {
		#$format =  "bowtiesam";
		$format =  "3:6:7:8:-1:11";
		$types[6]++;
		next;
	  }
	  if($columns[2] =~ /^(chr)?([23][rl]|[ixvyuml]+|\d+|\*)/ && $columns[3] =~ /^\d+$/ && $columns[9] =~ /^[acgtn.=]+$/) {
		#$format =  "sam";
		$format =  "3:4:-1:-1:-1:10";
		$types[7]++;
		next;
	  }
	  if(($columns[0] =~ /fold/ || $columns[0] =~ /^(chr)?([23][rl]|[ixvymcul]+|\d+)$/) && $columns[3] =~ /^\d+$/ && $columns[4] =~ /^\d+$/ && $columns[6] =~ /(\+|-|\.)/) {
		$format =  "gff";
		$types[8]++;
		next;
	  }
	  if($columns[1] =~ /^(chr)?([23][rl]|[ixvyuml]+|\d+)$/ && $columns[2] =~ /^\d+$/ && $columns[3] =~ /^(\+|-)$/ && $columns[13] =~ /^\d+$/) {
		$format =  "mapview";
		$types[9]++;
		next;
	  }
	  if(($columns[0] =~ /^\d+$/ || $columns[0] =~ /^chr/ ) && $columns[1] =~ /^\d+$/ && $columns[2] =~ /^\d+$/) {
		$seq = -1;
		for(my $i = 0; $i < @columns; $i++){
			if($columns[$i] =~ /^[.acgtn]{10,100}$/){
				$seq = $i + 1;
				last;
			}
		}
		$chrom = -1;
		for(my $i = 0; $i < @columns; $i++){
			if($columns[$i] =~ /^(\+|-|r|f)$/){
				$chrom = $i + 1;
				last;
			}
		}
		my $start = $columns[1] + 0;
		my $end = $columns[2] + 0;

		my $chr;
		if($columns[0] =~ /^chr/){ $chr = 15;}
		else { $chr = $column[0] + 0;}

		if($chr >=0 && $chr < 30){
			$types[11]++;
		}

		my $val = -1;
	  	if(@columns == 4 &&  $columns[3] =~ /^(-?\d+)(\.\d+)?$/) {
			$val = 4;
		}
		if( $format1 eq "unknow" && $chr < 30){
			$format1 = "1:2:3:$chrom:$val:$seq"; 
			$types[10]++;
			next;
		}
		if( $format1 eq "1:2:3:$chrom:$val:$seq" && $chr < 30){
			$types[10]++;
			next;
		}
	  }
	  next;
      }
	if($cnt >= 40){last;} 
  }
  if($format eq "wig"){
	close MAP;
	return $format;
  }

  if($types[1] >= 20 ){
	close MAP;
	return $format;
  }
  if($types[10] >= 20 ){
	close MAP;
	return $format1;
  }
  if($types[13] >= 20 ){
	close MAP;
	return $format2;
  }
  if($types[11] >= 20){
	close MAP;
	return "1:2:3:-1:-1:-1";
  }
  if($types[5] >= 16){
	close MAP;
	return "3:4:-1:2:-1:5";
  }
  if($types[7] >= 16){
	close MAP;
	return "3:4:-1:-1:-1:10";
  }
  if($format ne "unknow" && $format ne "wig"){
	if($types[3] >= 1){
		close MAP;
		return "elandexport";
	}
	if($types[4] >= 16){
		close MAP;
		return "eland";
	}
	for($i = 0; $i < scalar(@types); $i++)
	{
		if($types[$i] == 20){
			close MAP;
			return $format;
		}
	} 
  }
  $format = "unknow";
  close MAP; 
  return $format;
}
1;
__END__

