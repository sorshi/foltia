<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

m.php

��Ū
����ɽ���Ѥ��ʤ�������ưϿ��ͽ���¸����ޤ���
���������ʤɤ�ͽ�󤹤���⤳���򳫤��Ȥ褵�����Ǥ���

����
startdate:Ͽ�賫���� (ex.20051207)
starttime:Ͽ�賫�ϻ��� (ex.2304)
lengthmin:Ͽ���ʬ
recstid:Ͽ���ID
pname:����̾

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

$now = date("YmdHi");   
$today = date("Ymd");
$nowdate = date("Hi",(mktime(date("G"),date("i")+8,date("s"),date("m"),date("d"),date("Y"))));
$errflag = 0;
$pname = "��ưϿ��";

function printtitle(){
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html lang=\"ja\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"graytable.css\"> ";

print "<title>foltia:���ȼ�ưͽ��</title>
</head>";
}//end function printtitle()

printtitle();
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >
<div align="center">
<?php 
printhtmlpageheader();
?>
</div>
<p align="left"><font color="#494949" size="6">
���ȼ�ưͽ��
</font></p>
<hr size="4">
<?php
//�ͼ���
$startdate = getgetnumform(startdate);
$starttime = getgetnumform(starttime);

if (($startdate == "") || ($starttime == "")){
	print "<p align=\"left\">�����ܼ�ư�����ͽ�󤷤ޤ���</p>\n";
}else{

$lengthmin = getgetnumform(lengthmin);
$recstid = getgetnumform(recstid);
$pname = getgetform(pname);
//$usedigital = getgetnumform(usedigital);

//��ǧ
$startdatetime = "$startdate"."$starttime";
if (foldatevalidation($startdatetime)){
//print "valid";
}else{
	$errflag = 1;
	$errmsg = "���դ������Ǥ���";
}
if ($lengthmin < 361){
//valid
}else{
	$errflag = 2;
	$errmsg = "Ͽ����֤�360ʬ�Ƕ��ڤäƤ���������";
}
//�ɳ�ǧ
if ($recstid != ""){
$query = "
SELECT stationname  
FROM foltia_station 
WHERE stationid = ? ";
//	$stationvalid = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$stationvalid = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($recstid));
		$recstationname = $stationvalid->fetch();
		if (! $recstationname) {
		$errflag = 3;
		$errmsg = "���������꤬�۾�Ǥ���";
	}
}
//�ǥ�����ͥ��
/*if ($usedigital == 1){
}else{
	$usedigital = 0;
}
*/
//���������
if ($errflag == 0){
//��ʣ�����뤫?
//̤�����å�

//�ǥ�⡼�ɤ���ʤ��ä���񤭹���
$enddatetime = calcendtime($startdatetime,$lengthmin);

//���︡��
if (($startdatetime > $now ) && ($enddatetime > $now ) && ($enddatetime  > $startdatetime ) ){

//min pid��õ��
$query = "SELECT min(pid) FROM  foltia_subtitle ";
//	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$rowdata = $rs->fetch();
	if (! $rowdata) {
		$insertpid = -1 ;
	}else{
		if ($rowdata[0] > 0) {
			$insertpid = -1 ;
		}else{
			$insertpid = $rowdata[0];
			$insertpid-- ;
		}
	}
// next �ÿ���õ��
$query = "SELECT max(countno) FROM  foltia_subtitle WHERE tid = 0";
//	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���");
			$rowdata = $rs->fetch();
			if (! $rowdata) {
	$nextcno = 1 ;
	}else{
	$nextcno = $rowdata[0];
	$nextcno++ ;
	}

//INSERT
if ($demomode){
}else{
	$userclass = getuserclass($con);
	if ( $userclass <= 2){
	$memberid = getmymemberid($con);
	
	$query = "
	insert into foltia_subtitle  (pid ,tid ,stationid , countno ,subtitle ,
startdatetime ,enddatetime ,startoffset , lengthmin , epgaddedby )  
	values ( ?,'0',?,?,?,?,?,'0',?,?)";
	
//		$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
//print "��DEBUG��$insertpid,$recstid,$nextcno,$pname,$startdatetime,$enddatetime ,$lengthmin,$memberid <br>\n";
		$rs = sql_query($con, $query, "DB������˼��Ԥ��ޤ���",array($insertpid,$recstid,$nextcno,$pname,$startdatetime,$enddatetime ,$lengthmin,$memberid));
	
	//addatq.pl
	//���塼����ץ����򥭥å�
	//������TID �����ͥ�ID
	//echo("$toolpath/perl/addatq.pl $tid $station");
	exec("$toolpath/perl/addatq.pl 0 0");
	$oserr = system("$toolpath/perl/addatq.pl 0 0");
	//---------------------------------------------------
			if ($oserr){
			print "[DEBUG]$oserr ��$toolpath/perl/addatq.pl 0 0��<br>\n";
		}else{
			print "[DEBUG]exec addatq.pl false ��$toolpath/perl/addatq.pl 0 0��<br>\n";
			
			$oserr = system("$toolpath/perl/perltestscript.pl");
			if ($oserr){
				print "[DEBUG]exec perltestscript.pl $oserr<br>\n";
			}else{
				print "[DEBUG]exec perltestscript.pl false <br>\n";
			}
			
		}
	//-----------------------------------------------------
	}else{
		print "EPGͽ���Ԥ����¤�����ޤ���";
	}// end if $userclass <= 2
}//end if demomode

print "����ͽ���λ�������ޤ�����<br>";
//���ɽ��
print "Ͽ�賫��:";
echo foldate2print($startdatetime);
print "<br />
Ͽ�轪λ:";
echo foldate2print($enddatetime);
print "<br />
Ͽ���: $lengthmin ʬ<br />
Ͽ���:$recstationname[0]<br />
����̾:$pname<br />
";
exit();
}else{
print "���郎�����ʤ����ͽ��Ǥ��ޤ���Ǥ����� <br>";

}


}else{
	print "���Ϲ��ܤ��������ʤ������Ǥ���$errmsg<br />\n";
}

}//�����ɽ�����ǡ���������
?>
<form id="record" name="record" method="get" action="./m.php" autocomplete="off">
  <p>������:
    <input name="startdate" type="text" id="startdate" size="9" value="<?=$startdate?>" />
  ǯ���� Ex.<?=$today?></p>
  <p>Ͽ�賫�ϻ���:
    <input name="starttime" type="text" id="starttime" size="5" value="<?=$starttime?>" />
  ��ʬ Ex.<?=$nowdate?>  </p>
  <p>
    Ͽ���:
      <input name="lengthmin" type="text" id="lengthmin" size="4" value="<?=$lengthmin?>"/> 
    ʬ (��Ĺ360ʬ) </p>

  <p>Ͽ���:
<?php
$query = "
SELECT stationid as x, stationname, stationrecch, digitalch 
FROM foltia_station 
WHERE stationrecch > 0 
UNION 
SELECT DISTINCT  stationid,stationname,stationrecch ,digitalch 
FROM  foltia_station 
WHERE digitalch > 0 
ORDER BY x ASC";

$stations = sql_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rowdata = $stations->fetch();

if ($rowdata) {
			   do {
			if ($recstid == $rowdata[0]){
			print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" checked />  $rowdata[1] ($rowdata[2]ch / $rowdata[3]ch)��\n";
			}else{
				print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" />  $rowdata[1] ($rowdata[2]ch / $rowdata[3]ch)��\n";
			}
			   } while ($rowdata = $stations->fetch());
}else{
print "�����ɥǡ����١��������������åȥ��åפ���Ƥ��ޤ���Ͽ���ǽ�ɤ�����ޤ���";
}
//�������ϥ����ͥ�
$query = "
SELECT stationid as x ,stationname,stationrecch 
FROM foltia_station 
WHERE stationrecch > -2 AND stationrecch < 1 
ORDER BY x ASC";

//	$stations = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$stations = sql_query($con, $query, "DB������˼��Ԥ��ޤ���");
$rowdata = $stations->fetch();	
if ($rowdata) {
	do {
		if ($rowdata[0] != 0){
			if ($recstid == $rowdata[0]){
			print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" checked />  $rowdata[1]��\n";
			}else{
				print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" />  $rowdata[1]��\n";
			}

		}
	} while ($rowdata = $stations->fetch());
}
/*
print "<p>�ǥ�����Ͽ���ͥ��:";

if ($usedigital == 1){
print "<input name="useditial" type="radio" value="1" selected />  ���롡
<input name="useditial" type="radio" value="0" />  ���ʤ���
";
}else{
print "<input name="useditial" type="radio" value="1" />  ���롡
<input name="useditial" type="radio" value="0" selected />  ���ʤ���
";
}
*/
?>
  <p>����̾:
    <input name="pname" type="text" id="pname" value="<?=$pname ?>" />
  </p>
<!-- <p  style='background-color: #DDDDFF'>
�����֤�����-�轵�ʲ���������Ͽ��:
<input name="weeklyloop" type="radio" value="128" />  ���ˡ�
<input name="weeklyloop" type="radio" value="64" />  ���ˡ�
<input name="weeklyloop" type="radio" value="32" />  ���ˡ�
<input name="weeklyloop" type="radio" value="16" />  ���ˡ�
<input name="weeklyloop" type="radio" value="8" />  ���ˡ�
<input name="weeklyloop" type="radio" value="4" />  ���ˡ�
<input name="weeklyloop" type="radio" value="2" />  ���ˡ�
 </p>
 -->
<input type="submit" value="ͽ��">��
</form>

</body>
</html>
