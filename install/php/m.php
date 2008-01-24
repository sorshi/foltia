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
?>

<?php
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
WHERE stationid = $recstid";
	$stationvalid = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$stationcount = pg_num_rows($stationvalid);

	if ($stationcount == 1){
		$recstationname = pg_fetch_row($stationvalid, 0);
	//valid
	}else{
		$errflag = 3;
		$errmsg = "���������꤬�۾�Ǥ���";
	}
}
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
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
	if ($maxrows == 0){
	$insertpid = -1 ;
	}else{
	$rowdata = pg_fetch_row($rs, 0);
	$insertpid = $rowdata[0];
	$insertpid-- ;
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
}else{

$query = "
insert into foltia_subtitle  
values ( '$insertpid','0','$recstid',
	'$nextcno','$pname','$startdatetime','$enddatetime','0' ,'$lengthmin')";

	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");

//addatq.pl
//���塼����ץ����򥭥å�
//������TID �����ͥ�ID
//echo("$toolpath/perl/addatq.pl $tid $station");

	$oserr = system("$toolpath/perl/addatq.pl 0 0");

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
<form id="record" name="record" method="get" action="./m.php">
  <p>������:
    <input name="startdate" type="text" id="startdate" size="9" value="<?=$startdate?>" />
  ǯ���� Ex.19800121</p>
  <p>Ͽ�賫�ϻ���:
    <input name="starttime" type="text" id="starttime" size="5" value="<?=$starttime?>" />
  ��ʬ Ex.2304  </p>
  <p>
    Ͽ���:
      <input name="lengthmin" type="text" id="lengthmin" size="4" value="<?=$lengthmin?>"/> 
    ʬ (��Ĺ360ʬ) </p>

  <p>Ͽ���:
<?php
$query = "
SELECT stationid,stationname,stationrecch 
FROM foltia_station 
WHERE stationrecch > 0 
ORDER BY \"stationid\" ASC";

	$stations = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$stationcount = pg_num_rows($stations);
	
if ($stationcount > 0 ){
	for ($row = 0; $row < $stationcount ; $row++) {
		$rowdata = pg_fetch_row($stations, $row);
			if ($recstid == $rowdata[0]){
			print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" checked />  $rowdata[1] ($rowdata[2]ch)��\n";
			}else{
				print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" />  $rowdata[1] ($rowdata[2]ch)��\n";
			}
	}
}else{
print "�����ɥǡ����١��������������åȥ��åפ���Ƥ��ޤ���Ͽ���ǽ�ɤ�����ޤ���";
}

$query = "
SELECT stationid,stationname,stationrecch 
FROM foltia_station 
WHERE stationrecch > -2 AND stationrecch < 1 
ORDER BY \"stationid\" ASC";

	$stations = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$stationcount = pg_num_rows($stations);
	
if ($stationcount > 0 ){
	for ($row = 0; $row < $stationcount ; $row++) {
		$rowdata = pg_fetch_row($stations, $row);
		if ($rowdata[0] != 0){
			if ($recstid == $rowdata[0]){
			print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" checked />  $rowdata[1]��\n";
			}else{
				print " <input name=\"recstid\" type=\"radio\" value=\"$rowdata[0]\" />  $rowdata[1]��\n";
			}

		}
	}
}

?>
  <p>����̾:
    <input name="pname" type="text" id="pname" value="<?=$pname ?>" />
  </p>
<p  style='background-color: #DDDDFF'>
�����֤�����-�轵�ʲ���������Ͽ��:
<input name="weeklyloop" type="radio" value="128" />  ���ˡ�
<input name="weeklyloop" type="radio" value="64" />  ���ˡ�
<input name="weeklyloop" type="radio" value="32" />  ���ˡ�
<input name="weeklyloop" type="radio" value="16" />  ���ˡ�
<input name="weeklyloop" type="radio" value="8" />  ���ˡ�
<input name="weeklyloop" type="radio" value="4" />  ���ˡ�
<input name="weeklyloop" type="radio" value="2" />  ���ˡ�
 </p>
 
<input type="submit" value="ͽ��">��
</form>

</body>
</html>
