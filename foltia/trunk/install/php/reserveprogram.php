<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

reserveprogram.php

��Ū
����Ͽ��ͽ��ڡ�����ɽ�����ޤ���

����
tid:�����ȥ�ID

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

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>foltia</title>
</head>

<?php


$tid = getgetnumform(tid);
		if ($tid == "") {
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}


$now = date("YmdHi");   

//�����ȥ����
	$query = "select title from foltia_program where tid = ? ";
//	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($tid));
$rowdata = $rs->fetch();
if (! $rowdata) {
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}

		$title = htmlspecialchars($rowdata[0]);
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 
	printhtmlpageheader();
?>

  <p align="left"><font color="#494949" size="6">����ͽ��</font></p>
  <hr size="4">

<?php
if ($tid == 0){
	print "<p>EPGͽ����ɲäϡ�<a href=\"./viewepg.php\">����ɽ</a>�ץ�˥塼����ԤäƲ�������</p>\n</body>\n</html>\n";
	exit ;
}

?>

��<?php print "$title"; ?>�פ�����ͽ��⡼�ɤ�Ͽ��ͽ�󤷤ޤ��� <br>

  
<form name="recordingsetting" method="GET" action="reservecomp.php">
<input type="submit" value="ͽ��" >
<br>
<table width="100%" border="0">
  <tr>
    <td>������</td>
    <td>�ǥ�����Ͽ��ͥ��</td>
    <td>���ʥ��ӥåȥ졼��</td>
  </tr>
  <tr>
    <td>
<?php	
	//Ͽ�����ɸ���
		$query = "
SELECT distinct  foltia_station.stationid , stationname , foltia_station.stationrecch 
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_program.tid = ? 
ORDER BY stationrecch DESC
";
//	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($tid));
$rowdata = $rs->fetch();
if (! $rowdata) {
		echo("���Ƕɾ��󤬤ޤ��Ϥ��äƤޤ���<BR>");
		}
		else{
	$maxcols = $rs->columnCount();
		
			echo("<select name=\"station\">\n");
			/* �ơ��֥�Υǡ�������� */
	do {
				echo("<option value=\"");
				echo(htmlspecialchars($rowdata[0]));
				echo("\">");
				echo(htmlspecialchars($rowdata[1]));
				echo("</option>\n");
	} while ($rowdata = $rs->fetch());
			echo("<option value=\"0\">����</option>\n</select>\n");
		}//endif		
	?>

	</td>
	
	<td>
	<select name="usedigital">
	<?php
	 if ($usedigital == 1){
	 	print "
		<option value=\"1\" selected>����</option>
		<option value=\"0\">���ʤ�</option>
		";
		}else{
	 	print "
		<option value=\"1\">����</option>
		<option value=\"0\" selected>���ʤ�</option>
		";
		}
	?>
	</select>
	</td>

    <td><select name="bitrate">
        <option value="14">�ǹ���</option>
        <option value="13">13Mbps</option>
        <option value="12">12Mbps</option>
        <option value="11">11Mbps</option>
        <option value="10">10Mbps</option>
        <option value="9">9Mbps</option>
        <option value="8">����</option>
        <option value="7">7Mbps</option>
        <option value="6">6Mbps</option>
        <option value="5" selected>ɸ����</option>
        <option value="4">4Mbps</option>
        <option value="3">3Mbps</option>
        <option value="2">�⤤����</option>
      </select></td>
  </tr>
</table>
<input type="hidden" name="tid" value="<?=$tid?>">
</form>
<p>&nbsp; </p>
<p><br>
���������ͽ�� </p>

<?php
	$query = "
SELECT 
stationname,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_subtitle.startoffset 
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.startdatetime >= ?  AND foltia_program.tid = ? 
ORDER BY foltia_subtitle.startdatetime  ASC
";
//	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($now,$tid));
$rowdata = $rs->fetch();
if (! $rowdata) {
		echo("����ͽ��Ϥ���ޤ���<BR>");
		}
		else{
	$maxcols = $rs->columnCount();
?>
  <table BORDER="0" CELLPADDING="0" CELLSPACING="2" WIDTH="100%" BGCOLOR="#bcf1be">
	<thead>
		<tr>
			<th align="left">���Ƕ�</th>
			<th align="left">�ÿ�</th>
			<th align="left">���֥����ȥ�</th>
			<th align="left">���ϻ���</th>
			<th align="left">���</th>
			<th align="left">���鷺��</th>

		</tr>
	</thead>

	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
       do {
				echo("<tr>\n");
				for ($col = 0; $col < $maxcols; $col++) { /* ����б� */
					if ($col == 3){
					echo("<td>".htmlspecialchars(foldate2print($rowdata[$col]))."<br></td>\n");
					}else{
					echo("<td>".htmlspecialchars($rowdata[$col])."<br></td>\n");
					}
				}
				echo("</tr>\n");
       } while ($rowdata = $rs->fetch());
		}//end if
		?>
	</tbody>
</table>



</body>
</html>
