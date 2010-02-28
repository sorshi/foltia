#!/usr/bin/perl
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#tvrecording.pl
# record-v4l2.pl��ƤӤ���Ͽ��⥸�塼�롣
#
#usage tvrecording.pl ch length(sec) [clip No(000-)] [filename] [bitrate(5)] [TID] [NO] [/dev/video0]
#����
#ch :Ͽ������ͥ롡0����S���ϡ�-1���ȥ���ݥ��å����� [ɬ�ܹ���]
#length(sec) :Ͽ���ÿ� [ɬ�ܹ���]
#[sleeptype] :0��N N�ʤ饹�꡼�פʤ���Ͽ��
#[filename] :���ϥե�����̾
#[bitrate(5)] :�ӥåȥ졼�ȡ�Mbpsñ�̤ǻ���
#[TID] :����ܤ��륿���ȥ�ID
#[NO] :�������Ȥ������ÿ�
#[/dev/video0]:����ץ���ǥХ���
#
#
# DCC-JPL Japan/foltia project
#
#


#use Time::HiRes qw(usleep);


$path = $0;
$path =~ s/tvrecording.pl$//i;
if ($path ne "./"){
push( @INC, "$path");
}


##
#����ץ��㥫�����������
#cat /proc/interrupts  | grep ivtv |wc -l
# 11:    1054118          XT-PIC  uhci_hcd, eth0, ivtv0, ivtv1, ivtv2
#����

#tvConfig.pl -------------------------------
$extendrecendsec = 10;							#recording end second. 
#$startupsleeptime = 52;					#process wait(MAX60sec)
$startupsleeptime = 37;					#process wait(MAX60sec)


#-------------------------------

require 'foltialib.pl';

 &writelog("tvrecording:  DEBUG $ARGV[0] $ARGV[1] $ARGV[2] $ARGV[3] $ARGV[4] $ARGV[5] $ARGV[6]  $ARGV[7] ");

sub getChCallsign {
if ($ARGV[5]  ne ""){
	$recchname = $ARGV[5] ;
	}else{
	$recchname = $recch."ch";
}

}#endsub getChCallsign

sub getRecPath{ #capture directory define
	$recfolderpath = '/home/foltia/php/tv';		
}#end sub getRecPath
#
# -- ��������ᥤ�� ----------------------------
#����
&prepare;
#�⤷Ͽ�褬���äƤ��顢�ߤ��
$reclengthsec = &chkrecprocess();
&setbitrate;
&chkextinput;

$reclengthsec = $reclengthsec + $extendrecendsec ;

&callrecordv4l;

&writelog("tvrecording:$recch:$reclengthsec:$outputfile:$recdevice:$capturedeviceinputnum:$ivtvrecch:$stdbitrate:$peakbitrate");

# -- ����ʲ����֥롼���� ----------------------------
sub chkextinput{

if ($recch == 0){
		if ($svideoinputnum > -1 && $svideoinputnum < 30){
		$capturedeviceinputnum = $svideoinputnum ;
		}else{
		$capturedeviceinputnum = 7 ;
		}
	$capturedeviceinputName = "S-Video 1";
	$ivtvrecch = '';
}elsif($recch == -1){
		if ($comvideoinputnum > -1 && $comvideoinputnum < 30){
		$capturedeviceinputnum = $comvideoinputnum;
		}else{
		$capturedeviceinputnum = 8;
		}
	$capturedeviceinputName = "Composite 1";
	$ivtvrecch = '';
}else{
		if ($tunerinputnum > -1 && $tunerinputnum < 30){
		$capturedeviceinputnum = $tunerinputnum ;
		}else{
		$capturedeviceinputnum = 6 ;
		}
	$capturedeviceinputName = "Tuner 1";
	$ivtvrecch = $recch;
}
# 1-12ch��ntsc-bcast-jp
if($recch > 12){
	if ($uhfbandtype == 1){
	$frequencyTable = "ntsc-cable-jp";
	}else{
	$frequencyTable = "ntsc-bcast-jp";
	}
}else{
	$frequencyTable = "ntsc-bcast-jp";
}#if
	&writelog ("tvrecording DEBUG $frequencyTable $recch");

}#chkextinput



sub chkrecprocessOLD{
#�⤷Ͽ�褬���äƤ��顢�ߤ��
my $mencoderkillcmd = "";

$mencoderkillcmd =`/usr/sbin/lsof -Fp $recdevice`;
$mencoderkillcmd =~ s/p//;

if ($mencoderkillcmd != ""){
	#kill process
	$mencoderkillcmd  = "kill ".$mencoderkillcmd;
	system ($mencoderkillcmd);
	chomp($mencoderkillcmd);
	&writelog ("tvrecording Killed current recording process. process:$mencoderkillcmd");
		sleep(1);
		 my $videodevice =`/usr/sbin/lsof $recdevice`;

		while ($videodevice =~ /tvrecording/){

		$videodevice =`/usr/sbin/lsof $recdevice`;
		sleep(1);
		$sleepcounter++;
		$reclengthsec = $reclengthsec - $sleepcounter;
		&writelog ("tvrecording videodevice wait:$sleepcounter");
		}
		$sleepcounter = 0;		
}#if ($mencoderkillcmd != "")

return $reclengthsec;

}#end chkrecprocess

sub chkrecprocess{
my $mencoderkillcmd = "";
my $j = $recunits -1;
my $i = 0;
my $testrecdevice = "";
my @usedevices  ;
my @unusedevices;
my $n = 0;
$recdevice = "";
if ($ARGV[7]  ne ""){
	$recdevice =  $ARGV[7] ;
}

#for ($i = $j ;$i >= 0 ; $i--){
for ($i = 0 ;$i <= $j ; $i++){
#print "$i,$j\n";
$testrecdevice = "/dev/video$i";
$mencoderkillcmd =`/usr/sbin/lsof -Fp $testrecdevice`;
$mencoderkillcmd =~ s/p//;
if ($mencoderkillcmd != ""){
	push (@usedevices ,  $testrecdevice);
	&writelog ("tvrecording now using:$testrecdevice");
}else{
	push (@unusedevices ,  $testrecdevice);
	&writelog ("tvrecording unused:$testrecdevice");
}#if
}#for

$i = 0; #�����
$n = @unusedevices;
#�ǥХ������꤬���뤫?
if ($recdevice  ne ""){ #���꤬���ä���
#�������Ȥ��Ƥ��뤫�����å�
$mencoderkillcmd =`/usr/sbin/lsof -Fp $recdevice`;
$mencoderkillcmd =~ s/p//;
	if ($mencoderkillcmd != ""){ #�Ȥ��Ƥ���̵������Ȥ�
	$mencoderkillcmd  = "kill ".$mencoderkillcmd;
	system ($mencoderkillcmd);
	chomp($mencoderkillcmd);
	&writelog ("tvrecording Killed current recording process. $recdevice:$mencoderkillcmd");
		sleep(1);
	}
}else{
#�Ͼ���or ����ʤ��ʤ�
	if (($n == 0) and ($recch > 0)) {#�����ǥХ������ʤ��ơ��Ͼ��Ȥʤ�	
	$mencoderkillcmd =`/usr/sbin/lsof -Fp /dev/video$i`;#��$i
	$mencoderkillcmd =~ s/p//;
		if ($mencoderkillcmd != ""){ #�Ȥ��Ƥ���ǹ��/dev/video$j ��̵������Ȥ� �������$i
		$mencoderkillcmd  = "kill ".$mencoderkillcmd;
		system ($mencoderkillcmd);
		chomp($mencoderkillcmd);
		&writelog ("tvrecording Killed current recording process. /dev/video$i:$mencoderkillcmd");
			sleep(1);
		}
	$recdevice = "/dev/video$i"; #�������$i
		&writelog ("tvrecording select device:$recdevice");

}elsif ($recch <= 0) { # �������Ϥʤ�
	#�������Ϥ����ɥǥХ������ꤵ��Ƥ��ʤ��Ȥ���
	#��Ȥ�
	$mencoderkillcmd =`/usr/sbin/lsof -Fp /dev/video$j`;#
	$mencoderkillcmd =~ s/p//;
		if ($mencoderkillcmd != ""){ #�Ȥ��Ƥ���ǹ��/dev/video$j ��̵������Ȥ�
		$mencoderkillcmd  = "kill ".$mencoderkillcmd;
		system ($mencoderkillcmd);
		chomp($mencoderkillcmd);
		&writelog ("tvrecording Killed current recording process. /dev/video$j:$mencoderkillcmd");
			sleep(1);
		}
	$recdevice = "/dev/video$j"; #���������ϤϺǹ�̥ǥХ���
	}else{
	#������Ȥ�
	$recdevice = shift(@unusedevices );
	}#endif �����ǥХ����ʤ����

}#end if ���ꤢ�뤫

#�����ˤ�����Ƥ��ʤ��Ϥ��ʤΤ�?
if ($recdevice eq ""){
	$recdevice = "/dev/video0";
	&writelog ( "Rec Device un defined. / $recch ");
}
return $reclengthsec;

}#end chkrecprocessNew



sub prepare{

#�������顼����
$recch = $ARGV[0] ;
$reclengthsec = $ARGV[1];
if (($recch eq "" )|| ($reclengthsec eq "")){
	print "usage tvrecording.pl ch length(sec) [clip No(000-)] [filename] [bitrate(5)] [TID] [NO] [/dev/video0]\n";
	exit;
}
#1ʬ���˥ץ�����ư���뤫�������֥��꡼��
#srand(time ^ ($$ + ($$ << 15)));
#my $useconds  = int(rand(12000000));
#my $intval = int ($useconds  / 1000000);
#my $startupsleeptimemicro = ($startupsleeptime * 1000000) - $useconds;
#$reclengthsec = $reclengthsec + $intval + 1;
#&writelog("tvrecording:  DEBUG SLEEP $startupsleeptime:$useconds:$intval:$startupsleeptimemicro");
#	usleep ( $startupsleeptimemicro );

# $recch �ǥ�������Ĵ������ޤ��礦
#52
#my $intval = $recch % 50; # 0��49
#my $startupsleep = $startupsleeptime - $intval; #  3��52 (VHF 40-51)
#37
my $intval = $recch % 35; # 0��34
my $startupsleep = $startupsleeptime - $intval; #  3-37 (VHF 25-36,tvk 30)
$reclengthsec = $reclengthsec + (60 - $startupsleep) + 1; #

if ( $ARGV[2] ne "N"){
	&writelog("tvrecording: DEBUG SLEEP $startupsleeptime:$intval:$startupsleep:$reclengthsec");
	sleep ( $startupsleep);
}else{
	&writelog("tvrecording: DEBUG RAPID START");

}
if ($recunits > 1){
my $deviceno = $recunits - 1;#3�纹���ΤȤ�/dev/video2����Ȥ�
	$recdevice = "/dev/video$deviceno";
	$recch = $ARGV[0] ;
}else{
#1�纹��
	$recdevice = "/dev/video0";
	$recch = $ARGV[0] ;
}

&getChCallsign();
#&getRecPath;

$outputpath = "$recfolderpath"."/";

if ($ARGV[6] eq "0"){
	$outputfile = $outputpath.$ARGV[5]."--";
}else{
	$outputfile = $outputpath.$ARGV[5]."-".$ARGV[6]."-";
}
#2���ܰʹߤΥ���åפǥե�����̾���꤬���ä���
	if ($ARGV[3]  ne ""){
#		if ($ARGV[3] =~ /[0-9]{8}-[0-9]{4}/){
#		$outputfile .= "$ARGV[3]";
#		}else{
#		$outputfile .= strftime("%Y%m%d-%H%M", localtime(time + 60));
#		}
		$outputfile = $ARGV[3];
		$outputfile = &filenameinjectioncheck($outputfile);
		$outputfilewithoutpath = $outputfile ;
		$outputfile = $outputpath.$outputfile ;
#		$outputfile .= "$ARGV[3]";		
#		$outputfile .= strftime("%Y%m%d-%H%M", localtime(time + 60));
		&writelog("tvrecording:  DEBUG ARGV[2] ne null  \$outputfile $outputfile ");
	}else{
	$outputfile .= strftime("%Y%m%d-%H%M", localtime(time + 60));
		chomp($outputfile);
		$outputfile .= ".m2p";
		$outputfilewithoutpath = $outputfile ;
		&writelog("tvrecording:  DEBUG ARGV[2] is null  \$outputfile $outputfile ");
	}


@wday_name = ("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
$sleepcounter = 0;
$cmd="";

#���Ͽ��ʤɴ���Ʊ̾�ե����뤬���ä�������
if ( -e "$outputfile" ){
	if ( -s "$outputfile" ){
	&writelog("tvrecording :ABORT :recfile $outputfile exist.");
	exit 1;
	}
}

}#end prepare

sub setbitrate{
$bitrate = $ARGV[4] ;
$bitrate = $bitrate * 1024*1024;#Mbps -> bps
$peakbitrate = $bitrate + 350000;
$recordbitrate = "  --bitrate $bitrate --peakbitrate $peakbitrate ";
	$stdbitrate = "$bitrate";
	$peakbitrate = "$peakbitrate";
}#end setbitrate


sub callrecordv4l{

#$frequency = `ivtv-tune -d $recdevice -t $frequencyTable -c $ivtvrecch | awk '{print $2}'|tr -d .`;
my $ivtvtuneftype = '';
if ($frequencyTable eq "ntsc-cable-jp"){
	$ivtvtuneftype = 'japan-cable';
}else{
	$ivtvtuneftype = 'japan-bcast';
}
#print "ivtv-tune -d $recdevice -t $ivtvtuneftype -c $ivtvrecch\n";
&writelog("tvrecording DEBUG ivtv-tune -d $recdevice -t $ivtvtuneftype -c $ivtvrecch");
&writelog("tvrecording DEBUG $ENV{PATH}");

$frequency = `env PATH=PATH=/usr/kerberos/bin:/usr/lib/ccache:/usr/local/bin:/bin:/usr/bin:/home/foltia/bin ivtv-tune -d $recdevice -t $ivtvtuneftype -c $ivtvrecch`;
&writelog("tvrecording DEBUG frequency:$frequency");
@frequency = split(/\s/,$frequency);
$frequency[1] =~ s/\.//gi;
$frequency = $frequency[1] ;
&writelog("tvrecording DEBUG frequency:$frequency");

my $recordv4lcallstring = "$toolpath/perl/record-v4l2.pl --frequency $frequency --duration $reclengthsec --input $recdevice --directory $recfolderpath --inputnum $capturedeviceinputnum --inputname '$capturedeviceinputName' --freqtable $frequencyTable --bitrate $stdbitrate --peakbitrate $peakbitrate --output $outputfilewithoutpath ";

&writelog("tvrecording $recordv4lcallstring");
&writelog("tvrecording DEBUG $ENV{HOME}/.ivtvrc");
$oserr = `env HOME=$toolpath $recordv4lcallstring`;
&writelog("tvrecording DEBUG $oserr");

}#end callrecordv4l

