<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

listreserve.php

��Ū
Ͽ��ͽ����������ͽ���ͽ������̾��ɽ�����ޤ���

����
r:Ͽ��ǥХ�����
startdate:�������դ����ͽ�������YYYYmmddHHii�����ǡ�ɽ�����˸��꤫���Ƥʤ��Τǥ쥳���ɿ������̤ˤʤ�ȽŤ��ʤ뤫���Τ�ޤ���


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
$userclass = getuserclass($con);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>foltia:record plan</title>
</head>

<?php
$mymemberid = getmymemberid($con);
$now = getgetnumform(startdate);
*if ($now == ""){
$now = getgetnumform(date);
}

if ($now > 200501010000){
}else{
	$now = date("YmdHi");   
}
	$query = "
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate  , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid , 
foltia_subtitle.epgaddedby , 
foltia_tvrecord.digital 
FROM foltia_subtitle , foltia_program ,foltia_station ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid AND foltia_tvrecord.stationid = foltia_station .stationid AND foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid
AND foltia_subtitle.enddatetime >= '$now'
UNION
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid , 
foltia_subtitle.epgaddedby , 
foltia_tvrecord.digital 
FROM foltia_tvrecord
LEFT OUTER JOIN foltia_subtitle on (foltia_tvrecord.tid = foltia_subtitle.tid )
LEFT OUTER JOIN foltia_program on (foltia_tvrecord.tid = foltia_program.tid )
LEFT OUTER JOIN foltia_station on (foltia_subtitle.stationid = foltia_station.stationid )
WHERE foltia_tvrecord.stationid = 0 AND
foltia_subtitle.enddatetime >= '$now' ORDER BY \"startdatetime\" ASC
	";

	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			

//���塼�ʡ���
if (getgetnumform(r) != ""){
	$recunits = getgetnumform(r);
}elseif($recunits == ""){
	$recunits = 2;
}

?>

<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">ͽ�����</font></p>
  <hr size="4">
<p align="left">Ͽ��ͽ����������ͽ���ͽ������̾��ɽ�����ޤ���</p>

<?
	if ($maxrows == 0) {
		print "���ȥǡ���������ޤ���<BR>\n";			
		}else{


		/* �ե�����ɿ� */
		$maxcols = pg_num_fields($rs);
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
			<th align="left">���</th>
			<th align="left">�ǥ�����ͥ��</th>

		</tr>
	</thead>

	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
			for ($row = 0; $row < $maxrows; $row++) { /* �Ԥ��б� */
				echo("<tr>\n");
				/* pg_fetch_row �ǰ�Լ��Ф� */
				$rowdata = pg_fetch_row($rs, $row);
$pid = htmlspecialchars($rowdata[9]);

$tid = htmlspecialchars($rowdata[0]);
$title = htmlspecialchars($rowdata[2]);
$subtitle = htmlspecialchars($rowdata[4]);
$dbepgaddedby = htmlspecialchars($rowdata[10]);

//��ʣ����
//���ϻ��� $rowdata[5]
//��λ����
$endtime = calcendtime($rowdata[5],$rowdata[6]);
//���Ȥγ��ϻ������٤�����˽�λ������λ���������ˤϤ��ޤ����Ȥ����뤫�ɤ���
//����ܡ��ɥ��塼�ʡ�Ͽ��
$query = "
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate  , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid  , 
foltia_tvrecord.digital 
FROM foltia_subtitle , foltia_program ,foltia_station ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid AND foltia_tvrecord.stationid = foltia_station .stationid AND foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid
AND foltia_subtitle.enddatetime > '$rowdata[5]' 
AND foltia_subtitle.startdatetime < '$endtime'  
UNION
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate  , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid , 
foltia_tvrecord.digital 
FROM foltia_tvrecord
LEFT OUTER JOIN foltia_subtitle on (foltia_tvrecord.tid = foltia_subtitle.tid )
LEFT OUTER JOIN foltia_program on (foltia_tvrecord.tid = foltia_program.tid )
LEFT OUTER JOIN foltia_station on (foltia_subtitle.stationid = foltia_station.stationid )
WHERE foltia_tvrecord.stationid = 0 AND
foltia_subtitle.enddatetime > '$rowdata[5]'  
AND foltia_subtitle.startdatetime < '$endtime'  
	";
	$rclass = "";
	$overlap = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$overlapmaxrows = pg_num_rows($overlap);
	if ($overlapmaxrows > ($recunits) ){
		for ($rrow = 0; $rrow < $overlapmaxrows ; $rrow++) {
			$owrowdata = pg_fetch_row($overlap, $rrow);
			$overlappid[] = $owrowdata[9];
		}
	if (in_array($rowdata[9], $overlappid)) {
		$rclass = "overwraped";
	}
	}else{
	$overlappid = "";
	}//end if

//�������塼�ʡ�Ͽ��
$externalinputs = 1; //����������Τ�
$query = "
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate  , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid  , 
foltia_tvrecord.digital 
FROM foltia_subtitle , foltia_program ,foltia_station ,foltia_tvrecord
WHERE foltia_tvrecord.tid = foltia_program.tid AND foltia_tvrecord.stationid = foltia_station .stationid AND foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid
AND foltia_subtitle.enddatetime > '$rowdata[5]' 
AND foltia_subtitle.startdatetime < '$endtime'  
AND  (foltia_station.stationrecch = '0' OR  foltia_station.stationrecch = '-1' ) 
UNION
SELECT
foltia_program .tid,
stationname,
foltia_program .title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin ,
foltia_tvrecord.bitrate  , 
foltia_subtitle.startoffset , 
foltia_subtitle.pid , 
foltia_tvrecord.digital 
FROM foltia_tvrecord
LEFT OUTER JOIN foltia_subtitle on (foltia_tvrecord.tid = foltia_subtitle.tid )
LEFT OUTER JOIN foltia_program on (foltia_tvrecord.tid = foltia_program.tid )
LEFT OUTER JOIN foltia_station on (foltia_subtitle.stationid = foltia_station.stationid )
WHERE foltia_tvrecord.stationid = 0 AND
foltia_subtitle.enddatetime > '$rowdata[5]'  
AND foltia_subtitle.startdatetime < '$endtime'  
AND  (foltia_station.stationrecch = '0' OR  foltia_station.stationrecch = '-1' ) 

	";
	$eoverlap = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$eoverlapmaxrows = pg_num_rows($eoverlap);
	if ($eoverlapmaxrows > ($externalinputs) ){
		for ($erow = 0; $erow < $eoverlapmaxrows ; $erow++) {
			$eowrowdata = pg_fetch_row($eoverlap, $erow);
			$eoverlappid[] = $eowrowdata[9];
		}

		if (in_array($rowdata[9], $eoverlappid)) {
			$rclass = "exoverwraped";
		}
	}else{
	$eoverlappid = "";
	}
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
					//if ( $userclass <= 2){
					if (($mymemberid == $dbepgaddedby)||($userclass <= 1)){
						if ($userclass <= 1 ){//�����Ԥʤ�
							$membername = getmemberid2name($con,$dbepgaddedby);
							$membername = ":" . $membername ;
						}else{
						$membername = "";
						}
					print "<td>$subtitle [<a href=\"delepgp.php?pid=$pid\">ͽ����</a>$membername]<br></td>\n";
					}else{
					print "<td>$subtitle [�����ǽ]<br></td>\n";
					}
					}
					// ���ϻ���(����)
					echo("<td>".htmlspecialchars(foldate2print($rowdata[5]))."<br>(".htmlspecialchars($rowdata[8]).")</td>\n");
					// ���
					echo("<td>".htmlspecialchars($rowdata[6])."<br></td>\n");
					
					//Ͽ��졼��
					echo("<td>".htmlspecialchars($rowdata[7])."<br></td>\n");
					
					//�ǥ�����ͥ��
					echo("<td>");
					if (htmlspecialchars($rowdata[11]) == 1){
					print "����";
					}else{
					print "���ʤ�";
					}
					echo("<br></td>\n");
				echo("</tr>\n");
			}
		?>
	</tbody>
</table>


<table>
	<tr><td>���ʥ���ʣɽ��</td><td><br /></td></tr>
	<tr><td>���󥳡�����</td><td><?=$recunits ?></td></tr>
	<tr class="overwraped"><td>���塼�ʡ���ʣ</td><td><br /></td></tr>
	<tr class="exoverwraped"><td>�������Ͻ�ʣ</td><td><br /></td></tr>
</table>


<?php
} //if ($maxrows == 0) {


	$query = "
SELECT 
foltia_program.tid,
stationname,
foltia_program .title ,
foltia_tvrecord.bitrate ,
foltia_tvrecord.stationid , 
foltia_tvrecord.digital   
FROM  foltia_tvrecord , foltia_program , foltia_station 
WHERE foltia_tvrecord.tid = foltia_program.tid  AND foltia_tvrecord.stationid = foltia_station .stationid 
ORDER BY foltia_program.tid  DESC
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
//�ʤ���Фʤˤ⤷�ʤ�
			
		}else{
		$maxcols = pg_num_fields($rs);

?>
<p align="left">Ͽ��ͽ�����ȥ����ȥ��ɽ�����ޤ���</p>
  <table BORDER="0" CELLPADDING="0" CELLSPACING="2" WIDTH="100%">
	<thead>
		<tr>
			<th align="left">ͽ����</th>
			<th align="left">TID</th>
			<th align="left">���Ƕ�</th>
			<th align="left">�����ȥ�</th>
			<th align="left">Ͽ��ꥹ��</th>
			<th align="left">���</th>
			<th align="left">�ǥ�����ͥ��</th>

		</tr>
	</thead>

	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
			for ($row = 0; $row < $maxrows; $row++) { /* �Ԥ��б� */
				/* pg_fetch_row �ǰ�Լ��Ф� */
				$rowdata = pg_fetch_row($rs, $row);

				$tid = htmlspecialchars($rowdata[0]);
				
				if ($tid > 0){
				echo("<tr>\n");
				//ͽ����
				if ( $userclass <= 1){
					echo("<td><a href=\"delreserve.php?tid=$tid&sid=" .
					htmlspecialchars($rowdata[4])  . "\">���</a></td>\n");
				}else{
				echo("<td>��</td>");		
				}
				//TID
					echo("<td><a href=\"reserveprogram.php?tid=$tid\">$tid</a></td>\n");
				     //���Ƕ�
				     echo("<td>".htmlspecialchars($rowdata[1])."<br></td>\n");
				     //�����ȥ�
				     echo("<td><a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">" .
				     htmlspecialchars($rowdata[2]) . "</a></td>\n");

					//MP4
					echo("<td><a href=\"showlibc.php?tid=$tid\">mp4</a></td>\n");
					//���(���ʥ��ӥåȥ졼��)
					echo("<td>".htmlspecialchars($rowdata[3])."<br></td>\n");
					//�ǥ�����ͥ��
					echo("<td>");
					if (htmlspecialchars($rowdata[5]) == 1){
					print "����";
					}else{
					print "���ʤ�";
					}
				echo("</tr>\n");
				}else{
				print "<tr>
				<td>��</td><td>0</td>
				<td>[����]<br></td>
				<td>EPGϿ��</td>
				<td><a href=\"showlibc.php?tid=0\">mp4</a></td>";
				echo("<td>".htmlspecialchars($rowdata[3])."<br></td>");
					//�ǥ�����ͥ��
					echo("<td>");
					if (htmlspecialchars($rowdata[5]) == 1){
					print "����";
					}else{
					print "���ʤ�";
					}
				echo("\n</tr>");
				}//if tid 0
			}//for
		}//else
		?>
	</tbody>
</table>


</body>
</html>
