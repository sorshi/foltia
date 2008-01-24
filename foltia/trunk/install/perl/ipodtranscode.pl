#!/usr/bin/perl
#usage ipodtranscode.pl /path/to/mpeg2.m2p mp4filenamestring /path/to/mpeg2/tid.localized/mp4/ PID [aspect]
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#
# iPod MPEG4/H.264�ȥ饳��
# ffmpeg��ƤӽФ����Ѵ�
# ffmpeg��iPod�ѥå������Ǥ����ꤷ�Ƥ���
# ffmpeg�κ������
# http://www.dcc-jpl.com/diary/ddata2006/02A.html#20060215-00
#
# DCC-JPL Japan/foltia project
#

use DBI;
use DBD::Pg;
use Jcode;


$path = $0;
$path =~ s/ipodtranscode.pl$//i;
if ($pwd  ne "./"){
push( @INC, "$path");
}

require "foltialib.pl";
#�����������뤫?
$recch = $ARGV[0] ;
if ($recch eq "" ){
	#�������ʤ��Ǽ¹Ԥ��줿�顢��λ
	print "usage ipodtranscode.pl /path/to/mpeg2.m2p mp4filenamestring /path/to/mpeg2/tid.localized/mp4/ PID [aspect]\n";
	exit;
}

$inputmpeg2 = $ARGV[0]; 
$mp4filenamestring = $ARGV[1];
$mp4outdir = $ARGV[2];
$pid = $ARGV[3];
$aspect = $ARGV[4];

#DB�����
	my $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);
	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;


# �����ȥ����
if ($pid ne ""){

$DBQuery =  "SELECT title , countno , subtitle  
FROM  foltia_program, foltia_subtitle 
WHERE foltia_program.tid = foltia_subtitle.tid 
AND foltia_subtitle.pid = $pid ";
$sth = $dbh->prepare($DBQuery);
$sth->execute();
@programtitle = $sth->fetchrow_array;
$programtitle[0] =~ s/\"/\\"/gi;
$programtitle[2] =~ s/\"/\\"/gi;

	if ($pid > 0){
		if ($programtitle[1] ne ""){
			$movietitle = " -title \"$programtitle[0] ��$programtitle[1]�� $programtitle[2]\" ";
			$movietitleeuc = " -t \"$programtitle[0] ��$programtitle[1]�� $programtitle[2]\" ";
		}else{
			$movietitle = " -title \"$programtitle[0] $programtitle[2]\" ";
			$movietitleeuc = " -t \"$programtitle[0] $programtitle[2]\" ";
		}
	}elsif($pid < 0){
	#EPG
		$movietitle = " -title \"$programtitle[2]\" ";
		$movietitleeuc = " -t \"$programtitle[2]\" ";
	}else{# 0
	#����
	$movietitle = "";
	$movietitleeuc = "";
	}
#Jcode::convert(\$movietitle,'utf8');# Title������iTunes7.0.2������å��夹��
	$movietitle = "";
	$movietitleeuc = "";

}
# �����ڥ�����
if ($aspect == 16){
$cropopt = " -croptop 70 -cropbottom 60 -cropleft  8 -cropright 14 -aspect 1.7777 ";
}else{
$cropopt = " -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 ";
}
# ������ƥ����Ȥ�
if (($trconqty eq "")||($trconqty == 1)){#sample:src 106.6sec encode 82sec x0.77 382kbps @Celeron2.6GHz

$encodeoption = "-y -i $inputmpeg2 -vcodec xvid $cropopt -s 320x240 -b 300 -bt 128 -r 14.985 -bufsize 192 -maxrate 512 -minrate 0 -deinterlace -acodec aac -ab 128 -ar 24000 -ac 2 $movietitle ${mp4outdir}M4V${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}M4V${mp4filenamestring}.MP4";

}elsif($trconqty == 2){ #sample:src 106.6sec encode 117sec x1.1 597kbps @Celeron2.6GHz

$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt -s 320x240 -b 300 -r 24 -acodec aac -ar 32000 -ac 2 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";

}elsif($trconqty == 3){ #sample:src 106.6sec encode 364sec x3.4 528kbps @Celeron2.6GHz

$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt  -acodec aac -ab 96 -vcodec h264  -maxrate 700 -minrate 0 -deinterlace -b 300 -ar 32000 -mbd 2 -coder 1 -cmp 2 -subcmp 2 -s 320x240 -r 30000/1001  -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";

}elsif($trconqty == 4){ #sample:src 106.6sec encode 239sec x2.24 1036kbps @Celeron2.6GHz

$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt -s 480x360 -b 400 -r 24 -acodec aac -ar 32000 -ac 2 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";

}elsif($trconqty == 5){ #sample:src 106.6sec encode 1012sec x9.49 727kbps @Celeron2.6GHz

$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt  -acodec aac -ab 96 -vcodec h264  -maxrate 700 -minrate 0 -deinterlace -b 400 -ar 32000 -mbd 2 -coder 1 -cmp 2 -subcmp 2 -s 480x360 -r 30000/1001  -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";
}



$encodeoptionlog = $encodeoption;
Jcode::convert(\$encodeoptionlog,'euc');

&writelog("ipodtranscode START QTY=$trconqty $encodeoptionlog");
#print "ffmpeg $encodeoptionlog \n";
system ("/usr/local/bin/ffmpeg  $encodeoption ");
&writelog("ipodtranscode FFEND $inputmpeg2");

&writelog("ipodtranscode mp4psp -p $mp4file $movietitleeuc");
system("/usr/local/bin/mp4psp -p $mp4file '$movietitleeuc' ");
&writelog("ipodtranscode mp4psp COMPLETE  $mp4file ");

