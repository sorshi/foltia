#!/usr/bin/perl
#usage captureimagemaker.pl  MPEG2filename
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#
# ����ץ�����������⥸�塼��
# recwrap.pl����ƤӽФ���롣
#
# DCC-JPL Japan/foltia project
#

$path = $0;
$path =~ s/captureimagemaker.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";

#$tid = $ARGV[0] ;
$filename = $ARGV[0] ;

# filename��������������å�
@filenametmp = split(/\./,$filename);
@filename = split(/-/,$filenametmp[0]);
$tid = $filename[0];

# tid�������Τߤ������å�
$tid =~ s/[^0-9]//ig;
#print "$tid\n";

if ($tid eq "" ){
	#�������ʤ��м¹Ԥ��줿�顢��λ
	print "usage captureimagemaker.pl  MPEG2filename\n";
	exit;
}

if ($tid >= 0){
#	print "TID is valid\n";
}else{
	&writelog("captureimagemaker TID invalid");
	exit;
}


$countno = $filename[1];
$countno =~ s/[^0-9]//ig;
#if ($countno eq "" ){
#$countno = "x";
#}
#	print "CNTNO:$countno\n";

$date = $filename[2];
$date =~ s/[^0-9]//ig;
if ($date eq "" ){
	$date =  `date  +%Y%m%d`
}
#	print "DATE:$date\n";


$time = $filename[3];
$time = substr($time, 0, 4);
$time =~ s/[^0-9]//ig;
if ($time eq "" ){
	$time =  `date  +%H%M`
}
#	print "TIME:$time\n";

#��Ͽ��ե����뤬���뤫�����å�
if (-e "$recfolderpath/$filename"){
#	print "EXIST $recfolderpath/$filename\n";
}else{
#	print "NO $recfolderpath/$filename\n";
	&writelog("captureimagemaker notexist $recfolderpath/$filename");

	exit;
}

# Ÿ����ǥ��쥯�ȥ꤬���뤫��ǧ

$capimgdirname = "$tid.localized/";
$capimgdirname = $recfolderpath."/".$capimgdirname;
#�ʤ���к��
unless (-e $capimgdirname ){
	system("$toolpath/perl/mklocalizeddir.pl $tid");
	&writelog("captureimagemaker mkdir $capimgdirname");
}
$capimgdirname = "$tid.localized/img";
$capimgdirname = $recfolderpath."/".$capimgdirname;
#�ʤ���к��
unless (-e $capimgdirname ){
	mkdir $capimgdirname ,0777;
	&writelog("captureimagemaker mkdir $capimgdirname");
}


# ����ץ��������ǥ��쥯�ȥ���� 
# $captureimgdir = "$tid"."-"."$countno"."-"."$date"."-"."$time";
$captureimgdir = $filename;
$captureimgdir =~ s/\.m2p$//; 

unless (-e "$capimgdirname/$captureimgdir"){
	mkdir "$capimgdirname/$captureimgdir" ,0777;
	&writelog("captureimagemaker mkdir $capimgdirname/$captureimgdir");

}

# �Ѵ�
#system ("mplayer -ss 00:00:10 -vo jpeg:outdir=$capimgdirname/$captureimgdir/ -vf crop=702:468:6:6,scale=160:120,pp=lb -ao null -sstep 14 -v 3 $recfolderpath/$filename");

#system ("mplayer -ss 00:00:10 -vo jpeg:outdir=$capimgdirname/$captureimgdir/ -vf crop=702:468:6:6,scale=160:120 -ao null -sstep 14 -v 3 $recfolderpath/$filename");


#��ETV�Ȥ��������뤫�麸�����⤦�������Ť��ڤ���
#system ("mplayer -ss 00:00:10 -vo jpeg:outdir=$capimgdirname/$captureimgdir/ -vf crop=690:460:12:10,scale=160:120 -ao null -sstep 14 -v 3 $recfolderpath/$filename");

#��10�ä��Ȥ�
system ("mplayer -ss 00:00:10 -vo jpeg:outdir=$capimgdirname/$captureimgdir/ -vf crop=690:460:12:10,scale=160:120 -ao null -sstep 9 -v 3 $recfolderpath/$filename");

