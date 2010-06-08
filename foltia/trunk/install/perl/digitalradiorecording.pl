#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#digitalradiorecording.pl
# IP�����ޥ�饸����radiko�פ�Ͽ�����롣
#
#usage digitalradiorecording.pl stationname length(sec) filename
#����
#stationname : radiko�λȤ��ʼ��̻� ��:ʸ������ QRR  [ɬ�ܹ���]
#length(sec) :Ͽ���ÿ� [ɬ�ܹ���]
#filename :���ϥե�����̾ [ɬ�ܹ���]
#
# DCC-JPL Japan/foltia project
#
#

$path = $0;
$path =~ s/digitalradiorecording.pl$//i;
if ($path ne "./"){
push( @INC, "$path");
}

#tvConfig.pl -------------------------------
$extendrecendsec = 10;							#recording end second. 
#$startupsleeptime = 52;					#process wait(MAX60sec)
$startupsleeptime = 57;					#process wait(MAX60sec)
#-------------------------------

require 'foltialib.pl';

# &writelog("digitalradiorecording.pl: DEBUG $ARGV[0] $ARGV[1] ");


#����
&prepare;


&calldigitalrecorder;

# &writelog("digitaldigitalradiorecording:RECEND:$bandtype $recch $lengthsec $stationid $sleeptype $filename $tid $countno $unittype");

# -- ����ʲ����֥롼���� ----------------------------


sub prepare{

#�������顼����
$stationname = $ARGV[0] ;
$lengthsec = $ARGV[1] ;
$filename = $ARGV[2] ;


if (($stationname eq "" ) || ($lengthsec eq "") || ($filename eq "")){
	print "usage digitalradiorecording.pl stationname length(sec) filename\n";
	exit;
}

#my $intval = $recch % 10; # 0��9 sec
my $intval = 0;
my $startupsleep = $startupsleeptime - $intval; #  18��27 sec
$reclengthsec = $lengthsec + (60 - $startupsleep) + 10; #

if ( $sleeptype ne "N"){
	&writelog("digitalradiorecording: DEBUG SLEEP $startupsleeptime:$intval:$startupsleep:$reclengthsec");
	sleep ( $startupsleep);
	#2008/08/12_06:39:00 digitalradiorecording: DEBUG SLEEP 17:23:-6:367
}else{
	&writelog("digitalradiorecording: DEBUG RAPID START");
}

$outputpath = "$recfolderpath"."/";

if ($countno eq "0"){
	$outputfile = $outputpath.$tid."--";
}else{
	$outputfile = $outputpath.$tid."-".$countno."-";
}
#2���ܰʹߤΥ���åפǥե�����̾���꤬���ä���
	if ($filename  ne ""){

		$outputfile = $filename ;
		$outputfile = &filenameinjectioncheck($outputfile);
		$outputfilewithoutpath = $outputfile ;
		$outputfile = $outputpath.$outputfile ;
		&writelog("digitalradiorecording: DEBUG FILENAME ne null \$outputfile $outputfile ");
	}else{
	$outputfile .= strftime("%Y%m%d-%H%M", localtime(time + 60));
		chomp($outputfile);
		$outputfile .= ".aac";
		$outputfilewithoutpath = $outputfile ;
		&writelog("digitalradiorecording:  DEBUG FILENAME is null \$outputfile $outputfile ");
	}


@wday_name = ("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
$sleepcounter = 0;
$cmd="";

#���Ͽ��ʤɴ���Ʊ̾�ե����뤬���ä�������
if ( -e "$outputfile" ){
	if ( -s "$outputfile" ){
	&writelog("digitalradiorecording :ABORT :recfile $outputfile exist.");
	exit 1;
	}
}

}#end prepare



sub calldigitalrecorder{

#if  (-e "$toolpath/perl/tool/ffmpeg"){
#2010/4/7 radiko���к����줿�Τ�ffmpegľ�ܼ����Ǥ��ʤ��ʤä�
#./ffmpeg -i rtmp://radiko.smartstream.ne.jp:1935/QRR/_defInst_/simul-stream -t 180 -acodec copy ~/php/tv/qrr.aac
#&writelog("digitalradiorecording :DEBUG :$toolpath/perl/tool/ffmpeg -y -i rtmp://radiko.smartstream.ne.jp:1935/$stationname/_defInst_/simul-stream -t $reclengthsec -acodec copy $outputfile.");
#system("$toolpath/perl/tool/ffmpeg -y -i rtmp://radiko.smartstream.ne.jp:1935/$stationname/_defInst_/simul-stream -t $reclengthsec -acodec copy $outputfile");

if  (-e "$toolpath/perl/tool/rtmpdump"){
#./rtmpdump -y "simul-stream" -n "radiko.smartstream.ne.jp" -c 1935  -p "http://radiko.jp/player/player.html#QRR" -a "QRR/_defInst_" -f "WIN 10,0,45,2" -v -B 180 -o joqr.flv

&writelog("digitalradiorecording :DEBUG :$toolpath/perl/tool/rtmpdump  -r \"rtmpe://radiko.smartstream.ne.jp:1935/$stationname/_defInst_/simul-stream\" -s \"http://radiko.jp/player/player.html#${stationname}\" -f \"WIN 10,0,45,2\" -v -B $reclengthsec -o ${outputfile}.flv");

#system("$toolpath/perl/tool/rtmpdump -y \"simul-stream\" -n \"radiko.smartstream.ne.jp\" -c 1935  -p \"http://radiko.jp/player/player.html#${stationname}\" -a \"$stationname/_defInst_\" -f \"WIN 10,0,45,2\" -v -B $reclengthsec -o ${outputfile}.flv");
system("$toolpath/perl/tool/rtmpdump  -r \"rtmpe://radiko.smartstream.ne.jp:1935/$stationname/_defInst_/simul-stream\" -s \"http://radiko.jp/player/player.html#${stationname}\" -f \"WIN 10,0,45,2\" -v -B $reclengthsec -o ${outputfile}.flv");

&writelog("digitalradiorecording :DEBUG :ffmpeg -y -i ${outputfile}.flv -vn -acodec copy $outputfile");

system("ffmpeg -y -i ${outputfile}.flv -vn -acodec copy $outputfile");

unlink("${outputfile}.flv");
}else{
	&writelog("digitalradiorecording :ABORT :File not found,recordable ffmpeg on $toolpath/perl/tool/ffmpeg. Show http://d.hatena.ne.jp/nazodane/20100315/1268646192 ");
	exit 1;
}



}# end sub calldigitalrecorder


