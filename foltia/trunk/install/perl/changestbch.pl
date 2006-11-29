#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#changestbch.pl
#
# ��⥳���˥åȤ����Ƴ������塼�ʤο�����ڤ��ؤ��롣
#�б���˥å�
# Tira-2.1: Remote Control Receiver/Transmitter
#http://www.home-electro.com/tira2.php
#
#usage :changestbch.pl  [PID]
#����
#[PID]���ȥץ����ID
#
# �����ͥ��ڤ��ؤ���ή��
# changestbch.pl :�ɤ������п����Ĵ�٤� transfer.pl �˥����ͥ��ѹ����������Ϥ���
# ��
# transfer.pl ����ե���������� <http://www.geocities.jp/coffee_style/Tira-2-0.html>
#
#
# DCC-JPL Japan/foltia project
#

use DBI;
use DBD::Pg;

$path = $0;
$path =~ s/changestbch.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}
require 'foltialib.pl';


#	&writelog("changestbch DEBUG START");


#�����������뤫?
$pid = $ARGV[0] ;
if ($pid eq "" ){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage :changestbch.pl  [PID]\n";
	&writelog("changestbch ERR PID null");
	exit;
}

# $haveirdaunit = 1;��⥳��Ĥʤ��Ǥ뤫�ɤ���
if ($haveirdaunit == 1){
#�ǥХ��������뤫�ɤ���
if (-e "/dev/ttyUSB0"){

# pid�����(���Х��ޥ��)Ĵ�٤�
#DB�����
	my $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);
	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;

$DBQuery =  "SELECT foltia_station.tunertype,foltia_station.tunerch ,foltia_station.stationrecch ,foltia_station.stationid FROM foltia_subtitle,foltia_station WHERE foltia_subtitle.stationid = foltia_station.stationid AND foltia_subtitle.pid =  '$pid' ";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @chstatus = $sth->fetchrow_array;
 	$tunertype = $chstatus[0];
	$tunercmd =  $chstatus[1];
	$recch =  $chstatus[2];
	$stationid =  $chstatus[3];	
$cmdjoined = "$tunertype"."$tunercmd";

&writelog("changestbch DEBUG  $cmdjoined :$recch:$stationid");

$length = length($cmdjoined);
$sendcmdfile = "";
for ($i=0 ; $i < $length ; $i++ ){
	$cmdtxt = substr($cmdjoined,$i,1);
#	print "$cmdtxt\n";
	$sendcmdfile .= " $toolpath/perl/irda/$cmdtxt".".dat ";
}#for

#if (-e "$toolpath/perl/irda/$sendcmdfile"){
	system("$toolpath/perl/irda/transfer.pl $sendcmdfile");
&writelog("changestbch DEBUG  $toolpath/perl/irda/transfer.pl $toolpath/perl/irda/$sendcmdfile");
#}else{
#	&writelog("changestbch ERR cmd file not found:$toolpath/perl/irda/$sendcmdfile");
#}#if -e



#BS-hi b x103 || b 3
#���å����ơ������ c x264 || c 2 

#���ޥ�ɤ���¹Ԥ��륳�ޥ���Ȥ�Ω��
}else{
#�ǥХ��������ʤ�
		&writelog("changestbch ERR Tira2 Not found.");
}#end if (-e "/dev/ttyUSB0")

}#endif if ($haveirdaunit == 1


