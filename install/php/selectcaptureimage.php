<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/


目的
blogツール、スターライトブレイカー用キャプ選択画面サンプル

引数
pid:PID

mplayer -ss 00:00:10 -vo jpeg:outdir=/home/foltia/php/tv/691.localized/img/6/ -vf crop=702:468:6:6,scale=160:120,pp=lb  -ao null -sstep 14  -v 3 /home/foltia/php/tv/691-6-20060216-0130.m2p

旧仕様
mplayer -ss 00:0:10 -vo jpeg:outdir=./ -ao null -sstep 14  -v 3 ../665-13-20051230-0145.m2p 
convert -verbose  -crop 160x120+1+3 -resize 165x126\!  ../*.jpg 

 DCC-JPL Japan/foltia project

*/

include("./foltialib.php");

//$tid = getgetnumform(tid);
//$path = getgetform(d);
$pid = getgetform(pid);

if ($pid == "") {
	header("Status: 404 Not Found",TRUE,404);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>Starlight Breaker -キャプチャ画像選択</title>
<script src="http://images.apple.com/main/js/ac_quicktime.js" language="JavaScript" type="text/javascript"></script>
</head>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">

<?php
printhtmlpageheader();

if ($pid == "") {
	print "画像がありません。<br></body></html>";
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
	$rowdata = pg_fetch_row($rs, 0);

print "  <p align=\"left\"><font color=\"#494949\" size=\"6\">キャプチャ画像</font></p>
  <hr size=\"4\">
<p align=\"left\">";
print "<a href = \"http://cal.syoboi.jp/tid/$rowdata[0]/time#$pid\" target=\"_blank\">";
print htmlspecialchars($rowdata[2]) . "</a> " ;
print htmlspecialchars($rowdata[3]) . " ";
print htmlspecialchars($rowdata[4]) . " ";
print htmlspecialchars($rowdata[1]) . " ";
print htmlspecialchars($rowdata[6]) . "分 ";
print htmlspecialchars(foldate2print($rowdata[5]));

$mp4filename = $rowdata[9];
$tid = $rowdata[0];
$serverfqdn = getserverfqdn();

print "　　再生:<A HREF=\"$httpmediamappath/$tid.localized/mp4/$mp4filename\" target=\"_blank\">$mp4filename</A> / <script language=\"JavaScript\" type=\"text/javascript\">QT_WriteOBJECT_XHTML('http://g.hatena.ne.jp/images/podcasting.gif','16','16','','controller','FALSE','href','http://$serverfqdn/$httpmediamappath/$tid.localized/mp4/$mp4filename','target','QuickTimePlayer','type','video/mp4');</script><br>";

$m2pfilename = $rowdata[8];

list($tid,$countno,$date,$time)= split ("-", $m2pfilename );
	$tid = ereg_replace("[^0-9]", "", $tid);
//if ($countno == "x"){
//}else{
//	$countno = ereg_replace("[^0-9]", "", $countno);
//}
//	$date = ereg_replace("[^0-9]", "", $date);
//	$time = ereg_replace("[^0-9]", "", $time);
//$path = $tid."-".$countno."-".$date."-".$time ;
$path = ereg_replace("\.m2p$", "", $m2pfilename);
$serveruri = getserverfqdn ();

exec ("ls   $recfolderpath/$tid.localized/img/$path/", $tids);
//$timecount = 1;
foreach($tids as $filetid) {
print "<IMG SRC='http://$serveruri$httpmediamappath/$tid.localized/img/$path/$filetid' WIDTH='160' HEIGHT='120'  ALT='$tid:$countno:$filetid'>\n";
/*
$i++ ;
if ($i > 3){
 print "<br>\n";
$i = 0;
//$timecount++ ;
}
*/
}//foreach
// タイトル一覧　ここまで

// ファイル一覧　ここから
?>




</body>
</html>
