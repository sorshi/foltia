#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#folprep.pl
#
#at����ƤФ�ơ���Ū���Ȥ�����Ƥ��ʤ�����ǧ���ޤ�
#���������ǻ��郎15ʬ�ʾ���ʤ����folprep�Υ��塼������ޤ�
#���ǻ��郎15ʬ����ʤ����ǻ����Ͽ�襭�塼������ޤ�
#
#����:PID
#
# DCC-JPL Japan/foltia project
#
#
use DBI;
use DBD::Pg;
use Schedule::At;
use Time::Local;


$path = $0;
$path =~ s/folprep.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";

#XML���å� & DB����
system("$toolpath/perl/getxml2db.pl");

#�����������뤫?
$pid = $ARGV[0] ;
if ($pid eq "" ){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage;folprep.pl <PID>\n";
	exit;
}

#PIDõ��
$pid = $ARGV[0];

#���塼������
	&writelog("folprep  $toolpath/perl/addpidatq.pl $pid");
system("$toolpath/perl/addpidatq.pl $pid");

