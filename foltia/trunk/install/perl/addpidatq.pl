#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#addpidatq.pl
#
#PID�������atq������롣folprep.pl���饭�塼�����ϤΤ���˻Ȥ���
#
# DCC-JPL Japan/foltia project
#
#

use DBI;
use DBD::Pg;
use Schedule::At;
use Time::Local;

$path = $0;
$path =~ s/addpidatq.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";


#�����������뤫?
$pid = $ARGV[0] ;
if ($pid eq "" ){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage;addpidatq.pl <PID>\n";
	exit;
}


#DB����(PID)
	my $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);
	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;

$DBQuery =  "SELECT count(*) FROM  foltia_subtitle WHERE pid = '$pid' ";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @titlecount= $sth->fetchrow_array;
 
 if ($titlecount[0]  == 1 ){

$DBQuery =  "SELECT bitrate FROM  foltia_tvrecord , foltia_subtitle  WHERE foltia_tvrecord.tid = foltia_subtitle.tid AND pid='$pid' ";
 $sth = $dbh->prepare($DBQuery);
$sth->execute();
 @titlecount= $sth->fetchrow_array;
$bitrate = $titlecount[0];#�ӥåȥ졼�ȼ���

#PID���
$now = &epoch2foldate(`date +%s`);

$DBQuery =  "SELECT stationrecch FROM foltia_station,foltia_subtitle WHERE foltia_subtitle.pid = '$pid'  AND  foltia_subtitle.stationid =  foltia_station.stationid ";


#stationID����recch
 $stationh = $dbh->prepare($DBQuery);
	$stationh->execute();
@stationl =  $stationh->fetchrow_array;
$recch = $stationl[0];

$DBQuery =  "SELECT  * FROM  foltia_subtitle WHERE pid='$pid' ";
 $sth = $dbh->prepare($DBQuery);
$sth->execute();
($pid ,
$tid ,
$stationid ,
$countno,
$subtitle,
$startdatetime,
$enddatetime,
$startoffset ,
$lengthmin,
$atid ) = $sth->fetchrow_array();
# print "$pid ,$tid ,$stationid ,$countno,$subtitle,$startdatetime,$enddatetime,$startoffset ,$lengthmin,$atid \n";

if($now< $startdatetime){#������̤������դʤ�
#�⤷�����ϻ��郎15ʬ�ܾ���ʤ�ƥ��塼
$startafter = &calclength($now,$startdatetime);
&writelog("addpidatq DEBUG \$startafter $startafter \$now $now \$startdatetime $startdatetime");

if ($startafter > 14 ){

#���塼���
 Schedule::At::remove ( TAG => "$pid"."_X");
	&writelog("addpidatq remove que $pid");


#���塼����
	#�ץ�����ư��������ȳ��ϻ����-5ʬ
$atdateparam = &calcatqparam(300);
	Schedule::At::add (TIME => "$atdateparam", COMMAND => "$toolpath/perl/folprep.pl $pid" , TAG => "$pid"."_X");
	&writelog("addpidatq TIME $atdateparam   COMMAND $toolpath/perl/folprep.pl $pid ");
}else{
$atdateparam = &calcatqparam(60);
$reclength = $lengthmin * 60;

#���塼���
 Schedule::At::remove ( TAG => "$pid"."_R");
	&writelog("addpidatq remove que $pid");

if ($countno eq ""){
	$countno = "0";
}

Schedule::At::add (TIME => "$atdateparam", COMMAND => "$toolpath/perl/recwrap.pl $recch $reclength  $bitrate $tid $countno $pid" , TAG => "$pid"."_R");
	&writelog("addpidatq TIME $atdateparam   COMMAND $toolpath/perl/recwrap.pl $recch $reclength  $bitrate $tid $countno $pid");

}#end #�⤷�����ϻ��郎15ʬ�ܾ���ʤ�ƥ��塼

}else{
&writelog("addpidatq drop:expire  $pid  $startafter  $now  $startdatetime");
}#������̤������դʤ�

}else{
print "error record TID=$tid SID=$station $titlecount[0] match:$DBQuery\n";
&writelog("addpidatq error record TID=$tid SID=$station $titlecount[0] match:$DBQuery");

}#end if ($titlecount[0]  == 1 ){


