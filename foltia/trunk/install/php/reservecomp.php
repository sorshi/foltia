<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

reserveprogram.php

��Ū
���Ȥ�ͽ����Ͽ�򤷤ޤ���

����
tid:�����ȥ�ID
station:Ͽ���
bitrate:Ͽ��ӥåȥ졼��(ñ��:Mbps)

 DCC-JPL Japan/foltia project

*/
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

  include("./foltialib.php");

$tid = getgetnumform(tid);
		if ($tid == "") {
		die_exit("���Ȥ����ꤵ��Ƥ��ޤ���<BR>");
		}

$station = getgetnumform(station);
		if ($station == "") {
		$station = 0;
		}

$bitrate = getgetnumform(bitrate);
		if ($bitrate == "") {
		$bitrate = 5;
		}


$con = m_connect();
$now = date("YmdHi");   

//�����ȥ����
	$query = "select title from foltia_program where tid='$tid'";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		$title = "(̤��Ͽ)";
		}else{
		$rowdata = pg_fetch_row($rs, 0);
		$title = htmlspecialchars($rowdata[0]);
		}

?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 
	printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">ͽ��λ</font></p>
  <hr size="4">

��<?=$title?>�פ�����ͽ��⡼�ɤ�ͽ�󤷤ޤ����� <br>
 <br>
ͽ�󥹥����塼�� <BR>

<?php

if ($station != 0){
//�ɸ���
	$query = "
SELECT 
foltia_subtitle.pid ,  
stationname,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_subtitle.startoffset 
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_station.stationid = $station 
 AND foltia_subtitle.startdatetime >=  '$now'  AND foltia_program.tid ='$tid' 
ORDER BY foltia_subtitle.startdatetime  ASC
";

}else{
//����
	$query = "
SELECT 
foltia_subtitle.pid ,  
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

}
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		echo("����ͽ��Ϥ��ޤΤȤ�����ޤ���<BR>");
		}
		else{
		$maxcols = pg_num_fields($rs);		
?>
  <table BORDER="0" CELLPADDING="0" CELLSPACING="2" WIDTH="100%" BGCOLOR="#bcf1be">
	<thead>
		<tr>
			<th align="left">PID</th>
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


<?php
if ($demomode){
}else{
//foltia_tvrecord���񤭹���
//��¸��ͽ�󤢤äơ����夬����ͽ����ä���
if ($station ==0){
	$query = "
SELECT 
 * 
FROM foltia_tvrecord  
WHERE tid = '$tid' 
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
	if ($maxrows > 0){
	//��¸�ɤ�ä�
		$query = "DELETE 
FROM foltia_tvrecord  
WHERE tid = '$tid' 
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
		}
}//endif

	$query = "
SELECT 
 * 
FROM foltia_tvrecord  
WHERE tid = '$tid'  AND stationid = '$station' 
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);

		if ($maxrows == 0) { //�����ɲ�
				$query = "INSERT INTO  foltia_tvrecord  values ('$tid','$station','$bitrate')";
				$rs = m_query($con, $query, "DB�񤭹��ߤ˼��Ԥ��ޤ���");
		}else{//������(�ӥåȥ졼��)
			$query = "UPDATE  foltia_tvrecord  SET 
  bitrate = '$bitrate' WHERE tid = '$tid'  AND stationid = '$station'
			";
			$rs = m_query($con, $query, "DB�񤭹��ߤ˼��Ԥ��ޤ���");
		}
	
//���塼����ץ����򥭥å�
//������TID �����ͥ�ID
//echo("$toolpath/perl/addatq.pl $tid $station");
$oserr = system("$toolpath/perl/addatq.pl $tid $station");
}//end if demomode
?>


</body>
</html>
