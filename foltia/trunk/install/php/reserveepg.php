<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

reserveepg.php

��Ū
EPGϿ��ͽ��ڡ�����ɽ�����ޤ���

����
epgid:EPG����ID

 DCC-JPL Japan/foltia project

*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" type="text/css" href="graytable.css"> 

<?php

  include("./foltialib.php");
  
$epgid = getgetnumform(epgid);
		if ($epgid == "") {
		print "	<title>foltia:EPGͽ��:Error</title></head>\n";
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}
print "	<title>foltia:EPGͽ��:$epgid</title>
</head>\n";


$con = m_connect();
$now = date("YmdHi");   

//�����ȥ����
	$query = "
	SELECT epgid,startdatetime,enddatetime,lengthmin, ontvchannel,epgtitle,epgdesc,epgcategory , 
	stationname , stationrecch ,stationid 
	FROM foltia_epg , foltia_station 
	WHERE epgid='$epgid' AND foltia_station.ontvcode = foltia_epg.ontvchannel
	";//4812
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
			
		if ($maxrows == 0) {
		die_exit("��Ͽ���Ȥ�����ޤ���<BR>");
		}
		$rowdata = pg_fetch_row($rs, 0);
		//$title = htmlspecialchars($rowdata[0]);
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<?php 
	printhtmlpageheader();
?>

  <p align="left"><font color="#494949" size="6">����ͽ��</font></p>
  <hr size="4">
EPG���鲼�����Ȥ�Ͽ��ͽ�󤷤ޤ��� <br>
<form name="recordingsetting" method="POST" action="reserveepgcomp.php">
<input type="submit" value="ͽ��" >
<br>


<?php	
$stationjname = htmlspecialchars($rowdata[8]);
$startfoltime = htmlspecialchars($rowdata[1]);
$startprinttime =  foldate2print($startfoltime);
$endfoltime = htmlspecialchars($rowdata[2]);
$endprinttime = foldate2print($endfoltime);
$lengthmin = htmlspecialchars($rowdata[3]);
$recch = htmlspecialchars($rowdata[9]);
$progname = htmlspecialchars($rowdata[5]);
$progname = z2h($progname);
$progdesc = htmlspecialchars($rowdata[6]);
$progdesc =  z2h($progdesc);
$progcat = htmlspecialchars(z2h($rowdata[7]));

if ($progcat == "information"){
$progcat =  '����';
}elseif ($progcat == "anime"){
$progcat =  '���˥ᡦ�û�';
}elseif ($progcat == "news"){
$progcat =  '�˥塼������ƻ';
}elseif ($progcat == "drama"){
$progcat =  '�ɥ��';
}elseif ($progcat == "variety"){
$progcat =  '�Х饨�ƥ�';
}elseif ($progcat == "documentary"){
$progcat =  '�ɥ����󥿥꡼������';
}elseif ($progcat == "education"){
$progcat =  '����';
}elseif ($progcat == "music"){
$progcat =  '����';
}elseif ($progcat == "cinema"){
$progcat =  '�ǲ�';
}elseif ($progcat == "hobby"){
$progcat =  '��̣������';
}elseif ($progcat == "kids"){
$progcat =  '���å�';
}elseif ($progcat == "sports"){
$progcat =  '���ݡ���';
}elseif ($progcat == "etc"){
$progcat =  '����¾';
}elseif ($progcat == "stage"){
$progcat =  '���';
}

$epgid = $epgid ;
$stationid = htmlspecialchars($rowdata[10]);

if ($now > $endfoltime){
	print "�������ȤϤ��Ǥ˽�λ���Ƥ��뤿�ᡢϿ�褵��ޤ���<br>";
}elseif($now > $startfoltime){
	print "�������ȤϤ��Ǥ����ǳ��Ϥ��Ƥ��뤿�ᡢϿ�褵��ޤ���<br>";
}elseif($now > ($startfoltime - 10) ){
	print "�������Ȥ�����ľ���ʤ��ᡢϿ�褵��ʤ���ǽ��������ޤ���<br>";
}

//��ʣ��ǧ

	$query = "
SELECT  foltia_program.title,foltia_subtitle.tid,foltia_subtitle.pid 
FROM foltia_subtitle ,foltia_program ,foltia_tvrecord 
WHERE startdatetime ='$startfoltime' 
AND enddatetime = '$endfoltime' 
AND foltia_subtitle.stationid = '$stationid'  
AND foltia_program.tid = foltia_subtitle.tid 
AND foltia_tvrecord.tid =  foltia_program.tid 
AND foltia_tvrecord.stationid = foltia_subtitle.stationid 
";	
	
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);

		if ($maxrows == 0) {
		//��ʣ�ʤ�
		}else{
		$chkoverwrap = pg_fetch_row($rs, 0);
		$prereservedtitle = htmlspecialchars($chkoverwrap[0]);
		$tid =  htmlspecialchars($chkoverwrap[1]);
		$pid =  htmlspecialchars($chkoverwrap[2]);
		print "<strong>�������Ȥϴ���ͽ��ѤߤǤ���</strong>��\n";
			if ($tid > 1){
			print "ͽ������̾:<a href=\"http://cal.syoboi.jp/tid/$tid/time/#$pid\" target=\"_blank\">$prereservedtitle</a><br>\n";
			}else{
			print "ͽ����ˡ:EPGϿ��<br>\n";
			}
		}
		


print "<table width=\"100%\" border=\"0\">
    <tr><td>������</td><td>$stationjname</td></tr>
    <tr><td>��������</td><td>$startprinttime</td></tr>
    <tr><td>������λ</td><td>$endprinttime</td></tr>
    <tr><td>��(ʬ)</td><td>$lengthmin</td></tr>
    <tr><td>���������ͥ�</td><td>$recch</td></tr>
    <tr><td>����̾</td><td>$progname</td></tr>
    <tr><td>����</td><td>$progdesc</td></tr>
    <tr><td>������</td><td>$progcat</td></tr>
    <tr><td>����ID</td><td>$epgid</td></tr>
    <tr><td>�ɥ�����</td><td>$stationid</td></tr>
	
</table>

<input type=\"hidden\" name=\"stationid\" value=\"$stationid\" />
<input type=\"hidden\" name=\"subtitle\" value=\"$progname $progdesc\" />
<input type=\"hidden\" name=\"startdatetime\" value=\"$startfoltime\" />
<input type=\"hidden\" name=\"enddatetime\" value=\"$endfoltime\" />
<input type=\"hidden\" name=\"lengthmin\" value=\"$lengthmin\" />

";

    
?>

</FORM>


</body>
</html>
