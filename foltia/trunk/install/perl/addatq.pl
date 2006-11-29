#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#addatq.pl
#
#TID�ȶ�ID��������atq�������
# addatq.pl <TID> <StationID> [DELETE]
# DELETE�ե饰���Ĥ��Ⱥ���Τ߹Ԥ�
#
# DCC-JPL Japan/foltia project
#
#

use DBI;
use DBD::Pg;
use Schedule::At;
use Time::Local;

$path = $0;
$path =~ s/addatq.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";

#�����������뤫?
$tid = $ARGV[0] ;
$station = $ARGV[1];

if (($tid eq "" )|| ($station eq "")){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage;addatq.pl <TID> <StationID> [DELETE]\n";
	exit;
}

#DB����(TID��StationID����PID��)
 $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);
	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;

if ($station == 0){
	$DBQuery =  "SELECT count(*) FROM  foltia_tvrecord WHERE tid = '$tid'  ";
}else{
	$DBQuery =  "SELECT count(*) FROM  foltia_tvrecord WHERE tid = '$tid' AND stationid  = '$station' ";
}
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @titlecount = $sth->fetchrow_array;
#���������

#2�ʾ���ä���
if ($titlecount[0]  >= 2){
	#���ʼ�꤬�ޤޤ�Ƥ��뤫Ĵ�٤�
	$DBQuery =  "SELECT count(*) FROM  foltia_tvrecord WHERE tid = '$tid'  AND  stationid  ='0' ";
	$kth = $dbh->prepare($DBQuery);
	$kth->execute();
 	@reservecounts = $kth->fetchrow_array;

	if($reservecounts[0] >= 1 ){#�ޤޤ�Ƥ�����
		if($tid == 0){
		#����ΰ�������SID 0���ä���
		#���ɼ�����ͽ��
#		&writelog("addatq  DEBUG; ALL STATION RESERVE. TID=$tid SID=$station $titlecount[0] match:$DBQuery");
		&addcue;
		}else{
		#�ۤ�������Ͽ��addatq��ͽ������Ƥ���뤫��ʤˤ⤷�ʤ�
#		&writelog("addatq  DEBUG; SKIP OPERSTION. TID=$tid SID=$station $titlecount[0] match:$DBQuery");
		exit;
  		}#end if �դ��ޤ�Ƥ�����
	}#endif 2�İʾ�	
}elsif($titlecount[0]  == 1){
		&addcue;
}else{
&writelog("addatq  error; reserve impossible . TID=$tid SID=$station $titlecount[0] match:$DBQuery");
}

#�����
# if ($titlecount[0]  == 1 ){
# 	& addcue;
# }else{
#&writelog("addatq  error record TID=$tid SID=$station $titlecount[0] match:$DBQuery");
#}

sub addcue{

if ($station == 0){
	$DBQuery =  "SELECT * FROM  foltia_tvrecord WHERE tid = '$tid'  ";
}else{
	$DBQuery =  "SELECT * FROM  foltia_tvrecord WHERE tid = '$tid' AND stationid  = '$station' ";
}
 $sth = $dbh->prepare($DBQuery);
$sth->execute();
 @titlecount= $sth->fetchrow_array;
$bitrate = $titlecount[2];#�ӥåȥ졼�ȼ���

#PID���
$now = &epoch2foldate(`date +%s`);
$twodaysafter = &epoch2foldate(`date +%s` + (60 * 60 * 24 * 2));
#���塼�����ľ��2����ޤ�
if ($station == 0 ){
	$DBQuery =  "
SELECT * from foltia_subtitle WHERE tid = '$tid'  AND startdatetime >  '$now'  AND startdatetime < '$twodaysafter' ";
}else{
	$DBQuery =  "
SELECT * from foltia_subtitle WHERE tid = '$tid' AND stationid  = '$station'  AND startdatetime >  '$now'  AND startdatetime < '$twodaysafter' ";
#stationID����recch
$getrecchquery="SELECT stationid , stationrecch  FROM foltia_station where stationid  = '$station' ";
 $stationh = $dbh->prepare($getrecchquery);
	$stationh->execute();
@stationl =  $stationh->fetchrow_array;
$recch = $stationl[1];
}

 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 
while (($pid ,
$tid ,
$stationid ,
$countno,
$subtitle,
$startdatetime,
$enddatetime,
$startoffset ,
$lengthmin,
$atid ) = $sth->fetchrow_array()) {

if ($station == 0 ){
#stationID����recch
$getrecchquery="SELECT stationid , stationrecch  FROM foltia_station where stationid  = '$stationid' ";
 $stationh = $dbh->prepare($getrecchquery);
	$stationh->execute();
@stationl =  $stationh->fetchrow_array;
$recch = $stationl[1];
}
#���塼����
	#�ץ�����ư��������ȳ��ϻ����-1ʬ
$atdateparam = &calcatqparam(300);
$reclength = $lengthmin * 60;
#&writelog("TIME $atdateparam COMMAND $toolpath/perl/tvrecording.pl $recch $reclength 0 0 $bitrate $tid $countno");
#���塼���
 Schedule::At::remove ( TAG => "$pid"."_X");
	&writelog("addatq remove $pid");
if ( $ARGV[2] eq "DELETE"){
	&writelog("addatq remove  only $pid");
}else{
	Schedule::At::add (TIME => "$atdateparam", COMMAND => "$toolpath/perl/folprep.pl $pid" , TAG => "$pid"."_X");
	&writelog("addatq TIME $atdateparam   COMMAND $toolpath/perl/folprep.pl $pid ");
}
##processcheckdate 
#&writelog("addatq TIME $atdateparam COMMAND $toolpath/perl/schedulecheck.pl");
}#while



}#endsub
