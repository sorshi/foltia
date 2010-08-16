#!/usr/bin/perl
#
#
# Anime recording system foltia
# http://www.dcc-jpl.com/soft/foltia/
#
#
# �����ͥ륹�����
# ������󥹥ȡ�����˼�����ǽ�ɤ򥹥���󤷤ޤ�
#
# DCC-JPL Japan/foltia project
#

#use DBI;
#use DBD::Pg;
#use DBD::SQLite;
#use Schedule::At;
#use Time::Local;
#use Jcode;

#$path = $0;
#$path =~ s/channelscan.pl$//i;
#if ($path ne "./"){
#push( @INC, "$path");
#}

#require "foltialib.pl";

my $recpt1path = "/home/foltia/perl/tool/recpt1"; #�ۤ��Υ���ץ���ǥХ�����äƤ�ͤϥ������ѹ�
my $epgdumppath = "/home/foltia/perl/tool"; #epgdump�Τ���ǥ��쥯�ȥ�
my $recfolderpath = "/home/foltia/php/tv";#ts����Ϥ���ǥ��쥯�ȥ�
my $xmloutpath = "/tmp";
my $channel = 13 ; #�ϥǥ������ͥ��13-62
my $oserr = "";
my $line = "";

print "Initialize\n";
print "Tool path are\n";
print "REC:$recpt1path\n";
print "EPGDUMP:$epgdumppath/epgdump\n";
print "TS OUT:$recfolderpath/\n";
print "XML OUT:$xmloutpath/\n";

#�ġ��뤬���뤫��ǧ
unless (-e "$recpt1path"){
	print "Please install $recpt1path.\n";
	exit 1;
}
unless (-e "$epgdumppath/epgdump"){
	print "Please install $epgdumppath/epgdump.\n";
	exit 1;
}
unless (-e "$recfolderpath"){
	print "Please make directory $recfolderpath.\n";
	exit 1;
}
unless (-e "$xmloutpath"){
	print "Please make directory $xmloutpath.\n";
	exit 1;
}


#�ϥǥ��������롼��
for ($channel = 13; $channel <= 62 ; $channel++){
	print "\nChannel: $channel\n";
	$oserr = `$recpt1path $channel 4 $recfolderpath/__$channel.m2t`;
	$oserr = `$epgdumppath/epgdump $channel $recfolderpath/__$channel.m2t $xmloutpath/__$channel-epg.xml`;

	if (-s "$xmloutpath/__$channel-epg.xml" ){
		print "\t\t This channel can view :  $channel \n";
		open(XML, "< $xmloutpath/__$channel-epg.xml");
		while ( $line = <XML>) {
			#Jcode::convert(\$line,'euc','utf8');
			if($line =~ /<display-name/){
				$line =~ s/<.*?>//g;
				#Jcode::convert(\$line,'utf8','euc');
				print "\t\t $channel $line\n";
			}#end if
		}#end while
		close(XML);
	}else{
		print "\t\t Not Available :  $channel \n";
	}#end if 
}#end for


#BS�ǥ�����
$channel = 211;
	print "\nBS Digital Scan\n";
	$oserr = `$recpt1path $channel 4 $recfolderpath/__$channel.m2t`;
	$oserr = `$epgdumppath/epgdump /BS $recfolderpath/__$channel.m2t $xmloutpath/__$channel-epg.xml`;

	if (-s "$xmloutpath/__$channel-epg.xml" ){
		print "\t\t BS Digital can view :   \n";
		open(XML, "< $xmloutpath/__$channel-epg.xml");
		while ( $line = <XML>) {
			#Jcode::convert(\$line,'euc','utf8');
			if($line =~ /<display-name/){
				$line =~ s/<.*?>//g;
				#Jcode::convert(\$line,'utf8','euc');
				print "\t\t $line\n";
			}#end if
		}#end while
		close(XML);
	}else{
		print "\t\t Not Available :  BS Digital \n";
	}#end if 


#  <channel id="3001.ontvjapan.com">
#    <display-name lang="ja_JP">NHK BS1</display-name>
#  </channel>
#  <channel id="3002.ontvjapan.com">
#    <display-name lang="ja_JP">NHK BS2</display-name>
#  </channel>
#  <channel id="3003.ontvjapan.com">
#    <display-name lang="ja_JP">NHK BSh</display-name>
#  </channel>
#  <channel id="3004.ontvjapan.com">
#    <display-name lang="ja_JP">BS���ƥ�</display-name>
#  </channel>
#  <channel id="3005.ontvjapan.com">
#    <display-name lang="ja_JP">BSī��</display-name>
#  </channel>
#  <channel id="3006.ontvjapan.com">
#    <display-name lang="ja_JP">BS-TBS</display-name>
#  </channel>
#  <channel id="3007.ontvjapan.com">
#    <display-name lang="ja_JP">BS����ѥ�</display-name>
#  </channel>
#  <channel id="3008.ontvjapan.com">
#    <display-name lang="ja_JP">BS�ե�</display-name>
#  </channel>
#  <channel id="3009.ontvjapan.com">
#    <display-name lang="ja_JP">WOWOW</display-name>
#  </channel>
#  <channel id="3010.ontvjapan.com">
#    <display-name lang="ja_JP">WOWOW2</display-name>
#  </channel>
#  <channel id="3011.ontvjapan.com">
#    <display-name lang="ja_JP">WOWOW3</display-name>
#  </channel>
#  <channel id="3012.ontvjapan.com">
#    <display-name lang="ja_JP">�������������ͥ�</display-name>
#  </channel>
#  <channel id="3013.ontvjapan.com">
#    <display-name lang="ja_JP">BS11</display-name>
#  </channel>
#  <channel id="3014.ontvjapan.com">
#    <display-name lang="ja_JP">TwellV</display-name>
#  </channel>
#

#CS�ǥ�����
$channel = "CS8";
	print "\nCS Digital Scan\n";
	$oserr = `$recpt1path $channel 4 $recfolderpath/__$channel.m2t`;
	$oserr = `$epgdumppath/epgdump /CS $recfolderpath/__$channel.m2t $xmloutpath/__$channel-epg.xml`;

	if (-s "$xmloutpath/__$channel-epg.xml" ){
		print "\t\t CS Digital can view :   \n";
		open(XML, "< $xmloutpath/__$channel-epg.xml");
		while ( $line = <XML>) {
			#Jcode::convert(\$line,'euc','utf8');
			if($line =~ /<display-name/){
				$line =~ s/<.*?>//g;
				#Jcode::convert(\$line,'utf8','euc');
				print "\t\t $line\n";
			}#end if
		}#end while
		close(XML);
	}else{
		print "\t\t Not Available :  CS Digital \n";
	}#end if 

#  <channel id="1002.ontvjapan.com">
#    <display-name lang="ja_JP">���������ץ饹</display-name>
#  </channel>
#  <channel id="1086.ontvjapan.com">
#    <display-name lang="ja_JP">���ܱǲ�������ȣ�</display-name>
#  </channel>
#  <channel id="306ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">�ե��ƥ�ӣãӣȣ�</display-name>
#  </channel>
#  <channel id="1059.ontvjapan.com">
#    <display-name lang="ja_JP">����åץ����ͥ�</display-name>
#  </channel>
#  <channel id="1217.ontvjapan.com">
#    <display-name lang="ja_JP">�������ͥ�</display-name>
#  </channel>
#  <channel id="800ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���������ȣģ�����</display-name>
#  </channel>
#  <channel id="801ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">��������󣸣���</display-name>
#  </channel>
#  <channel id="802ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">��������󣸣���</display-name>
#  </channel>
#  <channel id="100ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">�売�ץ��</display-name>
#  </channel>
#  <channel id="194ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���󥿡�������ԣ�</display-name>
#  </channel>
#  <channel id="1025.ontvjapan.com">
#    <display-name lang="ja_JP">�ʥ��ݡ��ġ��ţӣУ�</display-name>
#  </channel>
#  <channel id="1016.ontvjapan.com">
#    <display-name lang="ja_JP">�ƣϣ�</display-name>
#  </channel>
#  <channel id="1018.ontvjapan.com">
#    <display-name lang="ja_JP">���ڡ��������ԣ�</display-name>
#  </channel>
#  <channel id="1046.ontvjapan.com">
#    <display-name lang="ja_JP">�����ȥ����󡡥ͥå�</display-name>
#  </channel>
#  <channel id="1213.ontvjapan.com">
#    <display-name lang="ja_JP">�ȥ����󡦥ǥ����ˡ�</display-name>
#  </channel>
#  <channel id="1010.ontvjapan.com">
#    <display-name lang="ja_JP">��ǥ����ͥ�</display-name>
#  </channel>
#  <channel id="1005.ontvjapan.com">
#    <display-name lang="ja_JP">�������</display-name>
#  </channel>
#  <channel id="1008.ontvjapan.com">
#    <display-name lang="ja_JP">�����ͥ�Σţã�</display-name>
#  </channel>
#  <channel id="1009.ontvjapan.com">
#    <display-name lang="ja_JP">�β�����ͥե���</display-name>
#  </channel>
#  <channel id="1003.ontvjapan.com">
#    <display-name lang="ja_JP">�����������饷�å�</display-name>
#  </channel>
#  <channel id="1133.ontvjapan.com">
#    <display-name lang="ja_JP">�������������ͥ�</display-name>
#  </channel>
#  <channel id="1006.ontvjapan.com">
#    <display-name lang="ja_JP">�����ѡ��ɥ��</display-name>
#  </channel>
#  <channel id="1014.ontvjapan.com">
#    <display-name lang="ja_JP">���أ�</display-name>
#  </channel>
#  <channel id="1204.ontvjapan.com">
#    <display-name lang="ja_JP">�ʥ��祸�������ͥ�</display-name>
#  </channel>
#  <channel id="110ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���ƥ�ݡ�����</display-name>
#  </channel>
#  <channel id="1028.ontvjapan.com">
#    <display-name lang="ja_JP">����ե����ͥ�</display-name>
#  </channel>
#  <channel id="1092.ontvjapan.com">
#    <display-name lang="ja_JP">�ƥ�ī�����ͥ�</display-name>
#  </channel>
#  <channel id="1019.ontvjapan.com">
#    <display-name lang="ja_JP">�ͣԣ�</display-name>
#  </channel>
#  <channel id="1024.ontvjapan.com">
#    <display-name lang="ja_JP">�ߥ塼���å�������</display-name>
#  </channel>
#  <channel id="1067.ontvjapan.com">
#    <display-name lang="ja_JP">ī���˥塼������</display-name>
#  </channel>
#  <channel id="1070.ontvjapan.com">
#    <display-name lang="ja_JP">�££å���</display-name>
#  </channel>
#  <channel id="1069.ontvjapan.com">
#    <display-name lang="ja_JP">�ãΣΣ�</display-name>
#  </channel>
#  <channel id="361ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���㥹�ȡ�����</display-name>
#  </channel>
#  <channel id="1041.ontvjapan.com">
#    <display-name lang="ja_JP">�ʥ��ݡ��ġ���</display-name>
#  </channel>
#  <channel id="1042.ontvjapan.com">
#    <display-name lang="ja_JP">�ʥ��ݡ��ġ���</display-name>
#  </channel>
#  <channel id="1043.ontvjapan.com">
#    <display-name lang="ja_JP">�ʥ��ݡ��ģУ�����</display-name>
#  </channel>
#  <channel id="1026.ontvjapan.com">
#    <display-name lang="ja_JP">�ǣ��ϣң�</display-name>
#  </channel>
#  <channel id="1040.ontvjapan.com">
#    <display-name lang="ja_JP">�����������ݡ��ġ�</display-name>
#  </channel>
#  <channel id="101ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���ͥץ������ͥ�</display-name>
#  </channel>
#  <channel id="1207.ontvjapan.com">
#    <display-name lang="ja_JP">�ӣˣ١��ӣԣ��ǣ�</display-name>
#  </channel>
#  <channel id="305ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">�����ͥ���</display-name>
#  </channel>
#  <channel id="1201.ontvjapan.com">
#    <display-name lang="ja_JP">����-��</display-name>
#  </channel>
#  <channel id="1050.ontvjapan.com">
#    <display-name lang="ja_JP">�ҥ��ȥ꡼�����ͥ�</display-name>
#  </channel>
#  <channel id="803ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">��������󣸣���</display-name>
#  </channel>
#  <channel id="804ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">��������󣸣���</display-name>
#  </channel>
#  <channel id="1007.ontvjapan.com">
#    <display-name lang="ja_JP">�ࡼ�ӡ��ץ饹�ȣ�</display-name>
#  </channel>
#  <channel id="1027.ontvjapan.com">
#    <display-name lang="ja_JP">����եͥåȥ��</display-name>
#  </channel>
#  <channel id="1074.ontvjapan.com">
#    <display-name lang="ja_JP">�̣�̣ᡡ�ȣ�</display-name>
#  </channel>
#  <channel id="1073.ontvjapan.com">
#    <display-name lang="ja_JP">�ե��ƥ�ӣ�����</display-name>
#  </channel>
#  <channel id="1072.ontvjapan.com">
#    <display-name lang="ja_JP">�ե��ƥ�ӣ�����</display-name>
#  </channel>
#  <channel id="1047.ontvjapan.com">
#    <display-name lang="ja_JP">���˥ޥå���</display-name>
#  </channel>
#  <channel id="1062.ontvjapan.com">
#    <display-name lang="ja_JP">�ǥ������Х꡼</display-name>
#  </channel>
#  <channel id="1193.ontvjapan.com">
#    <display-name lang="ja_JP">���˥ޥ�ץ�ͥå�</display-name>
#  </channel>
#  <channel id="160ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">��-�ԣ£ӥ����륫��</display-name>
#  </channel>
#  <channel id="1120.ontvjapan.com">
#    <display-name lang="ja_JP">�ѣ֣�</display-name>
#  </channel>
#  <channel id="185ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">�ץ饤�ࣳ�������ԣ�</display-name>
#  </channel>
#  <channel id="1015.ontvjapan.com">
#    <display-name lang="ja_JP">�ե��ߥ꡼���</display-name>
#  </channel>
#  <channel id="3201.ontvjapan.com">
#    <display-name lang="ja_JP">�ԣ£ӥ����ͥ�</display-name>
#  </channel>
#  <channel id="1090.ontvjapan.com">
#    <display-name lang="ja_JP">�ǥ����ˡ������ͥ�</display-name>
#  </channel>
#  <channel id="1022.ontvjapan.com">
#    <display-name lang="ja_JP">MUSIC ON! TV</display-name>
#  </channel>
#  <channel id="1045.ontvjapan.com">
#    <display-name lang="ja_JP">���å����ơ������</display-name>
#  </channel>
#  <channel id="1076.ontvjapan.com">
#    <display-name lang="ja_JP">�ԣ£ӥ˥塼���С���</display-name>
#  </channel>
#  <channel id="147ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">�ã��������ȥ�����</display-name>
#  </channel>
#  <channel id="1068.ontvjapan.com">
#    <display-name lang="ja_JP">���ƥ�ǡ�</display-name>
#  </channel>
#  <channel id="5004.ontvjapan.com">
#    <display-name lang="ja_JP">fashion TV</display-name>
#  </channel>
#  <channel id="300ch.epgdata.ontvjapan">
#    <display-name lang="ja_JP">���ƥ�ץ饹</display-name>
#  </channel>
#  <channel id="1023.ontvjapan.com">
#    <display-name lang="ja_JP">�����ߥ塼���å��ԣ�</display-name>
#  </channel>
#  <channel id="1208.ontvjapan.com">
#    <display-name lang="ja_JP">Music Japan TV</display-name>
#  </channel>
#  <channel id="2002.ontvjapan.com">
#    <display-name lang="ja_JP">���ƥ�Σţףӣ���</display-name>
#  </channel>


#CATV
# /home/foltia/perl/tool/recpt1 --b25 C13 10 /home/foltia/php/tv/__C13.m2t 
# /home/foltia/perl/tool/epgdump /CS /home/foltia/php/tv/__C13.m2t /tmp/__C13-epg.xml
