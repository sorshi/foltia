<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

delreserve.php

��Ū
��ưϿ���ͽ������Ԥ��ޤ�

����
tid:�����ȥ�ID
sid:������ID
delflag:��ǧ�ե饰

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
<title>foltia:delete schedule</title>
</head>

<?php


$tid = getgetnumform(tid);
		if ($tid == "") {
		die_exit("���Ȥ�����ޤ���<BR>");
		}
$sid = getgetnumform(sid);
		if ($sid == "") {
		die_exit("�ɤ�����ޤ���<BR>");
		}

$now = date("YmdHi");   
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 
	printhtmlpageheader();

//�����ȥ����
$query = "
SELECT 
foltia_program.tid,
stationname,
foltia_program .title ,
foltia_tvrecord.bitrate ,
foltia_tvrecord.stationid  
FROM  foltia_tvrecord , foltia_program , foltia_station 
WHERE foltia_tvrecord.tid = foltia_program.tid  AND foltia_tvrecord.stationid = foltia_station .stationid  AND foltia_tvrecord.tid = $tid AND foltia_tvrecord.stationid = $sid  ";

	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}
		$rowdata = pg_fetch_row($rs, 0);

		$tid = htmlspecialchars($rowdata[0]);
		$stationname = htmlspecialchars($rowdata[1]);
		$title = htmlspecialchars($rowdata[2]);
		$bitrate = htmlspecialchars($rowdata[3]);
		$stationid = htmlspecialchars($rowdata[4]);

$delflag = getgetnumform(delflag);

?>

  <p align="left"><font color="#494949" size="6">ͽ����</font></p>
  <hr size="4">
<?php
if ($delflag == "1") {
	print "��".$title."�פμ�ưϿ��ͽ��������ޤ����� <br>\n";

//�������
if (($demomode) || ($protectmode) ){
//demomode��protectmode�ʤ�ʤˤ⤷�ʤ�
}else{

//���塼����ץ����򥭥å�
$oserr = system("$toolpath/perl/addatq.pl $tid $sid DELETE");
//DB���
$query = "
DELETE  
FROM  foltia_tvrecord  
WHERE foltia_tvrecord.tid = $tid AND foltia_tvrecord.stationid = $sid  ";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
}

}else{
	print "��".$title."�פμ�ưϿ��ͽ��������ޤ��� <br>\n";

print "<form name=\"deletereserve\" method=\"GET\" action=\"delreserve.php\">
<input type=\"submit\" value=\"ͽ����\" >\n";

}

?>  
<br>
<table width="100%" border="0">
  <tr>
    <td>�����ȥ�</td>
    <td>������</td>
    <td>�ӥåȥ졼��</td>
  </tr>
  <tr>
    <td><?=$title?></td>
    <td><?=$stationname?></td>
    <td><?=$bitrate?></td>

  </tr>
</table>

<?php
if ($delflag == "1") {

}else{
print "
<input type=\"hidden\" name=\"tid\" value=\"$tid\">
<input type=\"hidden\" name=\"sid\" value=\"$sid\">
<input type=\"hidden\" name=\"delflag\" value=\"1\">
</form>\n";

}

?>  

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
 AND foltia_subtitle.startdatetime >=  '$now'  AND foltia_program.tid ='$tid' 
ORDER BY foltia_subtitle.startdatetime  ASC
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		echo("����ͽ��Ϥ���ޤ���<BR>");
		}
		else{
		$maxcols = pg_num_fields($rs);		
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
			for ($row = 0; $row < $maxrows; $row++) { /* �Ԥ��б� */
				echo("<tr>\n");
				/* pg_fetch_row �ǰ�Լ��Ф� */
				$rowdata = pg_fetch_row($rs, $row);

				for ($col = 0; $col < $maxcols; $col++) { /* ����б� */
					echo("<td>".htmlspecialchars($rowdata[$col])."<br></td>\n");
				}
				echo("</tr>\n");
			}
		}//end if
		?>
	</tbody>
</table>



</body>
</html>
