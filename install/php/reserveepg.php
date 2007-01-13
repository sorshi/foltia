<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

reserveepg.php

目的
EPG録画予約ページを表示します。

引数
epgid:EPG番組ID

 DCC-JPL Japan/foltia project

*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 

<?php

  include("./foltialib.php");
  
$epgid = getgetnumform(epgid);
		if ($epgid == "") {
		print "	<title>foltia:EPG予約:Error</title></head>\n";
		die_exit("登録番組がありません<BR>");
		}
print "	<title>foltia:EPG予約:$epgid</title>
</head>\n";


$con = m_connect();
$now = date("YmdHi");   

//タイトル取得
	$query = "
	SELECT epgid,startdatetime,enddatetime,lengthmin, ontvchannel,epgtitle,epgdesc,epgcategory , 
	stationname , stationrecch ,stationid 
	FROM foltia_epg , foltia_station 
	WHERE epgid='$epgid' AND foltia_station.ontvcode = foltia_epg.ontvchannel
	";//4812
	$rs = m_query($con, $query, "DBクエリに失敗しました");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		die_exit("登録番組がありません<BR>");
		}
		$rowdata = pg_fetch_row($rs, 0);
		//$title = htmlspecialchars($rowdata[0]);
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 
	printhtmlpageheader();
?>

  <p align="left"><font color="#494949" size="6">番組予約</font></p>
  <hr size="4">
EPGから下記番組を録画予約します。 <br>
<form name="recordingsetting" method="POST" action="reserveepgcomp.php">
<input type="submit" value="予約" >
<br>


<?php	
$stationjname = htmlspecialchars($rowdata[8]);
$startfoltime = htmlspecialchars($rowdata[1]);
$startprinttime =  foldate2print($startfoltime);
$endfoltime = htmlspecialchars($rowdata[2]);
$endprinttime = foldate2print($endfoltime);
$lengthmin = htmlspecialchars($rowdata[3]);
$recch = htmlspecialchars($rowdata[9]);
$progname = htmlspecialchars($rowdata[5]);
$progname = z2h($progname);
$progdesc = htmlspecialchars($rowdata[6]);
$progdesc =  z2h($progdesc);
$progcat = htmlspecialchars(z2h($rowdata[7]));

if ($progcat == "information"){
$progcat =  '情報';
}elseif ($progcat == "anime"){
$progcat =  'アニメ・特撮';
}elseif ($progcat == "news"){
$progcat =  'ニュース・報道';
}elseif ($progcat == "drama"){
$progcat =  'ドラマ';
}elseif ($progcat == "variety"){
$progcat =  'バラエティ';
}elseif ($progcat == "documentary"){
$progcat =  'ドキュメンタリー・教養';
}elseif ($progcat == "education"){
$progcat =  '教育';
}elseif ($progcat == "music"){
$progcat =  '音楽';
}elseif ($progcat == "cinema"){
$progcat =  '映画';
}elseif ($progcat == "hobby"){
$progcat =  '趣味・実用';
}elseif ($progcat == "kids"){
$progcat =  'キッズ';
}elseif ($progcat == "sports"){
$progcat =  'スポーツ';
}elseif ($progcat == "etc"){
$progcat =  'その他';
}elseif ($progcat == "stage"){
$progcat =  '演劇';
}

$epgid = $epgid ;
$stationid = htmlspecialchars($rowdata[10]);

if ($now > $endfoltime){
	print "この番組はすでに終了しているため、録画されません。<br>";
}elseif($now > $startfoltime){
	print "この番組はすでに放映開始しているため、録画されません。<br>";
}elseif($now > ($startfoltime - 10) ){
	print "この番組は放映直前なため、録画されない可能性があります。<br>";
}

//重複確認

	$query = "
SELECT  foltia_program.title,foltia_subtitle.tid,foltia_subtitle.pid 
FROM foltia_subtitle ,foltia_program ,foltia_tvrecord 
WHERE startdatetime ='$startfoltime' 
AND enddatetime = '$endfoltime' 
AND foltia_subtitle.stationid = '$stationid'  
AND foltia_program.tid = foltia_subtitle.tid 
AND foltia_tvrecord.tid =  foltia_program.tid 
AND foltia_tvrecord.stationid = foltia_subtitle.stationid 
";	
	
	$rs = m_query($con, $query, "DBクエリに失敗しました");
	$maxrows = pg_num_rows($rs);

		if ($maxrows == 0) {
		//重複なし
		}else{
		$chkoverwrap = pg_fetch_row($rs, 0);
		$prereservedtitle = htmlspecialchars($chkoverwrap[0]);
		$tid =  htmlspecialchars($chkoverwrap[1]);
		$pid =  htmlspecialchars($chkoverwrap[2]);
		print "<strong>この番組は既に予約済みです。</strong>　\n";
			if ($tid > 1){
			print "予約番組名:<a href=\"http://cal.syoboi.jp/tid/$tid/time/#$pid\" target=\"_blank\">$prereservedtitle</a><br>\n";
			}else{
			print "予約方法:EPG録画<br>\n";
			}
		}
		


print "<table width=\"100%\" border=\"0\">
    <tr><td>放送局</td><td>$stationjname</td></tr>
    <tr><td>放送開始</td><td>$startprinttime</td></tr>
    <tr><td>放送終了</td><td>$endprinttime</td></tr>
    <tr><td>尺(分)</td><td>$lengthmin</td></tr>
    <tr><td>放送チャンネル</td><td>$recch</td></tr>
    <tr><td>番組名</td><td>$progname</td></tr>
    <tr><td>内容</td><td>$progdesc</td></tr>
    <tr><td>ジャンル</td><td>$progcat</td></tr>
    <tr><td>番組ID</td><td>$epgid</td></tr>
    <tr><td>局コード</td><td>$stationid</td></tr>
	
</table>

<input type=\"hidden\" name=\"stationid\" value=\"$stationid\" />
<input type=\"hidden\" name=\"subtitle\" value=\"$progname $progdesc\" />
<input type=\"hidden\" name=\"startdatetime\" value=\"$startfoltime\" />
<input type=\"hidden\" name=\"enddatetime\" value=\"$endfoltime\" />
<input type=\"hidden\" name=\"lengthmin\" value=\"$lengthmin\" />

";

    
?>

</FORM>


</body>
</html>
