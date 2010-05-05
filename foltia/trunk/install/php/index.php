<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

index.php

��Ū
����������ͽ���ɽ�����ޤ���
Ͽ��ͽ�󤵤�Ƥ������Ȥ��̿��Ǥ狼��䤹��ɽ������Ƥ��ޤ���


���ץ����
mode:"new"����ꤹ��ȡ�������(��1��)�Τߤ�ɽ���Ȥʤ롣
now:YmdHi���������դ���ꤹ��Ȥ��������������ɽ��ɽ������롣

 DCC-JPL Japan/foltia project

*/

include("./foltialib.php");
$con = m_connect();

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

$now = getgetnumform(date);
if(($now < 200001010000 ) || ($now > 209912342353 )){ 
	$now = date("YmdHi");   
}
function printtitle(){

print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html lang=\"ja\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"graytable.css\"> ";
//�ǥ������������̤ˤ�ä��طʿ�ɽ���ѹ�
warndiskfreearea();
print "<title>foltia:����ͽ��</title>
</head>";


}//end function printtitle()

//Ʊ������¾�ɸ���
$query = "
SELECT
foltia_program .tid,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate ,
foltia_subtitle.pid  
FROM foltia_subtitle , foltia_program  ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid 
AND foltia_program.tid = foltia_subtitle.tid 
AND foltia_subtitle.enddatetime >= ? 
ORDER BY \"startdatetime\" ASC 
LIMIT 1000
	";
//	$reservedrssametid = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$reservedrssametid = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($now));
$rowdata = $reservedrssametid->fetch();
if ($rowdata) {
	do {
		$reservedpidsametid[] = $rowdata[7];
	} while ($rowdata = $reservedrssametid->fetch());

	$rowdata = "";
	}else{
	$reservedpidsametid = array();
	}//end if
$reservedrssametid->closeCursor();

//Ͽ�����ȸ���
$query = "
SELECT
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime as x, foltia_subtitle.lengthmin,
 foltia_tvrecord.bitrate, foltia_subtitle.pid
FROM foltia_subtitle , foltia_program ,foltia_station ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid AND foltia_tvrecord.stationid = foltia_station .stationid AND foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid
AND foltia_subtitle.enddatetime >= '$now'
UNION
SELECT
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_tvrecord.bitrate, foltia_subtitle.pid
FROM foltia_tvrecord
LEFT OUTER JOIN foltia_subtitle on (foltia_tvrecord.tid = foltia_subtitle.tid )
LEFT OUTER JOIN foltia_program on (foltia_tvrecord.tid = foltia_program.tid )
LEFT OUTER JOIN foltia_station on (foltia_subtitle.stationid = foltia_station.stationid )
WHERE foltia_tvrecord.stationid = 0 AND
 foltia_subtitle.enddatetime >= '$now' ORDER BY x ASC
LIMIT 1000
	";

//$reservedrs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$query = "
SELECT
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime as x, foltia_subtitle.lengthmin,
 foltia_tvrecord.bitrate, foltia_subtitle.pid
FROM foltia_subtitle , foltia_program ,foltia_station ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid AND foltia_tvrecord.stationid = foltia_station .stationid AND foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid
AND foltia_subtitle.enddatetime >= ? 
UNION
SELECT
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_tvrecord.bitrate, foltia_subtitle.pid
FROM foltia_tvrecord
LEFT OUTER JOIN foltia_subtitle on (foltia_tvrecord.tid = foltia_subtitle.tid )
LEFT OUTER JOIN foltia_program on (foltia_tvrecord.tid = foltia_program.tid )
LEFT OUTER JOIN foltia_station on (foltia_subtitle.stationid = foltia_station.stationid )
WHERE foltia_tvrecord.stationid = 0 AND
 foltia_subtitle.enddatetime >= ? ORDER BY x ASC
LIMIT 1000
	";
$reservedrs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($now,$now));

$rowdata = $reservedrs->fetch();
if ($rowdata) {
	do {
		$reservedpid[] = $rowdata[8];
	} while ($rowdata = $reservedrs->fetch());
	}else{
	$reservedpid = array();
	}//end if

$mode = getgetform(mode);

if ($mode == "new"){
//������ɽ���⡼��
	$query = "
	SELECT 
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_subtitle.pid, foltia_subtitle.startoffset
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.enddatetime >= '$now'  AND foltia_subtitle.countno = '1' 
ORDER BY foltia_subtitle.startdatetime  ASC 
LIMIT 1000
	";
$query = "
	SELECT 
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_subtitle.pid, foltia_subtitle.startoffset
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.enddatetime >= ?  AND foltia_subtitle.countno = '1' 
ORDER BY foltia_subtitle.startdatetime  ASC 
LIMIT 1000
	";
}else{
$query = "
	SELECT 
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_subtitle.pid, foltia_subtitle.startoffset
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.enddatetime >= '$now'  
ORDER BY foltia_subtitle.startdatetime  ASC 
LIMIT 1000
	";
$query = "
	SELECT 
 foltia_program.tid, stationname, foltia_program.title,
 foltia_subtitle.countno, foltia_subtitle.subtitle,
 foltia_subtitle.startdatetime, foltia_subtitle.lengthmin,
 foltia_subtitle.pid, foltia_subtitle.startoffset
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.enddatetime >= ?  
ORDER BY foltia_subtitle.startdatetime  ASC 
LIMIT 1000
	";
}//end if

//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($now));
$rowdata = $rs->fetch();
if (! $rowdata) {
header("Status: 404 Not Found",TRUE,404);
printtitle();
print "<body BGCOLOR=\"#ffffff\" TEXT=\"#494949\" LINK=\"#0047ff\" VLINK=\"#000000\" ALINK=\"#c6edff\" >
<div align=\"center\">\n";
printhtmlpageheader();
print "<hr size=\"4\">\n";
		die_exit("���ȥǡ���������ޤ���<BR>");
}//endif

printtitle();
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">
<?php
if ($mode == "new"){
	print "����������ͽ��";
}else{
	print "����ͽ��";
}
?>
</font></p>
  <hr size="4">
<p align="left">�������ȥꥹ�Ȥ�ɽ�����ޤ���</p>

<?php
		/* �ե�����ɿ� */
    $maxcols = $rs->columnCount();
		?>
  <table BORDER="0" CELLPADDING="0" CELLSPACING="2" WIDTH="100%">
	<thead>
		<tr>
			<th align="left">TID</th>
			<th align="left">���Ƕ�</th>
			<th align="left">�����ȥ�</th>
			<th align="left">�ÿ�</th>
			<th align="left">���֥����ȥ�</th>
			<th align="left">���ϻ���(����)</th>
			<th align="left">���</th>

		</tr>
	</thead>

	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
     do {
//¾�ɤ�Ʊ������Ͽ��Ѥߤʤ鿧�Ѥ�
if (in_array($rowdata[7], $reservedpidsametid)) {
$rclass = "reservedtitle";
}else{
$rclass = "";
}
//Ͽ��ͽ��Ѥߤʤ鿧�Ѥ�
if (in_array($rowdata[7], $reservedpid)) {
$rclass = "reserved";
}
$pid = htmlspecialchars($rowdata[7]);

$tid = htmlspecialchars($rowdata[0]);
$title = htmlspecialchars($rowdata[2]);
$subtitle =  htmlspecialchars($rowdata[4]);

				echo("<tr class=\"$rclass\">\n");
					// TID
					print "<td>";
					if ($tid == 0 ){
					print "$tid";
					}else{
					print "<a href=\"reserveprogram.php?tid=$tid\">$tid</a>";
					}
					print "</td>\n";
				     // ���Ƕ�
				     echo("<td>".htmlspecialchars($rowdata[1])."<br></td>\n");
				     // �����ȥ�
					print "<td>";
					if ($tid == 0 ){
					print "$title";
					}else{
					print "<a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">$title</a>";
					}
					print "</td>\n";
					 // �ÿ�
					echo("<td>".htmlspecialchars($rowdata[3])."<br></td>\n");
					// ���֥���
					if ($pid > 0 ){
					print "<td><a href=\"http://cal.syoboi.jp/tid/$tid/time#$pid\" target=\"_blank\">$subtitle<br></td>\n";
					}else{
					print "<td>$subtitle<br></td>\n";
					}
					// ���ϻ���(����)
					echo("<td>".htmlspecialchars(foldate2print($rowdata[5]))."<br>(".htmlspecialchars($rowdata[8]).")</td>\n");
					// ���
					echo("<td>".htmlspecialchars($rowdata[6])."<br></td>\n");

				echo("</tr>\n");
     } while ($rowdata = $rs->fetch());
		?>
	</tbody>
</table>


</body>
</html>
