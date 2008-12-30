<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

reserveepgcomp.php

��Ū
EPG���Ȥ�ͽ����Ͽ�򤷤ޤ���

����
stationid:Ͽ���ID
subtitle:����̾
startdatetime:Ͽ�賫�ϻ��� (ex.200510070145)
enddatetime:Ͽ�轪λ���� (ex.200510070215)
lengthmin:Ͽ�����(ñ��:ʬ)

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
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 

	printhtmlpageheader();
?>
  <p align="left"><font color="#494949" size="6">����ͽ��</font></p>
  <hr size="4">
<?php

$stationid = getnumform(stationid);
$subtitle = getform(subtitle);
$startdatetime = getnumform(startdatetime);
$enddatetime = getnumform(enddatetime);
$lengthmin = getnumform(lengthmin);

		if ($stationid == "" || $startdatetime < 0 ||  $enddatetime < 0 || $lengthmin < 0) {
		print "	<title>foltia:EPGͽ��:Error</title></head>\n";
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}
print "	<title>foltia:EPGͽ��:��λ</title>
</head>\n";
$now = date("YmdHi");   
// - DB��Ͽ���

//���︡��
if (($startdatetime > $now ) && ($enddatetime > $now ) && ($enddatetime  > $startdatetime ) ){

//min pid��õ��
$query = "SELECT min(pid) FROM  foltia_subtitle ";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
	if ($maxrows == 0){
	$insertpid = -1 ;
	}else{
	$rowdata = pg_fetch_row($rs, 0);
	
	$insertpid = $rowdata[0];
		if ($insertpid > 0){
		$insertpid = -1;
		}else{
		$insertpid-- ;
		}
	}
// next �ÿ���õ��
$query = "SELECT max(countno) FROM  foltia_subtitle WHERE tid = 0";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
	if ($maxrows == 0){
	$nextcno = 1 ;
	}else{
	$rowdata = pg_fetch_row($rs, 0);
	$nextcno = $rowdata[0];
	$nextcno++ ;
	}

//INSERT
if ($demomode){
	print "����ͽ���λ�������ޤ�����<br>";
}else{
$userclass = getuserclass($con);
if ( $userclass <= 2){
/*
pid 
tid 
stationid  
countno 
subtitle
startdatetime  
enddatetime  
startoffset  
lengthmin  
m2pfilename 
pspfilename 
epgaddedby  

*/

$memberid = getmymemberid($con);
	$query = "
insert into foltia_subtitle  (pid ,tid ,stationid , countno ,subtitle ,
startdatetime ,enddatetime ,startoffset , lengthmin , epgaddedby ) 
values ( '$insertpid','0','$stationid',
	'$nextcno','$subtitle','$startdatetime','$enddatetime','0' ,'$lengthmin' , '$memberid')";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");

	//addatq.pl
	//���塼����ץ����򥭥å�
	//������TID �����ͥ�ID
	//echo("$toolpath/perl/addatq.pl $tid $station");

	$oserr = system("$toolpath/perl/addatq.pl 0 0");
	print "����ͽ���λ�������ޤ�����<br>";
}else{
	print "EPGͽ���Ԥ����¤�����ޤ���";
}// end if $userclass <= 2
}//end if demomode



}else{
print "���郎�����ʤ����ͽ��Ǥ��ޤ���Ǥ����� <br>";

}


print "<table width=\"100%\" border=\"0\">
    <tr><td>��������</td><td>$startdatetime</td></tr>
    <tr><td>������λ</td><td>$enddatetime</td></tr>
    <tr><td>�ɥ�����</td><td>$stationid</td></tr>
    <tr><td>��(ʬ)</td><td>$lengthmin</td></tr>
    <tr><td>����̾</td><td>$subtitle</td></tr>
	
</tbody>
</table>";

?>
</body>
</html>
