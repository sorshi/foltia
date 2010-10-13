<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

deletemovie.php

��Ū
���ꤵ�줿���Ȥ����������ޤ���

����
showplaylist.php�������о�mpeg2�ꥹ�ȡ�

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
<title>foltia:�ե�������</title>
</head>

<?php
$now = date("YmdHi");   

$delete = $_POST['delete'];

?>

<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">Ͽ�����Ⱥ��</font></p>
  <hr size="4">
<?php
if ($delete == ""){
	print "<p align=\"left\">������ȤϤ���ޤ���</p>\n";
}else{


$userclass = getuserclass($con);
if ( $userclass <= 1){

print "<p align=\"left\">�������Ȥ������ޤ�����</p>
  <table BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"2\" WIDTH=\"100%\">
	<thead>
		<tr>
			<th align=\"left\">�ե�����̾</th>
			<th align=\"left\">�����ȥ�</th>
			<th align=\"left\">�ÿ�</th>
			<th align=\"left\">���֥���</th>
		</tr>
	</thead>
	<tbody>";

foreach ($delete as $fName) {

if( preg_match('/.MP4$/',$fName)){   //��ĥ�Ҥ�MP4�ʤ�Ͽ��饤�֥�����ȸ���ɽ���Ǥκ�� (showlibc.php)
$query = "
SELECT
foltia_subtitle.pspfilename,
foltia_program.title,
foltia_subtitle.countno,
foltia_subtitle.subtitle
FROM foltia_subtitle,foltia_program
WHERE  foltia_subtitle.tid = foltia_program.tid AND  foltia_subtitle.pspfilename = ? 
LIMIT 1";

$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($fName));
$rall = $rs->fetch();
$rowdata = $rall[0];

$title =  htmlspecialchars($rall[1]);
$count =  htmlspecialchars($rall[2]);
$subtitle =  htmlspecialchars($rall[3]);

print "
<tr>
<td>$fName<br></td>
<td>";

if ($tid > 0 ){
        print "<a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">$title</a>";
}else{
        print "$title";
}

print "</td>
<td>$count<br></td>
<td>$subtitle<br></td>
</tr>\n
";

//DB������
if ($demomode){
}else{

$query = "
DELETE  FROM  foltia_mp4files
WHERE mp4filename = ?
";
//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($fName));

//�������
$oserr = system("$toolpath/perl/deletemovie.pl $fName");
}//end if demomode

}else{       //��ĥ�Ҥ�MP4�ʳ��ʤ� Ͽ�������Ͽ��硦���Ƚ�ˤκ��(showplaylist.php)

$query = "
SELECT foltia_program.tid,foltia_program.title,foltia_subtitle.countno,foltia_subtitle.subtitle 
FROM foltia_subtitle , foltia_program 
WHERE foltia_program.tid = foltia_subtitle.tid 
AND foltia_subtitle.m2pfilename =  ? 
";

//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
//$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($filesplit[0] ,$filesplit[1]));
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($fName));
				$rall = $rs->fetchAll();
				$rowdata = $rall[0];
$title = $rowdata[1];
$count = $rowdata[2];
$subtitle = $rowdata[3];

$tid = htmlspecialchars($rowdata[0]);
$title = htmlspecialchars($title);
$count = htmlspecialchars($count);
$subtitle = htmlspecialchars($subtitle);

print "
<tr>
<td>$fName<br></td>
<td>";

if ($tid > 0 ){
	print "<a href=\"http://cal.syoboi.jp/tid/$tid\" target=\"_blank\">$title</a>";
}else{
	print "$title";
}

print "</td>
<td>$count<br></td>
<td>$subtitle<br></td>
</tr>\n
";

//DB������
if ($demomode){
}else{

$query = "
DELETE  FROM  foltia_m2pfiles  
WHERE m2pfilename = ? 
";
//$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($fName));

//�������
$oserr = system("$toolpath/perl/deletemovie.pl $fName");
}//end if demomode

}//end if .MP4��ĥ��ʬ��

}//foreach

print "	</tbody></table>\n";

}else{//���¤ʤ�
	print "<p align=\"left\">�ե����������¤�����ޤ���</p>";
}
}//if $delete == ""
?>

</body>
</html>
