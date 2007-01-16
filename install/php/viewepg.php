<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

viewepg.php

��Ū
����Ͽ��ͽ��ڡ�����ɽ�����ޤ���

���ץ����
start:ɽ�������ॹ�����(Ex.200512281558)
����ά�������߻��

 DCC-JPL Japan/foltia project

*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>foltia:EPG</title>
</head>
<?php
include("./foltialib.php");
  
$con = m_connect();
$start = getgetnumform(start);

if ($start == ""){
	$start =  date("YmdHi");
}else{
  $start = ereg_replace( "[^0-9]", "", $start); 
}
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">EPG����ɽ</font></p>
  <hr size="4">
<p align="left">EPG����ɽ��ɽ�����ޤ���
<?php 

$startyear =   substr($start,0,4);
$startmonth =   substr($start,4,2);
$startday =   substr($start,6,2);
$starthour =   substr($start,8,2);
$startmin =   substr($start,10,2);
print "($startyear/$startmonth/$startday $starthour:$startmin-)<BR>\n";

$yesterday = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday -1 , $startyear));
$today0400 = date ("YmdHi",mktime(4 , 0 , 0, $startmonth , $startday  , $startyear));
$today1200 = date ("YmdHi",mktime(12 , 0 , 0, $startmonth , $startday , $startyear));
$today2000 = date ("YmdHi",mktime(20 , 0 , 0, $startmonth , $startday , $startyear));
$day1after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +1 , $startyear));
$day1 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +1 , $startyear));
$day2after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +2 , $startyear));
$day2 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +2 , $startyear));
$day3after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +3 , $startyear));
$day3 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +3 , $startyear));
$day4after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +4 , $startyear));
$day4 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +4 , $startyear));
$day5after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +5 , $startyear));
$day5 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +5 , $startyear));
$day6after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +6 , $startyear));
$day6 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +6 , $startyear));
$day7after = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +7 , $startyear));
$day7 = date ("m/d",mktime($starthour , 0 , 0, $startmonth , $startday +7 , $startyear));



//ɽ��������
// $page = 1 ~ 
$maxdisplay = 8;

	$query = "SELECT stationid, stationname, stationrecch, ontvcode FROM foltia_station WHERE \"ontvcode\" ~~ '%ontvjapan%' 
	";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);

if ($maxrows > $maxdisplay){
	$pages = ceil($maxrows / $maxdisplay) ;
}

$page = getgetnumform(p);

if (($page == "")|| ($page <= 0) ){
	$page = 1 ;
	$offset = 0  ;
}else{
  $page = ereg_replace( "[^0-9]", "", $page); 
  if ($page > $pages){
  	$page = $pages ;
  }elseif ($page <= 0) {
  $page = 1 ;
  }
  $offset = ($page * $maxdisplay ) - $maxdisplay;
}


print "��<A HREF=\"./viewepg.php?p=$page&start=$yesterday\">������</A>��<A HREF=\"./viewepg.php\">����</A>������(<A HREF=\"./viewepg.php?p=$page&start=$today0400\">4:00</A>��<A HREF=\"./viewepg.php?p=$page&start=$today1200\">12:00</A>��<A HREF=\"./viewepg.php?p=$page&start=$today2000\">20:00</A>)��<A HREF=\"./viewepg.php?p=$page&start=$day1after\">������</A>��<A HREF=\"./viewepg.php?p=$page&start=$day2after\">$day2</A>��<A HREF=\"./viewepg.php?p=$page&start=$day3after\">$day3</A>��<A HREF=\"./viewepg.php?p=$page&start=$day4after\">$day4</A>��<A HREF=\"./viewepg.php?p=$page&start=$day5after\">$day5</A>��<A HREF=\"./viewepg.php?p=$page&start=$day6after\">$day6</A>��<A HREF=\"./viewepg.php?p=$page&start=$day7after\">$day7</A>��<BR>\n";


if ($maxrows > $maxdisplay){
//ʣ���ڡ���
//$pages = ceil($maxrows / $maxdisplay) ;
if ($page > 1){
	$beforepage = $page - 1;
	print "<a href = \"./viewepg.php?p=$beforepage&start=$start\">��</A>";
}

print " $page / $pages (������) ";

if ($page < $pages){
	$nextpage = $page + 1;
	print "<a href = \"./viewepg.php?p=$nextpage&start=$start\">��</A>";
}
}
//�������鿷������
//���ɥꥹ��
$query = "SELECT stationid, stationname, stationrecch, ontvcode 
FROM foltia_station 
WHERE \"ontvcode\" ~~ '%ontvjapan%'  
ORDER BY stationid ASC , stationrecch 
OFFSET $offset LIMIT $maxdisplay 
";
$slistrs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$stations =  pg_num_rows($slistrs);
for ($i=0 ; $i < $stations ; $i++){
	$rowdata = pg_fetch_row($slistrs, $i);
	$stationhash[$i] = $rowdata[3] ;
}

//�����֤������֤Υϥå�����
$epgstart = $start ;
$epgend = calcendtime($start , (8*60));

$query = "SELECT DISTINCT startdatetime   
FROM foltia_epg
WHERE foltia_epg.ontvchannel in (
	SELECT ontvcode 
	FROM foltia_station 
	WHERE \"ontvcode\" ~~ '%ontvjapan%'  
	ORDER BY stationid ASC , stationrecch 
	OFFSET $offset LIMIT $maxdisplay
	)
AND startdatetime  >= $start  
AND startdatetime  < $epgend  
ORDER BY foltia_epg.startdatetime  ASC	";

$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$colmnums =  pg_num_rows($rs);
if ($colmnums == 0){
//���ȥǡ������ʤ�
$colmnums = 2;
}else{
	for ($i=0 ; $i < $colmnums ; $i++){
		$rowdata = pg_fetch_row($rs, $i);
		$timetablehash["$rowdata[0]"] = $i;
	}
}
//���ɤ��Ȥ˽Ĥ���������Ƥ���
for ($j=0 ; $j < $stations ; $j++){
	$rowdata = pg_fetch_row($slistrs, $j);
	$stationname = $rowdata[3];

$epgstart = $start ;
$epgend = calcendtime($start , (8*60));
$query = "
SELECT startdatetime , enddatetime , lengthmin , epgtitle , epgdesc , epgcategory  ,ontvchannel  ,epgid ,	epgcategory 
FROM foltia_epg 
WHERE foltia_epg.ontvchannel = '$stationname' AND 
enddatetime  > $epgstart  AND 
startdatetime  < $epgend  
ORDER BY foltia_epg.startdatetime  ASC
	";
	$statiodh = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrowsstation = pg_num_rows($statiodh);
if ($maxrowsstation == 0) {
		//print("���ȥǡ���������ޤ���<BR>");
		$item[0]["$stationname"] =  ">���ȥǡ���������ޤ���";
}else{

for ($srow = 0; $srow < $maxrowsstation ; $srow++) { 
	 
$stationrowdata = pg_fetch_row($statiodh, $srow);

$printstarttime = substr($stationrowdata[0],8,2) . ":" .  substr($stationrowdata[0],10,2);
$tdclass = "t".substr($stationrowdata[0],8,2) .  substr($stationrowdata[0],10,2);
$title = $stationrowdata[3];
$title = htmlspecialchars(z2h($title));
$desc = $stationrowdata[4];
$desc = htmlspecialchars(z2h($desc));
$height =  htmlspecialchars($stationrowdata[2]) * 3;
$epgid =  htmlspecialchars($stationrowdata[7]);
$epgcategory = htmlspecialchars($stationrowdata[8]);

if (isset($timetablehash["$stationrowdata[0]"])){
	$number = $timetablehash["$stationrowdata[0]"];
}else{
	$number = 0;
}
if ($epgcategory == ""){
$item["$number"]["$stationname"] =  " onClick=\"location = './reserveepg.php?epgid=$epgid'\"><span id=\"epgstarttime\">$printstarttime</span> <A HREF=\"./reserveepg.php?epgid=$epgid\"><span id=\"epgtitle\">$title</span></A> <span id=\"epgdesc\">$desc</span>";
}else{
$item["$number"]["$stationname"] =  " id=\"$epgcategory\" onClick=\"location = './reserveepg.php?epgid=$epgid'\"><span id=\"epgstarttime\">$printstarttime</span> <A HREF=\"./reserveepg.php?epgid=$epgid\"><span id=\"epgtitle\">$title</span></A> <span id=\"epgdesc\">$desc</span></span>";
}//if

}//for
}//if

//���ɤ��Ȥ˴ֳַ���
//$item[$i][NHK] �ϥ̥뤫�ɤ���Ƚ��
$dataplace = 0 ; //�����
$rowspan = 0;

for ($i=1; $i <= $colmnums ; $i++){
	if ($i === ($colmnums - 1)){//�ǽ���
		$rowspan = $i - $dataplace + 1;
		//�����Ƽ�ʬ���Ȥ˥�����
			if ($item[$i][$stationname] == ""){
			$item[$i][$stationname]  = "";
			}else{
			$item[$i][$stationname]  = "<td ". $item[$i][$stationname] . "</td>";
			$rowspan--;
			}
			//ROWSPAN
			if ($rowspan === 1 ){
			$item[$dataplace][$stationname]  = "<td ". $item[$dataplace][$stationname] . "</td>";
			}else{
			$item[$dataplace][$stationname]  = "<td  rowspan = $rowspan ". $item[$dataplace][$stationname] . "</td>";
			}

	}elseif ($item[$i][$stationname] == ""){
	//�̥�ʤ�
		$item[$i][$stationname]  =  $item[$i][$stationname] ;
	}else{
	//�ʤ����äƤ�ʤ�
		$rowspan = $i - $dataplace;
			if ($rowspan === 1 ){
			$item[$dataplace][$stationname]  = "<td ". $item[$dataplace][$stationname] . "</td>";
			}else{
			$item[$dataplace][$stationname]  = "<td rowspan = $rowspan ". $item[$dataplace][$stationname] . "</td>";
			}
		$dataplace = $i;
		
	}
}//for
}// end of for://���ɤ��Ȥ˽Ĥ���������Ƥ���

//���ơ��֥�������
print "<table>\n<tr>";

//�إå�
for ($i=0;$i<$stations;$i++){
	$rowdata = pg_fetch_row($slistrs, $i);
	print "<th>".htmlspecialchars($rowdata[1])."</th>" ;
}
//����
for ($l = 0 ;$l <  $colmnums; $l++){
	print "<tr>";
	for ($m = 0 ; $m < $stations ; $m++ ){
		$stationname = $stationhash[$m];
		print_r($item[$l]["$stationname"]);
	}
	print "</tr>\n";
}
print "</table>\n";
 ?>

<hr>
����
<table>
<tr>
<td id="information">����</td>
<td id="anime">���˥ᡦ�û�</td>
<td id="news">�˥塼������ƻ</td>
<td id="drama">�ɥ��</td>
<td id="variety">�Х饨�ƥ�</td>
<td id="documentary">�ɥ����󥿥꡼������</td>
<td id="education">����</td>
<td id="music">����</td>
<td id="cinema">�ǲ�</td>
<td id="hobby">��̣������</td>
<td id="kids">���å�</td>
<td id="sports">���ݡ���</td>
<td id="etc">����¾</td>
<td id="stage">���</td>

</tr>
</table>
</body>
</html>


