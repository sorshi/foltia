<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

delepgp.php

��Ū
EPGϿ��ͽ���ͽ������Ԥ��ޤ�

����
pid:�ץ����ID
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
<title>foltia:delete EPG Program</title>
</head>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php

	printhtmlpageheader();

$pid = getgetnumform(pid);
		if ($pid == "") {
		die_exit("���Ȥ�����ޤ���<BR>");
		}

$now = date("YmdHi");   


//�����ȥ����
$query = "
SELECT 
foltia_subtitle.pid  , 
foltia_subtitle.stationid , 
foltia_subtitle.countno , 
foltia_subtitle.subtitle  ,
foltia_subtitle.startdatetime , 
foltia_subtitle.enddatetime ,
foltia_subtitle.lengthmin ,
foltia_station.stationname , 
foltia_station.stationrecch 
FROM foltia_subtitle , foltia_station 
WHERE foltia_subtitle.tid = 0 AND 
foltia_station.stationid = foltia_subtitle.stationid AND 
foltia_subtitle.pid = $pid 
 ";

	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}
		$rowdata = pg_fetch_row($rs, 0);

		$pid = htmlspecialchars($rowdata[0]);
		$stationid = htmlspecialchars($rowdata[1]);
		$countno = htmlspecialchars($rowdata[2]);
		$subtitle = htmlspecialchars($rowdata[3]);
		$starttime = htmlspecialchars($rowdata[4]);
		$startprinttime = htmlspecialchars(foldate2print($rowdata[4]));
		$endtime = htmlspecialchars($rowdata[5]);
		$endprinttime = htmlspecialchars(foldate2print($rowdata[5]));
		$lengthmin = htmlspecialchars($rowdata[6]);
		$stationjname = htmlspecialchars($rowdata[7]);
		$recch = htmlspecialchars($rowdata[8]);
$delflag = getgetnumform(delflag);
?>

  <p align="left"><font color="#494949" size="6">EPGͽ����</font></p>
  <hr size="4">
<?php
if ($delflag == "1") {
	//�����ǧ
	if ($now < $starttime ){
		print "EPGͽ���".$subtitle."�פ�Ͽ��ͽ��������ޤ����� <br>\n";
		
		//�������
		if (($demomode) || ($protectmode) ){
		//demomode��protectmode�ʤ�ʤˤ⤷�ʤ�
		}else{
		//���塼����
//		$oserr = system("$toolpath/perl/addatq.pl 0 $stationid ");
		$oserr = system("$toolpath/perl/addpidatq.pl $pid ");
		//DB���
		$query = "
		DELETE  
		FROM  foltia_subtitle  
		WHERE foltia_subtitle.pid = $pid AND  foltia_subtitle.tid = 0 ";
			$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
		}
	}else{
		print "<strong>������Ȥ�ͽ��������ޤ���</strong>";
	}//end if

}else{//delflag��1����ʤ����

	//�����ǧ
	if ($now < $starttime ){
	print "EPGͽ���".$subtitle."�פ�Ͽ��ͽ��������ޤ��� <br>\n";

	print "<form name=\"deletereserve\" method=\"GET\" action=\"delepgp.php\">
	<input type=\"submit\" value=\"ͽ����\" >\n";
	}else{
	print "<strong>������Ȥ�ͽ��������ޤ���</strong>";
	}//end if
}

print "<br>
	<table width=\"100%\" border=\"0\">
    <tr><td>������</td><td>$stationjname</td></tr>
    <tr><td>��������</td><td>$startprinttime</td></tr>
    <tr><td>������λ</td><td>$endprinttime</td></tr>
    <tr><td>��(ʬ)</td><td>$lengthmin</td></tr>
    <tr><td>���������ͥ�</td><td>$recch</td></tr>
    <tr><td>����̾</td><td>$subtitle</td></tr>
    <tr><td>����ID</td><td>$pid</td></tr>
    <tr><td>�ɥ�����</td><td>$stationid</td></tr>
	
</table>
";

if ($delflag == "1") {

}else{
print "
<input type=\"hidden\" name=\"pid\" value=\"$pid\">
<input type=\"hidden\" name=\"delflag\" value=\"1\">
</form>\n";

}

?>  
</table>

</body>
</html>
