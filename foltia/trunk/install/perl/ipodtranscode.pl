#!/usr/bin/perl
#usage ipodtranscode.pl 
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
# iPod MPEG4/H.264�ȥ饳��
# ffmpeg��ƤӽФ����Ѵ�
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


# ��ŵ�ư�γ�ǧ!
$processes =  &processfind("ipodtranscode.pl");
#$processes = $processes +  &processfind("ffmpeg");

if ($processes > 1 ){
&writelog("ipodtranscode processes exist. exit:");
exit;
}else{
#&writelog("ipodtranscode.pl  Normal launch.");
}

#DB�����
	my $data_source = sprintf("dbi:%s:dbname=%s;host=%s;port=%d",
		$DBDriv,$DBName,$DBHost,$DBPort);
	 $dbh = DBI->connect($data_source,$DBUser,$DBPass) ||die $DBI::error;;

# �����ȥ����
#�ȥ饳��ե饰�����äƤ��ƥ��ơ�����50�ʾ�150̤���Υե������Ť���ˤҤȤ�õ��
# ��������
#$DBQuery =  "SELECT count(*) FROM foltia_subtitle, foltia_program, foltia_m2pfiles 
#WHERE filestatus >= $FILESTATUSRECEND AND filestatus < $FILESTATUSTRANSCODECOMPLETE  AND foltia_program.tid = foltia_subtitle.TID AND foltia_program.PSP = 1  AND foltia_m2pfiles.m2pfilename = foltia_subtitle.m2pfilename  ";
#$sth = $dbh->prepare($DBQuery);
#$sth->execute();
#@titlecount= $sth->fetchrow_array;
&writelog("ipodtranscode starting up.");

$counttranscodefiles = &counttranscodefiles();
if ($counttranscodefiles == 0){
	&writelog("ipodtranscode No MPEG2 files to transcode.");
	exit;
}
sleep 30;

while ($counttranscodefiles >= 1){

$DBQuery =  "SELECT foltia_subtitle.pid,foltia_subtitle.tid,foltia_subtitle.m2pfilename,filestatus,foltia_program.aspect ,foltia_subtitle.countno 
FROM foltia_subtitle, foltia_program, foltia_m2pfiles 
WHERE filestatus >= $FILESTATUSRECEND AND filestatus < $FILESTATUSTRANSCODECOMPLETE  AND foltia_program.tid = foltia_subtitle.TID AND foltia_program.PSP = 1  AND foltia_m2pfiles.m2pfilename = foltia_subtitle.m2pfilename 
ORDER BY enddatetime ASC 
LIMIT 1  ";

$sth = $dbh->prepare($DBQuery);
$sth->execute();
@dbparam = $sth->fetchrow_array;
#print "$dbparam[0],$dbparam[1],$dbparam[2],$dbparam[3],$dbparam[4],$dbparam[5]\n";
&writelog("ipodtranscode DEBUG $DBQuery");
&writelog("ipodtranscode DEBUG $dbparam[0],$dbparam[1],$dbparam[2],$dbparam[3],$dbparam[4],$dbparam[5]");
$pid = $dbparam[0];
$tid = $dbparam[1];
$inputmpeg2 = $recfolderpath."/".$dbparam[2]; # path�դ�
$mpeg2filename = $dbparam[2]; # path�ʤ�
$filestatus = $dbparam[3];
$aspect = $dbparam[4];# 16,1 (Ķ�۱�),4,3
$countno = $dbparam[5];
$mp4filenamestring = &mp4filenamestringbuild($pid);

&writelog("ipodtranscode DEBUG mp4filenamestring $mp4filenamestring");
#Ÿ���ǥ��쥯�ȥ����
$pspdirname = &makemp4dir($tid);
$mp4outdir = $pspdirname ;
# �ºݤΥȥ饳��
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

if ($filestatus <= $FILESTATUSRECEND){
}

if ($filestatus <= $FILESTATUSWAITINGCAPTURE){
#�ʤˤ⤷�ʤ�
}

if ($filestatus <= $FILESTATUSCAPTURE){
#unlink
# Starlight breaker��������ץ����������
if (-e "$toolpath/perl/captureimagemaker.pl"){
	&writelog("ipodtranscode Call captureimagemaker $mpeg2filename");
&changefilestatus($pid,$FILESTATUSCAPTURE);
	system ("$toolpath/perl/captureimagemaker.pl $mpeg2filename");
&changefilestatus($pid,$FILESTATUSCAPEND);
}
}

if ($filestatus <= $FILESTATUSCAPEND){
# ����ͥ�����
&makethumbnail();
&changefilestatus($pid,$FILESTATUSTHMCREATE);
}

if ($filestatus <= $FILESTATUSWAITINGTRANSCODE){
}

$filenamebody = $inputmpeg2 ;
$filenamebody =~ s/.m2t$|.ts$|.m2p$|.mpg$//gi;
#�ǥ����뤫���ʥ�����
if ($inputmpeg2 =~ /m2t$|ts$/i){
	#print "MPEG2-TS\n";


if ($filestatus <= $FILESTATUSTRANSCODETSSPLITTING){
		unlink("${filenamebody}_tss.m2t");
		unlink("${filenamebody}_HD.m2t");
}
if ($filestatus <= $FILESTATUSTRANSCODEFFMPEG){

	# H.264����
	$trcnmpegfile = $inputmpeg2 ;
	# �����ڥ�����
	if ($aspect == 1){#Ķ�۱�
	$cropopt = " -croptop 150 -cropbottom 150 -cropleft 200 -cropright 200 ";
	}elsif($aspect == 4){#SD 
	$cropopt = " -croptop 6 -cropbottom 6 -cropleft 8 -cropright 8 ";
	}else{#16:9
	$cropopt = " -croptop 6 -cropbottom 6 -cropleft 8 -cropright 8 ";
	}
	# ������ƥ����Ȥ�
	if (($trconqty eq "")||($trconqty == 1)){
	$ffmpegencopt = " -s 360x202 -deinterlace -r 24.00 -vcodec libx264 -g 300 -b 330000 -level 13 -loop 1 -sc_threshold 60 -partp4x4 1 -rc_eq 'blurCplx^(1-qComp)' -refs 3 -maxrate 700000 -async 50 -f h264 $filenamebody.264";
	}elsif($trconqty == 2){
	$ffmpegencopt = " -s 480x272 -deinterlace -r 29.97 -vcodec libx264 -g 300 -b 400000 -level 13 -loop 1 -sc_threshold 60 -partp4x4 1 -rc_eq 'blurCplx^(1-qComp)' -refs 3 -maxrate 700000 -async 50 -f h264 $filenamebody.264";
	}elsif($trconqty == 3){#640x352
	$ffmpegencopt = " -s 640x352 -deinterlace -r 29.97 -vcodec libx264 -g 100 -b 600000 -level 13 -loop 1 -sc_threshold 60 -partp4x4 1 -rc_eq 'blurCplx^(1-qComp)' -refs 3 -maxrate 700000 -async 50 -f h264 $filenamebody.264";
	}
	&changefilestatus($pid,$FILESTATUSTRANSCODEFFMPEG);
	&writelog("ipodtranscode ffmpeg $filenamebody.264");
	system ("ffmpeg -y -i $trcnmpegfile $cropopt $ffmpegencopt");
	
	#�⤷���顼�ˤʤä���TsSplit����
	if (! -e "$filenamebody.264"){
		&changefilestatus($pid,$FILESTATUSTRANSCODETSSPLITTING);
		unlink("${filenamebody}_tss.m2t");
		unlink("${filenamebody}_HD.m2t");
		if (-e "$toolpath/perl/tool/tss.py"){
		&writelog("ipodtranscode tss $inputmpeg2");
		system("$toolpath/perl/tool/tss.py $inputmpeg2");
		
		}else{
		# TsSplit
		&writelog("ipodtranscode TsSplitter $inputmpeg2");
		system("wine $toolpath/perl/tool/TsSplitter.exe  -EIT -ECM  -EMM -SD -1SEG -WAIT2 $inputmpeg2");
		}
		if(-e "${filenamebody}_tss.m2t"){
		$trcnmpegfile = "${filenamebody}_tss.m2t";
		}elsif (-e "${filenamebody}_HD.m2t"){
		$trcnmpegfile = "${filenamebody}_HD.m2t";
		}else{
		&writelog("ipodtranscode ERR NOT Exist ${filenamebody}_HD.m2t");
		$trcnmpegfile = inputmpeg2 ;
		}
		#��ffmpeg
		&changefilestatus($pid,$FILESTATUSTRANSCODEFFMPEG);
		&writelog("ipodtranscode ffmpeg retry $filenamebody.264");
		system ("ffmpeg -y -i $trcnmpegfile $cropopt $ffmpegencopt");
	}
	#�⤷���顼�ˤʤä���crop����
	if (! -e "$filenamebody.264"){
		#��ffmpeg
		&changefilestatus($pid,$FILESTATUSTRANSCODEFFMPEG);
		&writelog("ipodtranscode ffmpeg retry no crop $filenamebody.264");
		system ("ffmpeg -y -i $trcnmpegfile $ffmpegencopt");
	}
}
if ($filestatus <= $FILESTATUSTRANSCODEWAVE){
	# WAVE����
	unlink("${filenamebody}.wav");
	&changefilestatus($pid,$FILESTATUSTRANSCODEWAVE);
	&writelog("ipodtranscode mplayer $filenamebody.wav");
	system ("mplayer $trcnmpegfile -vc null -vo null -ao pcm:file=$filenamebody.wav:fast");

}
if ($filestatus <= $FILESTATUSTRANSCODEAAC){
	# AAC�Ѵ�
	unlink("${filenamebody}.aac");
	&changefilestatus($pid,$FILESTATUSTRANSCODEAAC);
	if (-e "$toolpath/perl/tool/neroAacEnc"){
		if (-e "$filenamebody.wav"){
	&writelog("ipodtranscode neroAacEnc $filenamebody.wav");
	system ("$toolpath/perl/tool/neroAacEnc -br 128000  -if $filenamebody.wav  -of $filenamebody.aac");
		}else{
		&writelog("ipodtranscode ERR Not Found $filenamebody.wav");
		}
	}else{
	#print "DEBUG $toolpath/perl/tool/neroAacEnc\n\n";
	&writelog("ipodtranscode faac $filenamebody.wav");
	system ("faac -b 128  -o $filenamebody.aac $filenamebody.wav ");
	}

}
if ($filestatus <= $FILESTATUSTRANSCODEMP4BOX){
	# MP4�ӥ��
	unlink("${filenamebody}.base.mp4");
	&changefilestatus($pid,$FILESTATUSTRANSCODEMP4BOX);
	&writelog("ipodtranscode MP4Box $filenamebody");
		system ("cd $recfolderpath ; MP4Box -fps 29.97 -add $filenamebody.264 -new $filenamebody.base.mp4");
#$exit_value = $? >> 8;
#$signal_num = $? & 127;
#$dumped_core = $? & 128; 
#&writelog("ipodtranscode DEBUG MP4Box -fps 29.97 -add:$exit_value:$signal_num:$dumped_core");

	if (-e "$filenamebody.base.mp4"){
	system ("cd $recfolderpath ; MP4Box -add $filenamebody.aac $filenamebody.base.mp4");
#$exit_value = $? >> 8;
#$signal_num = $? & 127;
#$dumped_core = $? & 128; 
#&writelog("ipodtranscode DEBUG MP4Box -add $filenamebody.aac:$exit_value:$signal_num:$dumped_core");
	}else{
	&writelog("ipodtranscode ERR File not exist.$filenamebody.base.mp4");
	}

}

if ($filestatus <= $FILESTATUSTRANSCODEATOM){
	unlink("${mp4outdir}MAQ${mp4filenamestring}.MP4");
	# iPod�إå��ղ�
	&changefilestatus($pid,$FILESTATUSTRANSCODEATOM);
	&writelog("ipodtranscode ATOM $filenamebody");
	#system ("/usr/local/bin/ffmpeg -y -i $filenamebody.base.mp4 -vcodec copy -acodec copy -f ipod ${mp4outdir}MAQ${mp4filenamestring}.MP4");
	system ("cd $recfolderpath ; MP4Box -ipod $filenamebody.base.mp4");
$exit_value = $? >> 8;
$signal_num = $? & 127;
$dumped_core = $? & 128;
&writelog("ipodtranscode DEBUG MP4Box -ipod:$exit_value:$signal_num:$dumped_core");
	system("mv $filenamebody.base.mp4 ${mp4outdir}MAQ${mp4filenamestring}.MP4");
	&writelog("ipodtranscode mv $filenamebody.base.mp4 ${mp4outdir}MAQ${mp4filenamestring}.MP4");
# ipodtranscode mv /home/foltia/php/tv/1329-21-20080829-0017.base.mp4 /home/foltia/php/tv/1329.localized/mp4/MAQ-/home/foltia/php/tv/1329-21-20080829-0017.MP4

}
if ($filestatus <= $FILESTATUSTRANSCODECOMPLETE){
	# ��֥ե�����ä�
	&changefilestatus($pid,$FILESTATUSTRANSCODECOMPLETE);
	unlink("${filenamebody}_HD.m2t");
	unlink("${filenamebody}_tss.m2t");
	unlink("$filenamebody.264");
	unlink("$filenamebody.wav");
	unlink("$filenamebody.aac");
	unlink("$filenamebody.base.mp4");
	
	&updatemp4file();

}

}else{ #�ǥ����뤫���ʥ�����
	#print "MPEG2\n";
	# �����ڥ�����
	if ($aspect == 16){
	$cropopt = " -croptop 70 -cropbottom 60 -cropleft  8 -cropright 14 -aspect 16:9 ";
	}else{
	$cropopt = " -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 ";
	}
# ������ƥ����Ȥ�
if (($trconqty eq "")||($trconqty == 1)){
#$encodeoption = "-y -i $inputmpeg2 -vcodec xvid $cropopt -s 320x240 -b 300 -bt 128 -r 14.985 -bufsize 192 -maxrate 512 -minrate 0 -deinterlace -acodec aac -ab 128 -ar 24000 -ac 2 $movietitle ${mp4outdir}M4V${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}M4V${mp4filenamestring}.MP4";
$encodeoption = "-y -i $inputmpeg2 vcodec libxvid $cropopt -s 320x240 -b 300 -bt 128 -r 14.985 -deinterlace -acodec libfaac -f ipod  ${mp4outdir}M4V${mp4filenamestring}.MP4";
#time ffmpeg -y  -i /home/foltia/php/tv/trcntest/nanoha-As-op.mpg -vcodec libxvid -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 -s 320x240 -b 300 -bt 128 -r 14.985 -deinterlace -acodec libfaac -f ipod M4V-Nanoha-As-OP.MP4
# 32sec
# 2.1MB
}elsif($trconqty == 2){ 
#$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt -s 320x240 -b 300 -r 24 -acodec aac -ar 32000 -ac 2 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";
$encodeoption = "-y -i $inputmpeg2 -vcodec libx264 -croptop 8 $cropopt -s 320x240 -b 300 -bt 128 -r 24 -deinterlace -acodec libfaac -f ipod  ${mp4outdir}MAQ${mp4filenamestring}.MP4";
#time ffmpeg -y  -i /home/foltia/php/tv/trcntest/nanoha-As-op.mpg -vcodec libx264 -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 -s 320x240 -b 300 -bt 128 -r 24 -deinterlace -acodec libfaac -f ipod MAQ-Nanoha-As-OP.MP4
# 2min22sec
# 6.4MB
}elsif($trconqty == 3){ 
#$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt  -acodec aac -ab 96 -vcodec h264  -maxrate 700 -minrate 0 -deinterlace -b 300 -ar 32000 -mbd 2 -coder 1 -cmp 2 -subcmp 2 -s 320x240 -r 30000/1001  -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";
$encodeoption = "-y -i $inputmpeg2  -vcodec libx264 $cropopt -s 320x240 -b 380 -bt 128 -r 29.97 -deinterlace -acodec libfaac -f ipod  ${mp4outdir}MAQ${mp4filenamestring}.MP4";
#time ffmpeg -y  -i /home/foltia/php/tv/trcntest/nanoha-As-op.mpg -vcodec libx264 -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 -s 320x240 -b 380 -bt 128 -r 29.97 -deinterlace -acodec libfaac -f ipod MAQ-Nanoha-As-OP.MP4
#  2m53.912s
# 7MB
}elsif($trconqty == 4){
#$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt -s 480x360 -b 400 -r 24 -acodec aac -ar 32000 -ac 2 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";
$encodeoption = "-y -i $inputmpeg2 -vcodec libx264 $cropopt -s 640x480 -b 500 -maxrate 700 -bt 128 -r 29.97 -deinterlace -acodec libfaac -f ipod ${mp4outdir}MAQ${mp4filenamestring}.MP4";
#time ffmpeg -y  -i /home/foltia/php/tv/trcntest/nanoha-As-op.mpg -vcodec libx264 -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 -s 640x480 -b 500  -maxrate 700 -bt 128 -r 29.97 -deinterlace -acodec libfaac -f ipod MAQ-Nanoha-As-OP.MP4
# 11m0.294s
# 20MB
}elsif($trconqty == 5){ 
#$encodeoption = "-y -i $inputmpeg2  -target ipod -profile 51 -level 30 $cropopt  -acodec aac -ab 96 -vcodec h264  -maxrate 700 -minrate 0 -deinterlace -b 400 -ar 32000 -mbd 2 -coder 1 -cmp 2 -subcmp 2 -s 480x360 -r 30000/1001  -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 $movietitle ${mp4outdir}MAQ${mp4filenamestring}.MP4";
$mp4file = "${mp4outdir}MAQ${mp4filenamestring}.MP4";
$encodeoption = "-y -i $inputmpeg2 -vcodec libx264 -croptop 8 $cropopt -s 640x480 -b 500  -maxrate 700 -bt 128 -r 29.97 -deinterlace -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8 -acodec libfaac -f ipod ${mp4outdir}MAQ${mp4filenamestring}.MP4";
#time ffmpeg -y  -i /home/foltia/php/tv/trcntest/nanoha-As-op.mpg -vcodec libx264 -croptop 8 -cropbottom 8 -cropleft  8 -cropright 14 -s 640x480 -b 500  -maxrate 700 -bt 128 -r 29.97 -deinterlace -flags loop -trellis 2 -partitions parti4x4+parti8x8+partp4x4+partp8x8+partb8x8  -acodec libfaac -f ipod MAQ-Nanoha-As-OP.MP4
#  14m14.033s
# 18MB
}

$encodeoptionlog = $encodeoption;
Jcode::convert(\$encodeoptionlog,'euc');

&writelog("ipodtranscode START QTY=$trconqty $encodeoptionlog");
#print "ffmpeg $encodeoptionlog \n";
&changefilestatus($pid,$FILESTATUSTRANSCODEFFMPEG);
system ("ffmpeg  $encodeoption ");
&writelog("ipodtranscode FFEND $inputmpeg2");
&changefilestatus($pid,$FILESTATUSTRANSCODECOMPLETE);
#�⤦�פ�ʤ��ʤä� #2008/11/14 
#&writelog("ipodtranscode mp4psp -p $mp4file $movietitleeuc");
#system("/usr/local/bin/mp4psp -p $mp4file '$movietitleeuc' ");
#&writelog("ipodtranscode mp4psp COMPLETE  $mp4file ");

&updatemp4file();
}#endif #�ǥ����뤫���ʥ�����

$counttranscodefiles = &counttranscodefiles();
############################
#���ǽ��餻��褦��
#exit;
}# end while
#�Ĥ�ե����뤬�����ʤ�
&writelog("ipodtranscode ALL COMPLETE");
exit;

#-----------------------------------------------------------------------
sub mp4filenamestringbuild(){
#�ե�����̾����
#1329-19-20080814-2337.m2t
my @mpegfilename = split(/\./,$dbparam[2]) ;
my $pspfilname = "-".$mpegfilename[0] ;
return("$pspfilname");
}#end sub mp4filenamestringbuild


sub makethumbnail(){
#����͡���
my $outputfilename = $inputmpeg2 ;#�ե�ѥ�
my $thmfilename = "MAQ${mp4filenamestring}.THM";
&writelog("ipodtranscode DEBUG thmfilename $thmfilename");

system ("mplayer -ss 00:01:20 -vo jpeg:outdir=$pspdirname -ao null -sstep 1 -frames 3  -v 3 $outputfilename");

&writelog("ipodtranscode DEBUG mplayer -ss 00:01:20 -vo jpeg:outdir=$pspdirname -ao null -sstep 1 -frames 3  -v 3 $outputfilename");

if (-e "$pspdirname/$thmfilename"){
$timestamp =`date "+%Y%m%d-%H%M%S"`;
chomp $timestamp;
	system("convert -crop 160x120+1+3 -resize 165x126\! $pspdirname/00000002.jpg $pspdirname/$thmfilename".$timestamp.".THM");
}else{
	system("convert -crop 160x120+1+3 -resize 165x126\! $pspdirname/00000002.jpg $pspdirname/$thmfilename");
}
&writelog("ipodtranscode DEBUG convert -crop 160x120+1+3 -resize 165x126\! $pspdirname/00000002.jpg $pspdirname/$thmfilename");

system("rm -rf $pspdirname/0000000*.jpg ");
&writelog("ipodtranscode DEBUG rm -rf $pspdirname/0000000*.jpg");

}#endsub makethumbnail

sub updatemp4file(){
my $mp4filename = "MAQ${mp4filenamestring}.MP4";

if (-e "${mp4outdir}MAQ${mp4filenamestring}.MP4"){
# MP4�ե�����̾��PID�쥳���ɤ˽񤭹���
	$DBQuery =  "UPDATE foltia_subtitle SET PSPfilename = '$mp4filename' WHERE pid = '$pid' ";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
&writelog("ipodtranscode UPDATEsubtitleDB $DBQuery");

# MP4�ե�����̾��foltia_mp4files����
	$DBQuery = "insert into foltia_mp4files values ('$tid','$mp4filename') ";
	 $sth = $dbh->prepare($DBQuery);
	$sth->execute();
&writelog("ipodtranscode UPDATEmp4DB $DBQuery");

&changefilestatus($pid,$FILESTATUSALLCOMPLETE);
}else{
&writelog("ipodtranscode ERR MP4 NOT EXIST $pid/$mp4filename");
}


}#updatemp4file

sub counttranscodefiles(){
my $DBQuery =  "SELECT count(*) FROM foltia_subtitle, foltia_program, foltia_m2pfiles 
WHERE filestatus >= $FILESTATUSRECEND AND filestatus < $FILESTATUSTRANSCODECOMPLETE  AND foltia_program.tid = foltia_subtitle.TID AND foltia_program.PSP = 1  AND foltia_m2pfiles.m2pfilename = foltia_subtitle.m2pfilename  ";
$sth = $dbh->prepare($DBQuery);
$sth->execute();
my @titlecount= $sth->fetchrow_array;

return ($titlecount[0]);


}