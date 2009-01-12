<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/


��Ū
blog�ġ��롢�������饤�ȥ֥쥤�������Խ�����

����
pid:PID
f:file name

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


$pid = getgetform(pid);
$filename = getgetform(f);

if (($pid == "") ||($filename == "")) {
	header("Status: 404 Not Found",TRUE,404);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 
<title>Starlight Breaker -�Խ�</title>
</head>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">

<?php
printhtmlpageheader();

if (($pid == "") ||($filename == "")) {
	print "����������ޤ���<br></body></html>";
	exit;
}


$query = "
SELECT 
foltia_program.tid,
stationname,
foltia_program.title,
foltia_subtitle.countno,
foltia_subtitle.subtitle,
foltia_subtitle.startdatetime ,
foltia_subtitle.lengthmin  , 
foltia_subtitle.pid ,
foltia_subtitle.m2pfilename , 
foltia_subtitle.pspfilename 
FROM foltia_subtitle , foltia_program ,foltia_station  
WHERE foltia_program.tid = foltia_subtitle.tid AND foltia_station.stationid = foltia_subtitle.stationid 
 AND foltia_subtitle.pid = '$pid'  
 
	";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rows = pg_num_rows($rs);
if ($rows == 0){
	print "  <p align=\"left\"><font color=\"#494949\" size=\"6\">�񤭹����Խ�</font></p>
  <hr size=\"4\">
<p align=\"left\">
Ͽ�赭Ͽ������ޤ���<br>
";

}else{
$rowdata = pg_fetch_row($rs, 0);

print "  <p align=\"left\"><font color=\"#494949\" size=\"6\">�񤭹����Խ� </font></p>
  <hr size=\"4\">
<p align=\"left\">";
print "<a href = \"http://cal.syoboi.jp/tid/$rowdata[0]/\" target=\"_blank\">";
$title = htmlspecialchars($rowdata[2]);
$countno = htmlspecialchars($rowdata[3]);
print "$title</a> $countno " ;

$tid = $rowdata[0];
$subtitle = htmlspecialchars($rowdata[4]) ;
if ($tid > 0){
print "<a href = \"http://cal.syoboi.jp/tid/$tid/time#$pid\" target=\"_blank\">$subtitle</a> ";
}else{
print "$subtitle ";
}
print htmlspecialchars($rowdata[1]) . " ";
print htmlspecialchars($rowdata[6]) . "ʬ ";
print htmlspecialchars(foldate2print($rowdata[5]));
print "<br /><br />";
$mp4filename = $rowdata[9];
$serverfqdn = getserverfqdn();


$m2pfilename = $rowdata[8];

list($tid,$countno,$date,$time)= split ("-", $m2pfilename );
	$tid = ereg_replace("[^0-9]", "", $tid);

$path = ereg_replace("\.m2p$", "", $m2pfilename);
$serveruri = getserverfqdn ();

print "</div>\n";

//����

print "<img src='http://$serveruri$httpmediamappath/$tid.localized/img/$path/$filename' width='160' height='120' alt='$tid:$countno:$filetid' align=\"left\">\n";


if (getform(preview) == 1){
//�ץ�ӥ塼ɽ��
// htmlspecialchars(stripslashes( )) 
$subject = getform(subject); 
$maintext = $_POST["textarea"];
$maintext = pg_escape_string($maintext);
//$maintext = mbereg_replace("\n","<br />\n", $maintext);
$rate = getform(rank4);

switch ($rate) {
	case -2:
		$ratechara =  "�� ";
	break;
	case -1:
	$ratechara =  "�� ";
	break;
	case 0:
	$ratechara =  "�� ";
	break;
	case 1:
	$ratechara =  "�� ";
	break;
	case 2:
	$ratechara =  "���� ";
	break;
	case 3:
	$ratechara =  "������ ";
	break;
	case 4:
	$ratechara =  "�������� ";
	break;
	default:
	$ratechara =  "�� ";
}
$subject = $ratechara . $subject;

print "". htmlspecialchars(stripslashes( $subject)) ."\n";
print "". stripslashes( $maintext) ."<br />\n";
print "<br />\n";
print "��ʸ(source view):<br />". htmlspecialchars(stripslashes( $maintext)) ."<hr><br /><br /><br />\n";

print "<form id=\"form2\" name=\"form2\" method=\"post\" action=\"./sb-write.php?tid=$tid&path=$path&f=$filename\"><input type=\"password\" name=\"blogpw\">[ <a href = \"./sb-write.php?tid=$tid&path=$path&f=$filename\" target=\"_blank\">Send Picture Only</a> ] [ <input type=\"hidden\" name=\"subjects\" value=\"" . urlencode(stripslashes($subject)) . "\" /><input type=\"hidden\" name=\"maintext\" value=\"" . urlencode(stripslashes($maintext)) . "\" /><input type=submit value=\" Blog Write \"> ]</form>";


}else{//�Խ��񤭹��ߥ⡼��
//�����ȥ�
if ($tid == 0){
	$subjects = "��".$subtitle."��";
}else{
	if ($countno == ""){
	$subjects = "$title ��".$subtitle."��";
	}else{
	$subjects = "$title ��". $countno ." ��".$subtitle."��";
	}
}
print "<form id=\"form1\" name=\"form1\" method=\"post\" action=\"./sb-edit.php?pid=$pid&f=$filename\">
<input type=\"text\" name=\"subject\" size=\"70\"value=\"$subjects \"><br />
			<select class='hosi' name='rank4' size='1'>
				<option value='-2'>�߸��ڤ�
				<option value='-1'>�����ڤ����
				<option value='0'>�ݸ��Ƥʤ�
				<option value='1' selected=\"selected\">���դĤ�
				<option value='2'>�������⤷��
				<option value='3'>������̾��
				<option value='4'>����������Ʋ
			</select> 
<br />
<br />
<input type=\"hidden\" name=\"preview\" value=\"1\" />

            <textarea name=\"textarea\" rows=\"40\" cols=\"55\">
";
if ($tid > 0){
print "
<br />
���ͥ��:<a href = \"http://cal.syoboi.jp/tid/$tid/\" target=\"_blank\"> $title</a> "; 
	if ($countno != ""){ 
	print "��". $countno ."�� ";
	}
print"<a href = \"http://cal.syoboi.jp/tid/$tid/time#$pid\" target=\"_blank\">$subtitle</a> (����:<a href = \"http://cal.syoboi.jp/\">����ܤ���������</a>)";
}
print "			</textarea><br />
  <input type=submit value=\" �֥�ӥ塼 \">
</form>

";
}//�ץ�ӥ塼ɽ�����ɤ���
/*
ToDo
��Form�ץ�ӥ塼
���ѥ֥�å���ܥ���
��
*/

// �����ȥ�����������ޤ�
}//if rowdata == 0

?>

</body>
</html>
