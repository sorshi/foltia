<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

showlib.php

��Ū
MPEG4Ͽ��饤�֥���ɽ�����ޤ���

����
�ʤ�

 DCC-JPL Japan/foltia project

*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<link rel="alternate" type="application/rss+xml" title="RSS" href="./folcast.php" />
<title>foltia:MP4 Lib</title>
</head>

<?php
include("./foltialib.php");

$con = m_connect();
$now = date("YmdHi");   

?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
	printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">Ͽ��饤�֥��ɽ��</font></p>
  <hr size="4">
<p align="left">������ǽ�饤�֥���ɽ�����ޤ���<br>

<?
//������ /* 2006/10/26 */
$query = "
SELECT foltia_mp4files.tid,foltia_program.title , count(foltia_mp4files.mp4filename) 
FROM   foltia_mp4files ,  foltia_program 
WHERE  foltia_program.tid = foltia_mp4files.tid  
GROUP BY foltia_mp4files.tid ,foltia_program.title 
ORDER BY foltia_mp4files.tid DESC
";

$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");

$maxrows = pg_num_rows($rs);

if ($maxrows > 0 ){
print "
  <table BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"2\" WIDTH=\"100%\">
	<thead>
		<tr>
			<th align=\"left\">TID</th>
			<th align=\"left\">�����ȥ�(���ƥ��)</th>
			<th align=\"left\">���ƿ�</th>
			<th align=\"left\">���</th>
		</tr>
	</thead>
	<tbody>
";
for ($row = 0; $row < $maxrows; $row++) {
	$rowdata = pg_fetch_row($rs, $row);
$title = $rowdata[1];
$counts = $rowdata[2];
$tid = htmlspecialchars($rowdata[0]);
$title = htmlspecialchars($title);
$counts = htmlspecialchars($counts);

print "
<tr>
<td>$tid<br></td>
<td><a href=\"showlibc.php?tid=$tid\">$title</a></td>
<td>$counts<br></td>
<td><a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">����ܤ���-$tid</a><br></td>
</tr>\n
";
}//for
print "
	</tbody>
</table>
</body>
</html>
";
}else{
print "Ͽ��ե����뤬¸�ߤ��ޤ���</body></html>";

}//end if
/*
//�����
//�ǥ��쥯�ȥ꤫��ե�������������
	exec ("ls  $recfolderpath | grep localized | sort -r", $libdir);
//print "libdir:$libdir<BR>\n";

foreach($libdir as $fName) {

if(($fName == ".") or ($fName == "..") ){ continue; }
	if (ereg(".localized", $fName)){
		$filesplit = split("\.",$fName);
$query = "
SELECT 
foltia_program.tid,foltia_program.title   
FROM   foltia_program   
WHERE foltia_program.tid = $filesplit[0] 
";
$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rowdata = pg_fetch_row($rs, $row);
//print" $fName./$rowdata[1]/$rowdata[2]/$rowdata[3]<BR>\n";
$title = $rowdata[1];

$tid = htmlspecialchars($rowdata[0]);
$title = htmlspecialchars($title);
//--
print "
<tr>
<td>$tid<br></td>
<td><a href=\"showlibc.php?tid=$tid\">$title</a></td>
<td>";
//�׿�
$counts = system ("ls  $recfolderpath/$fName/mp4/*.MP4 | wc -l");
print "<br></td>
<td><a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">����ܤ���-$tid</a><br></td>
</tr>\n
";
        }//end if ereg m2p
		}//end foreach
//����ͥ����ޤ�
*/
//$d->close();
?>
