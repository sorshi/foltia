<?php
/*
 Anime recording system foltia
 http://www.dcc-jpl.com/soft/foltia/

accountregist.php

��Ū
���Ķ��ݥꥷ���Τ���Υ桼��������Ͽ


����


 DCC-JPL Japan/foltia project

*/
?>

<?php
  include("./foltialib.php");

$con = m_connect();
$now = date("YmdHi");   
$errflag = 0;
$errmsg = "";


function printtitle(){
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
<html lang=\"ja\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"graytable.css\"> ";

print "<title>foltia:���������������Ͽ</title>
</head>";
}//end function printtitle()

printtitle();
?>
<body BGCOLOR="#ffffff" TEXT="#494949" LINK="#0047ff" VLINK="#000000" ALINK="#c6edff" >

<p align="left"><font color="#494949" size="6">
���������������Ͽ
</font></p>
<hr size="4">
<?php
//�ͼ���
$username = getform(username);
$userpasswd = getform(userpasswd);
if ($username == "") {
	print "<p align=\"left\">���������������Ͽ�򤷤ޤ���</p>\n";

}else{
//���Ǥˤ��Υ桼����¸�ߤ��Ƥ��뤫�ɤ�����ǧ
if ($username != ""){
$query = "
SELECT memberid ,userclass,name,passwd1 
FROM foltia_envpolicy 
WHERE foltia_envpolicy.name  = '$username'  
";
	$isaccountexist = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$isaccountexistncount = pg_num_rows($isaccountexist);

	if ($isaccountexistncount == 0){
	//valid
	}else{
		$errflag = 1;
		$errmsg = "���Υ桼��̾�ϴ��˻Ȥ��Ƥ��ޤ���";
	}
}
if ($userpasswd == ""){
		$errflag = 2;
		$errmsg = "�ѥ���ɤ���Ŭ�ڤǤ���Ⱦ�ѱѿ�����ꤷ�Ʋ�������";
}


if ($errflag == 0){
// next mid��õ��
$query = "
SELECT max(memberid) 
FROM  foltia_envpolicy 
";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
	if ($maxrows == 0){
	$nextcno = 1 ;
	}else{
	$rowdata = pg_fetch_row($rs, 0);
	$nextcno = $rowdata[0];
	$nextcno++ ;
	}

//��Ͽ
//INSERT
if ($demomode){
}else{
/*
�桼�����饹
0:�ø�������
1:������:ͽ�������ե��������������
2:���Ѽ�:EPG�ɲá�ͽ���ɲä������
3:�ӥ奢��:�ե������������ɤ������
4:������:���󥿡��ե������������
*/
$remotehost = gethostbyaddr($_SERVER['REMOTE_ADDR']);

$query = "
insert into foltia_envpolicy  
values ( '$nextcno','2','$username','$userpasswd',now(),'$remotehost')";
//print "$query <br>\n";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");

print "���Υ�������Ȥ���Ͽ���ޤ�����<br>
������̾:$username<br>
�ѥ����:$userpasswd";

if ($environmentpolicytoken != ""){
	print "�ܥ������ƥ�������<br>\n";
}
print "<a href=\"./index.php\">������</a><br>\n";

print "</body>
</html>
";
	$oserr = system("$toolpath/perl/envpolicyupdate.pl");
exit;

}//endif �ǥ�⡼��
}else{//error�ե饰���ä���
print "$errmsg / $errflag<br>\n";

}//end if ���顼����ʤ����

}//end if ""
?>

<form id="account" name="account" method="post" action="./accountregist.php">
  <p>��Ͽ�桼��̾:
    <input name="username" type="text" id="username" size="19" value="" />
  (Ⱦ�ѱѿ��Τ�)</p>
  <p>��Ͽ�ѥ����:
    <input name="userpasswd" type="text" id="userpasswd" size="19" value="" />
  (Ⱦ�ѱѿ��Τ�)</p>

<input type="submit" value="������Ͽ">��
</form>

</body>
</html>
