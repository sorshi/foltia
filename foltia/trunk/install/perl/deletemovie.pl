#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#
#deletemovie.pl
#
#�ե�����̾�������ꡢ��������򤹤�
#�Ȥꤢ������./mita/�ذ�ư
#
#
# DCC-JPL Japan/foltia project
#
#

$path = $0;
$path =~ s/deletemovie.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";

#�����������뤫?
$fname = $ARGV[0] ;
if ($fname eq "" ){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage;deletemovie.pl <FILENAME>\n";
	exit;
}

#�ե�����̾�����������å�
if ($fname =~ /.m2p\z/){

}else{
#	print "deletemovie invalid filetype.\n";
	&writelog("deletemovie invalid filetype:$fname.");
	exit (1);
}

#�ե�����¸�ߥ����å�

if (-e "$recfolderpath/$fname"){

}else{
#	print "deletemovie file not found.$recfolderpath/$fname\n";
	&writelog("deletemovie file not found:$fname.");
	exit (1);
}

#���ɺ������ 
if ($rapidfiledelete  > 0){ #./mita/�ذ�ư
	system ("mv $recfolderpath/$fname $recfolderpath/mita/");
	&writelog("deletemovie mv $recfolderpath/$fname $recfolderpath/mita/.");
}else{ #¨�����
	system ("rm $recfolderpath/$fname ");
	&writelog("deletemovie rm $recfolderpath/$fname ");


}



