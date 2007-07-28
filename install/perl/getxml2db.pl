#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
# usage :getxml2db [long]
#����ܤ���������<http://cal.syoboi.jp/>�������ȥǡ���XML�������foltia DB�˥���ݡ��Ȥ���
#
#���ץ����
#long:2����ʬ�����ࡣ���Υ⡼�ɤǰ������󤻤Ф褤�Ǥ��礦��
#
# DCC-JPL Japan/foltia project
#
#

use LWP::Simple;
use Jcode;
use Time::Local;
use DBI;
use DBD::Pg;

$path = $0;
$path =~ s/getxml2db.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}
require "foltialib.pl";


$processes =  &processfind("getxml2db.pl");
if ($processes > 1 ){
#print "process  found:$processes\n";
&writelog("getxml2db   processes exist. exit:");
exit;
}else{
#print "process NOT found:$processes\n";
&writelog("getxml2db  Normal launch.");
}

if ($ARGV[0]  eq "long"){
	$uri="http://cal.syoboi.jp/cal_chk.php";
	#$uri="http://syobocal.orz.hm/cal_chk.php";
	&writelog("getxml2db  use long mode.");
}else{
	$uri="http://cal.syoboi.jp/cal_chk.xml";
	#$uri="http://syobocal.orz.hm/cal_chk.xml";
}


	my $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);

	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;

#$dbh->{AutoCommit} = 0;

my ($content) = get("$uri");
if ($content eq ""){
&writelog("getxml2db   no responce from $uri, exit:");
	exit;#����ܤ��뤬����Ƥ���ʤ�
}

my (@line) = split(/\n/, $content);

foreach(@line){

Jcode::convert(\$_,'euc');

#<ProgItem PID="21543" TID="528" StTime="20041114213000" EdTime="20041114220000" ChName="AT-X" Count="4" StOffset="0" SubTitle="������衢����ʥ��" Title="���Ȥ����ȡ��꡼ ŷ�ȤΤ��ä�" ProgComment=""/>
if (/^<ProgItem /){
s/<ProgItem //i;
s/\"\/>/\" /i;
s/\"[\s]/\";\n/gio;
s/\'/\\'/gio;
s/\"/\'/gio;
s/[\w]*=/\$item{$&}=/gio;
s/\=}=/}=/gio;

#$item{PID}='21543';
#$item{TID}='528';
#$item{StTime}='20041114213000';
#$item{EdTime}='20041114220000';
#$item{ChName}='AT-X';
#$item{Count}='4';
#$item{StOffset}='0';
#$item{SubTitle}='������衢����ʥ��';
#$item{Title}='���Ȥ����ȡ��꡼ ŷ�ȤΤ��ä�';
#$item{ProgComment}='';
eval("$_");
Jcode::convert(\$item{Title},'euc');

$programtitlename = $item{Title};
$programtitlename =~ s/\&lt\;/</gi;
$programtitlename =~ s/\&gt\;/>/gi;
$programtitlename =~ s/\&amp\;/\&/gi;
$programtitle = $dbh->quote($programtitlename);

Jcode::convert(\$item{ChName},'euc');
Jcode::convert(\$item{SubTitle},'euc');

#$programSubTitle = $dbh->quote($item{SubTitle});
$programSubTitle = $item{SubTitle};
$programSubTitle =~ s/\&lt\;/</gi;
$programSubTitle =~ s/\&gt\;/>/gi;
$programSubTitle =~ s/\&amp\;/\&/gi;
$programSubTitle = $dbh->quote($programSubTitle);

$offsetmin = $item{StOffset}/60;
$edtime = &syobocaldate2foltiadate($item{EdTime});
$sttime = &syobocaldate2foltiadate($item{StTime});
$length = &calclength($sttime,$edtime);
$recstartdate = &calcoffsetdate($sttime ,$offsetmin );
$recenddate = &calcoffsetdate($edtime ,$offsetmin );

$stationid = &getstationid($item{ChName});
#���֥����ȥ��ɲ�-------------------------------------------------
#���Ȥ����뤫��ǧ
$DBQuery =  "SELECT count(*) FROM foltia_program WHERE tid = '$item{TID}'";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @titlecount= $sth->fetchrow_array;
 
 if ($titlecount[0] == 0){
#�ʤ�����ɲ�

#200412012359
$nomalstarttime = substr($sttime,8,4);
$DBQuery =  "insert into  foltia_program values ($item{TID},$programtitle,'','$nomalstarttime','$length','','','3','1','')";
# $sth = $dbh->prepare($DBQuery);
# $sth->execute();
$oserr = $dbh->do($DBQuery);
&writelog("getxml2db  ADD TV Progtam:$item{TID}:$programtitle");


}else{
#2006/2/26 
#���ä��顢�����ȥ��ǧ����
$DBQuery =  "SELECT title FROM foltia_program WHERE tid = '$item{TID}'";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @titlearray = $sth->fetchrow_array;
#�����ʤɤ���Ƥ���update
#print "$titlearray[0] / $programtitle\n";
 if ($titlearray[0] ne "$programtitlename" ){
 	$DBQuery =  "UPDATE  foltia_program  SET 	title = $programtitle where  tid = '$item{TID}' ";
#	  $sth = $dbh->prepare($DBQuery);
#	$sth->execute();
	$oserr = $dbh->do($DBQuery);
	&writelog("getxml2db  UPDATE TV Progtam:$item{TID}:$programtitle");
 }#end if update
}# end if TID


#PID�����뤫��ǧ
$DBQuery =  "SELECT count(*) FROM foltia_subtitle WHERE tid = '$item{TID}' AND pid =  '$item{PID}' ";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
 @subticount= $sth->fetchrow_array;
 if ($subticount[0]  >= 1){
	#PID���ä����񤭹���
#�����Ǥ���ʥ��顼�ФƤ�
#	DBD::Pg::st execute failed: ERROR:  invalid input syntax for type bigint: "" at /home/foltia/perl/getxml2db.pl line 147.
#UPDATE  foltia_subtitle  SET stationid = '42',countno = '8',subtitle = '���ԹԤ��ޤ�' ,startdatetime = '200503010035'  ,enddatetime = '200503010050',startoffset  = '0' ,lengthmin = '15' WHERE tid = '550' AND pid =  '26000' 
if ($item{Count} == ""){

	$DBQuery =  "UPDATE  foltia_subtitle  SET 
	stationid = '$stationid',
	countno =  null,
	subtitle = $programSubTitle ,
	startdatetime = '$recstartdate'  ,
	enddatetime = '$recenddate',
	startoffset  = '$offsetmin' ,
	lengthmin = '$length' 
	WHERE tid = '$item{TID}' AND pid =  '$item{PID}' ";

}else{

	$DBQuery =  "UPDATE  foltia_subtitle  SET 
	stationid = '$stationid',
	countno = '$item{Count}',
	subtitle = $programSubTitle ,
	startdatetime = '$recstartdate'  ,
	enddatetime = '$recenddate',
	startoffset  = '$offsetmin' ,
	lengthmin = '$length' 
	WHERE tid = '$item{TID}' AND pid =  '$item{PID}' ";
}
#		 $sth = $dbh->prepare($DBQuery);
#		$sth->execute();
	$oserr = $dbh->do($DBQuery);

 }else{
	#�ʤ�����ɲ�
	
	#���ä����������ϥ��ե��åȤ��줿����!
	#���Τޤޥ��塼���������
	if ($item{Count} eq ""){
	$DBQuery = "insert into foltia_subtitle values ( '$item{PID}','$item{TID}','$stationid',null,$programSubTitle,'$recstartdate','$recenddate','$offsetmin' ,'$length')";
	}else{
	$DBQuery = "insert into foltia_subtitle values ( '$item{PID}','$item{TID}','$stationid','$item{Count}',$programSubTitle,'$recstartdate','$recenddate','$offsetmin' ,'$length')";
	}
#		 $sth = $dbh->prepare($DBQuery);
#		$sth->execute();
	$oserr = $dbh->do($DBQuery);

}


#print "$DBQuery\n\n\n";
#debug 20050803
#&writelog("getxml2db $DBQuery");


}#if
}#foreach

#$oserr = $dbh->commit;
##	$dbh->disconnect();


