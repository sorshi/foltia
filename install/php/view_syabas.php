<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/


目的
MediaWiz,LinkPlayer をはじめとする Syabasアーキテクチャのネットワークメディアプレーヤでの再生

引数
pid:PID

出力

<Title>|0|0|http://servername/foltia/tv/filename.m2p|

参考URL
http://www.geocities.co.jp/SiliconValley-Cupertino/2647/tec.html

*/

include("./foltialib.php");

$pid = getgetform(pid);

if ($pid == "") {
		exit;
}

?>


<?php
if ($pid == "") {
	exit;
}


$con = m_connect();
$query = "
SELECT 
foltia_program.tid,
stationname,
foltia_program.title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin  , 
foltia_subtitle.pid ,
foltia_subtitle.m2pfilename , 
foltia_subtitle.pspfilename 
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.pid = '$pid'  
 
	";
	$rs = m_query($con, $query, "DBクエリに失敗しました");

$rows = pg_num_rows($rs);
if ($rows == 0){
	print "";
	exit;
}else{
$rowdata = pg_fetch_row($rs, 0);

$title = $rowdata[2];
$episode = $rowdata[3];
$subtitle = $rowdata[4];
$m2pfilename = $rowdata[8];
$serveruri = getserveruri();
}

print "\n";
print "\n";

print "$title";
print " 第". "$episode" . "話 ";
print "$subtitle";
print "|0|0|";
print "http://$serveruri$httpmediamappath/$m2pfilename";
print "|";

?>



