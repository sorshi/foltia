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

include("./foltialib.php");
$con = m_connect();
$epgviewstyle = 1;// 0���Ƚ�λ�����ɽ��
if ($useenvironmentpolicy == 1){
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
	    header("WWW-Authenticate: Basic realm=\"foltia\"");
	    header("HTTP/1.0 401 Unauthorized");
		redirectlogin();
	    exit;
	} else {
	login($con,$_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
	}
}//end if login
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>foltia:EPG����ɽ</title>
</head>
<?php
$start = getgetnumform("start");

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
<p align="left"><a href="./m.php">���ȼ�ưͽ��</a ></p>
<hr size="4">
<p align="left">EPG����ɽ��ɽ�����ޤ���
<?php 

///////////////////////////////////////////////////////////////////////////
//���ߤ����վ������
$begin =  date("YmdHi");
$beginyear =   substr($begin,0,4);
$beginmonth =   substr($begin,4,2);
$beginday =   substr($begin,6,2);
$beginhour =   substr($begin,8,2);
$beginmin =   substr($begin,10,2);
///////////////////////////////////////////////////////////////////////////

$startyear =   substr($start,0,4);
$startmonth =   substr($start,4,2);
$startday =   substr($start,6,2);
$starthour =   substr($start,8,2);
$startmin =   substr($start,10,2);
$day_of_the_week = date ("(D)",mktime($starthour , 0 , 0, $startmonth , $startday  , $startyear));

print "($startyear/$startmonth/$startday $day_of_the_week $starthour:$startmin-)<BR>\n";


$yesterday = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday -1 , $startyear));
$dayyesterday = date ("m/d(D)",mktime($starthour , 0 , 0, $startmonth , $startday -1 , $startyear));

/////////////////////////////////////////////////////////// 
//������٤Ρ������ۤ��ѿ�
$tomorrow  = date ("YmdHi",mktime($starthour , 0 , 0, $startmonth , $startday +1 , $startyear));   
/////////////////////////////////////////////////////////// 
//EPG����ɽ��������ޤ��ΤȤʤ�����դΡ������ۤ��ѿ�
$daytomorrow  = date ("m/d(D)",mktime($starthour , 0 , 0, $startmonth , $startday +1 , $startyear));
///////////////////////////////////////////////////////////


$today0400 = date ("YmdHi",mktime(4 , 0 , 0, $startmonth , $startday  , $startyear));
$today0800 = date ("YmdHi",mktime(8 , 0 , 0, $startmonth , $startday  , $startyear));
$today1200 = date ("YmdHi",mktime(12 , 0 , 0, $startmonth , $startday , $startyear));
$today1600 = date ("YmdHi",mktime(16 , 0 , 0, $startmonth , $startday , $startyear));
$today2000 = date ("YmdHi",mktime(20 , 0 , 0, $startmonth , $startday , $startyear));
$today2359 = date ("YmdHi",mktime(23 , 59 , 0, $startmonth , $startday , $startyear));


///////////////////////////////////////////////////////////////////
//������ʬ�Υڡ����Υ�󥯤��ѿ�
$day0after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday  , $beginyear));
$day0 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday  , $beginyear));
$day1after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +1 , $beginyear));
$day1 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +1 , $beginyear));
$day2after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +2 , $beginyear));
$day2 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +2 , $beginyear));
$day3after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +3 , $beginyear));
$day3 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +3 , $beginyear));
$day4after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +4 , $beginyear));
$day4 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +4 , $beginyear));
$day5after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +5 , $beginyear));
$day5 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +5 , $beginyear));
$day6after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +6 , $beginyear));
$day6 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +6 , $beginyear));
$day7after = date ("YmdHi",mktime($beginhour , 0 , 0, $beginmonth , $beginday +7 , $beginyear));
$day7 = date ("m/d(D)",mktime($beginhour , 0 , 0, $beginmonth , $beginday +7 , $beginyear));
///////////////////////////////////////////////////////////////////


//ɽ��������
// $page = 1 ~ 
$maxdisplay = 8;

$query = "SELECT count(*) FROM foltia_station WHERE \"ontvcode\" LIKE '%ontvjapan%'";
//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���");
$maxrows = $rs->fetchColumn(0);
if ($maxrows > $maxdisplay){
	$pages = ceil($maxrows / $maxdisplay) ;
}

$page = getgetnumform("p");

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


/////////////////////////////////////////////////////////////////
//ɽ����ʬ
$navigationbar =  "

[<A HREF=\"./viewepg.php\">����</A>] | 
<A HREF=\"./viewepg.php?p=$page&start=$yesterday\">$dayyesterday [����]</A> | 
����(
<A HREF=\"./viewepg.php?p=$page&start=$today0400\">4:00</A>��
<A HREF=\"./viewepg.php?p=$page&start=$today0800\">8:00</A>��
<A HREF=\"./viewepg.php?p=$page&start=$today1200\">12:00</A>��
<A HREF=\"./viewepg.php?p=$page&start=$today1600\">16:00</A>��
<A HREF=\"./viewepg.php?p=$page&start=$today2000\">20:00</A>��
<A HREF=\"./viewepg.php?p=$page&start=$today2359\">24:00</A>) | 
<A HREF=\"./viewepg.php?p=$page&start=$tomorrow\">$daytomorrow [����]</A>
<br>
 | 
<A HREF=\"./viewepg.php?p=$page&start=$day0after\">$day0</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day1after\">$day1</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day2after\">$day2</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day3after\">$day3</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day4after\">$day4</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day5after\">$day5</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day6after\">$day6</A> | 
<A HREF=\"./viewepg.php?p=$page&start=$day7after\">$day7</A> | <BR>\n";
print "$navigationbar";
///////////////////////////////////////////////////////////////////

if ($maxrows > $maxdisplay){
//ʣ���ڡ���
//$pages = ceil($maxrows / $maxdisplay) ;
if ($page > 1){
	$beforepage = $page - 1;
	print "<a href = \"./viewepg.php?p=$beforepage&start=$start\">��</A>";
}

print " $page / $pages (������) ";
for ($i=1;$i<=$pages;$i++){
	print "<a href = \"./viewepg.php?p=$i&start=$start\">$i</a>��";
}


if ($page < $pages){
	$nextpage = $page + 1;
	print "<a href = \"./viewepg.php?p=$nextpage&start=$start\">��</a>";
}
}
//�������鿷������
//���ɥꥹ��
$query = "SELECT stationid, stationname, stationrecch, ontvcode 
FROM foltia_station 
WHERE \"ontvcode\" LIKE '%ontvjapan%'  
ORDER BY stationid ASC , stationrecch 
LIMIT ? OFFSET ?
";

//$slistrs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$slistrs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($maxdisplay,$offset));
while ($rowdata = $slistrs->fetch()) {
	$stationhash[] = $rowdata[3];
	$snames[] = $rowdata[1]; // headder
}

//�����֤������֤Υϥå�����
$epgstart = $start ;
$epgend = calcendtime($start , (8*60));

$query = "SELECT DISTINCT startdatetime   
FROM foltia_epg
WHERE foltia_epg.ontvchannel in (
	SELECT ontvcode 
	FROM foltia_station 
	WHERE \"ontvcode\" LIKE '%ontvjapan%' 
	ORDER BY stationid ASC , stationrecch 
	LIMIT ? OFFSET ?
	)
AND startdatetime  >= ? 
AND startdatetime  < ? 
ORDER BY foltia_epg.startdatetime  ASC	";

//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($maxdisplay,$offset,$start,$epgend));

//print "$query<br>\n";

$rowdata = $rs->fetch();
if (! $rowdata) {
//���ȥǡ������ʤ�
$colmnums = 2;
}else{
	$colmnums = 0;
	do {
		$colmnums++;
		$timetablehash[$rowdata[0]] = $colmnums;
//		print "$rowdata[0]:$i+1 <br>\n";
	} while ($rowdata = $rs->fetch());
}
//print "colmnums $colmnums <br>\n";

//���ɤ��Ȥ˽Ĥ���������Ƥ���
foreach ($stationhash as $stationname) {
$epgstart = $start ;
$epgend = calcendtime($start , (8*60));
$query = "
SELECT startdatetime , enddatetime , lengthmin , epgtitle , epgdesc , epgcategory  ,ontvchannel  ,epgid ,	epgcategory 
FROM foltia_epg 
WHERE foltia_epg.ontvchannel = ? AND 
enddatetime  > ?  AND 
startdatetime  < ?  
ORDER BY foltia_epg.startdatetime  ASC
	";

//	$statiodh = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$statiodh = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($stationname,$epgstart,$epgend));
	$stationrowdata = $statiodh->fetch();
	if (! $stationrowdata) {
		//print("���ȥǡ���������ޤ���<BR>");
		$item[0]["$stationname"] =  ">���ȥǡ���������ޤ���";
}else{
		do {
$printstarttime = substr($stationrowdata[0],8,2) . ":" .  substr($stationrowdata[0],10,2);
$tdclass = "t".substr($stationrowdata[0],8,2) .  substr($stationrowdata[0],10,2);
$title = $stationrowdata[3];
$title = htmlspecialchars(z2h($title));
$desc = $stationrowdata[4];
$desc = htmlspecialchars(z2h($desc));

if ($epgviewstyle){
$desc=$desc ."<br><br><!-- ". htmlspecialchars(foldate2print($stationrowdata[1])) ."-->";
}else{
$desc=$desc ."<br><br>". htmlspecialchars(foldate2print($stationrowdata[1])) ;
}


$height =  htmlspecialchars($stationrowdata[2]) * 3;
$epgid =  htmlspecialchars($stationrowdata[7]);
$epgcategory = htmlspecialchars($stationrowdata[8]);

if (isset($timetablehash["$stationrowdata[0]"])){
	$number = $timetablehash["$stationrowdata[0]"];
//print "$stationname $stationrowdata[0] [$number] $printstarttime $title $desc<br>\n";
}else{
	$number = 0;
//print "$stationname $stationrowdata[0] �������� $printstarttime $title $desc<br>\n";
}
if ($epgcategory == ""){
$item["$number"]["$stationname"] =  " onClick=\"location = './reserveepg.php?epgid=$epgid'\"><span id=\"epgstarttime\">$printstarttime</span> <A HREF=\"./reserveepg.php?epgid=$epgid\"><span id=\"epgtitle\">$title</span></A> <span id=\"epgdesc\">$desc</span>";
}else{
$item["$number"]["$stationname"] =  " id=\"$epgcategory\" onClick=\"location = './reserveepg.php?epgid=$epgid'\"><span id=\"epgstarttime\">$printstarttime</span> <A HREF=\"./reserveepg.php?epgid=$epgid\"><span id=\"epgtitle\">$title</span></A> <span id=\"epgdesc\">$desc</span></span>";
}//if

		} while ($stationrowdata = $statiodh->fetch());
}//if

//���ɤ��Ȥ˴ֳַ���
//$item[$i][NHK] �ϥ̥뤫�ɤ���Ƚ��
$dataplace = 0 ; //�����
$rowspan = 0;

for ($i=1; $i <= $colmnums ; $i++){
	if ($i === ($colmnums )){//�ǽ���
		$rowspan = $i - $dataplace ;
		//�����Ƽ�ʬ���Ȥ˥�����
			//if ((!isset($item[$i][$stationname])) && ($item[$i][$stationname] == "")){
			if (!isset($item[$i][$stationname])){
			$item[$i][$stationname]  = null ;
			}else{
			$item[$i][$stationname]  = "<td ". $item[$i][$stationname] . "</td>";
			$rowspan--;
			}
			//ROWSPAN
			if ($rowspan === 1 ){
			$item[$dataplace][$stationname]  = "<td ". $item[$dataplace][$stationname] . "</td>";
			}else{
			$item[$dataplace][$stationname]  = "<td  rowspan = $rowspan ". $item[$dataplace][$stationname] . "</td>";
//			$item[$dataplace][$stationname]  = "<td ". $item[$dataplace][$stationname] . "$rowspan </td>";
			}

//	}elseif ((!isset($item[$i][$stationname]))&&($item[$i][$stationname] == "")){
	}elseif (!isset($item[$i][$stationname])){
	//�̥�ʤ�
		//$item[$i][$stationname]  =  $item[$i][$stationname] ;
		$item[$i][$stationname]  =  null ;
//		$item[$i][$stationname]  =  "<td><br></td>" ;
	}else{
	//�ʤ����äƤ�ʤ�
		$rowspan = $i - $dataplace;
		$itemDataplaceStationname = null;
		if (isset($item[$dataplace][$stationname])){
		$itemDataplaceStationname = $item[$dataplace][$stationname];
		}
			if ($rowspan === 1 ){
			$item[$dataplace][$stationname]  = "<td ". $itemDataplaceStationname . "</td>";
			}else{
			$item[$dataplace][$stationname]  = "<td rowspan = $rowspan ". $itemDataplaceStationname . "</td>";
//			$item[$dataplace][$stationname]  = "<td ". $item[$dataplace][$stationname] . "$rowspan </td>";
			}
		$dataplace = $i;
		
	}
}//for
}// end of for://���ɤ��Ȥ˽Ĥ���������Ƥ���

//���ơ��֥�������
print "<table>\n<tr>";

//�إå�
foreach ($snames as $s) {
	print "<th>".htmlspecialchars($s)."</th>" ;
}
//����
for ($l = 0 ;$l <  $colmnums; $l++){
	print "<tr>";
	foreach ($stationhash as $stationname) {
		print_r($item[$l]["$stationname"]);
	}
	print "</tr>\n";
}
print "</table>\n";

print "<p align=\"left\"> $navigationbar </p>";
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


