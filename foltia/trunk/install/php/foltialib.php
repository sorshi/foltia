<?php
		
include("./foltia_config2.php");

/*
������Υ⥸�塼���
Apache + PHP + PostgreSQL �¸���
http://www.hizlab.net/app/
�Υ���ץ��Ȥ碌�Ƥ��������Ƥ���ޤ���
���꤬�Ȥ��������ޤ���
*/

	/* ���顼ɽ�������� */
	//error_reporting(0);

	
	//GET�ѥե�����ǥ�����
	  function getgetform($key) {
    if ($_GET["{$key}"] != "") {
		$value = $_GET["{$key}"];
                   escape_string($value);
                   htmlspecialchars($value);
	return ($value);
    }
  }
	//GET�ѿ����ե�����ǥ�����
	  function getgetnumform($key) {
    if ($_GET["{$key}"] != "") {
		$value = $_GET["{$key}"];
		escape_string($value);
		htmlspecialchars($value);
		$value = ereg_replace("[^0-9]", "", $value);
		$value = escape_numeric($value);
	return ($value);
    }
  }
	
	//�ե�����ǥ�����
	  function getform($key) {
			//    global $where;
    if ($_POST["{$key}"] != "") {
		$value = $_POST["{$key}"];
                   escape_string($value);
                   htmlspecialchars($value);
	return ($value);
    }
  }
	//�������ѥե�����ǥ�����
	  function getnumform($key) {
    if ($_POST["{$key}"] != "") {
		$value = $_POST["{$key}"];
                   escape_string($value);
                   htmlspecialchars($value);
                   $value = ereg_replace("[^0-9]", "", $value);
		$value = escape_numeric($value);
	return ($value);
    }
  }

	/* ���ѥ������ʲ����ƥ��ڡ����������ƥ���ǥå����Ѥˤ��� */
	function name2read($name) {
	$name = mb_convert_kana($name, "KVC", "EUC-JP");
	$name = mb_convert_kana($name, "s", "EUC-JP");
	$name = ereg_replace(" ", "", $name);

		return $name;
	}

	/* ������Ⱦ�Ѳ����ƿ��������ƥ���ǥå����Ѥˤ��� */
	function pnum2dnum($num) {
	$num = mb_convert_kana($num, "a", "EUC-JP");
	$num = ereg_replace("[^0-9]", "", $num);

		return $num;
	}
	
	/* ��λ�ؿ������ */
	function die_exit($message) {
		?>
		<p class="error"><?= $message ?></p>
		<div class="index"><a href="./">�ȥå�</a></div>
	</body>
</html><?
		exit;
	}
	
	/* ���Ϥ����ͤΥ�����������å� */
	function check_length($str, $maxlen, $must, $name) {
		$len = strlen($str);
		if ($must && $len == 0) {
			die_exit("$name �����Ϥ���Ƥޤ���ɬ�ܹ��ܤǤ���");
		}
		if ($len > $maxlen) {
			die_exit("$name �� $len ʸ���ʲ������Ϥ��Ʋ�����������ʸ���ϡ���ʸ������ʸ��ʬ�ȷ׻�����ޤ���");
		}
	}
	
	/* LIKE �Ѥ�ʸ����Υ��������� */
	function escape_like($sql, $quote = TRUE) {
		return ($quote ? "'" : "") .
		       str_replace(array("\\\\",     "%"    , "_"    ),
		                   array("\\\\\\\\", "\\\\%", "\\\\_"),
		                   pg_escape_string($sql)) .
		       ($quote ? "'" : "");
	}
	
	/* SQL ʸ����Υ��������� */
	function escape_string($sql, $quote = TRUE) {
		if ($quote && strlen($sql) == 0) {
			return "null";
		}
		return ($quote ? "'" : "") .
		       pg_escape_string($sql) .
		       ($quote ? "'" : "");
	}
	
	/* SQL ���ͤΥ��������� */
	function escape_numeric($sql) {
		if (strlen($sql) == 0) {
			return "null";
		}
		if (!is_numeric($sql)) {
			die_exit("$sql �Ͽ��ͤǤϤ���ޤ���");
		}
		return $sql;
	}
	
	/* PostgreSQL �����Ф���³ */
	function m_connect() { 
/*		$con = @pg_connect("host=".DBHOST ." dbname=".DATABASE_NAME .
		                   " user=".USER_NAME .
		                   " password=".USER_PASSWORD);
*/
		$con = @pg_pconnect("host=".DBHOST ." dbname=".DATABASE_NAME .
		                   " user=".USER_NAME .
		                   " password=".USER_PASSWORD);


		if (!$con) {
			die_exit("�ǡ����١�������³����ޤ���Ǥ�����");
		}
		/* �ǡ����١����ȡ�PHP ������ʸ�������ɤ��㤦��� */
		return($con);
	}

	/* �ǡ����١����Ȥ���³���ڤ�Υ�� */
	function m_close($con) {
		return @pg_close($con);
	}

	/* SQL ʸ��¹� */
	function m_query($con, $query, $errmessage) {
		$rtn = @pg_query($con, $query);
		if (!$rtn) {
			/* ���顼��å������� SQL ʸ��Ф��Τϥ������ƥ����ɤ��ʤ����� */
			$msg = $errmessage . "<br>\n" .
			       @pg_last_error($con) . "<br>\n" .
			       "<small><code>" . htmlspecialchars($query) .
			       "</code></small>\n";
			       $rtn = @pg_query($con, "rollback");//04.4.8
			m_close($con);
			die_exit($msg);
		}
		return($rtn);
	}

	/* select ������̤�ơ��֥��ɽ�� */
	function m_showtable($rs) {
		/* ������� */
		$maxrows = pg_num_rows($rs);
		
		if ($maxrows == 0) {
			echo("<p class=\"msg\">�ǡ�����¸�ߤ��ޤ���</p>\n");
			return 0;
		}
		
		/* �ե�����ɿ� */
		$maxcols = pg_num_fields($rs);
		?>
<table class="list" summary="�ǡ���������̤�ɽ��" border="1">
	<thead>
		<tr>
			<?php
				/* �ơ��֥�Υإå�������� */
				for ($col = 1; $col < $maxcols; $col++) {
					/* pg_field_name() �ϥե������̾���֤� */
					$f_name = htmlspecialchars(pg_field_name($rs, $col));
					echo("<th abbr=\"$f_name\">$f_name</th>\n");
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
			for ($row = 0; $row < $maxrows; $row++) { /* �Ԥ��б� */
				echo("<tr>\n");
				/* pg_fetch_row �ǰ�Լ��Ф� */
				$rowdata = pg_fetch_row($rs, $row);
				/* �����ܤ˥�󥯤�ĥ�� */
				echo("<td><a href=\"edit.php?q_code=" .
				     urlencode($rowdata[0]) . "\">" .
				     htmlspecialchars($rowdata[1]) . "</a></td>\n");
				for ($col = 2; $col < $maxcols; $col++) { /* ����б� */
					echo("<td>".htmlspecialchars($rowdata[$col])."<br></td>\n");
				}
				echo("</tr>\n");
			}
		?>
	</tbody>
</table>
		<?php
		return $maxrows;
	}

	/* ���ꤷ�������ɤΥǡ�����ɽ�� */
	function m_viewdata($con, $code) {
		/* �����ɤ˳�������ǡ����򸡺� */
		$query = "
select p.code
      ,p.name
      ,p.email
      ,p.pseudonym
      ,s.name as job
      ,p.profile
      ,to_char(p.editdate, 'YYYY/MM/DD HH24:MI:SS') as editdate
  from inet_profile p left join inet_job s on p.job = s.code
 where p.code = $code";
		$rs = m_query($con, $query, "�Ŀ;���μ����˼��Ԥ��ޤ�����");
		if (pg_num_rows($rs) == 0) {
			echo("<p class=\"msg\">�ǡ�����¸�ߤ��ޤ���</p>\n");
			return FALSE;
		}
		
		/* �ե�����ɿ� */
		$maxcols = pg_num_fields($rs);
		/* ��Ƭ�� */
		$rowdata = pg_fetch_row($rs, 0);
		?>
<table class="view" summary="�ǡ����١�����Υǡ�����ɽ��" border="1">
	<tr>
		<td class="name"><?= htmlspecialchars(pg_field_name($rs, 1)) ?></td>
		<td><a href="edit.php?q_code=<?= $rowdata[0] ?>"
		     ><?= htmlspecialchars($rowdata[1]) ?></a></td>
	</tr>
	<?php for ($col = 2; $col < $maxcols; $col++) { ?>
	<tr>
		<td class="name"><?= htmlspecialchars(pg_field_name($rs, $col)) ?></td>
		<td><?= htmlspecialchars($rowdata[$col]) ?></td>
	</tr>
	<?php } ?>
</table>
		<?php
		/* �����꡼����� */
		pg_free_result($rs);
		
		return TRUE;
	}
	

function printhtmlpageheader(){

global $useenvironmentpolicy;

$serveruri = getserveruri();
$username = $_SERVER['PHP_AUTH_USER'];

print "<p align='left'><font color='#494949'><A HREF = 'http://www.dcc-jpl.com/soft/foltia/' target=\"_blank\">foltia</A>��| <A HREF = './index.php'>����ͽ��</A> | <A HREF = './index.php?mode=new'>������</A> | <A HREF = './listreserve.php'>ͽ�����</A> | <A HREF = './titlelist.php'>���Ȱ���</A> | <A HREF = './viewepg.php'>����ɽ</A> | Ͽ�����(<A HREF = './showplaylist.php'>Ͽ���</A>��<A HREF = './showplaylist.php?list=title'>���Ƚ�</A>��<A HREF = './showplaylist.php?list=raw'>��</A>) | <A HREF = './showlib.php'>Ͽ��饤�֥��</A> |  <A HREF = './folcast.php'>Folcast</A>[<a href=\"itpc://$serveruri/folcast.php\">iTunes����Ͽ</a>] | ";
if ($useenvironmentpolicy == 1){
	print "�� $username ��";
}

print "</font></p>\n";

}


function renderepgstation($con,$stationname,$start){ //����͡��ʤ���EPG�ζ�ɽ��

$now = date("YmdHi");
$today = date("Ymd");   
$tomorrow = date ("Ymd",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
//$today = "20051013";   
//$tomorrow = "20051014";
//$epgstart = $today . "2000";
$epgstart = $start ;
//$epgend = $tomorrow . "0400";
$epgend = calcendtime($start , (8*60));
$query = "
SELECT startdatetime , enddatetime , lengthmin , epgtitle , epgdesc , epgcategory  ,ontvchannel  ,epgid 
FROM foltia_epg 
WHERE foltia_epg.ontvchannel = '$stationname' AND 
enddatetime  > $epgstart  AND 
startdatetime  < $epgend  
ORDER BY foltia_epg.startdatetime  ASC
	";
	$rs = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$maxrows = pg_num_rows($rs);
if ($maxrows == 0) {
		print("���ȥǡ���������ޤ���<BR>");			
}else{
print "<table width=\"100%\"  border=\"0\">\n";
//print "<ul><!-- ($maxrows) $query -->\n";

for ($row = 0; $row < $maxrows; $row++) { 
	 
$rowdata = pg_fetch_row($rs, $row);

$printstarttime = substr($rowdata[0],8,2) . ":" .  substr($rowdata[0],10,2);
$tdclass = "t".substr($rowdata[0],8,2) .  substr($rowdata[0],10,2);
$title = htmlspecialchars($rowdata[3]);
$title = z2h($title);
$desc = htmlspecialchars($rowdata[4]);
$desc = z2h($desc);
$height =  htmlspecialchars($rowdata[2]) * 3;
$epgid =  htmlspecialchars($rowdata[7]);

print"
      <tr>
        <td height = \"$height\" >$printstarttime  <A HREF=\"./reserveepg.php?epgid=$epgid\">$title</A> $desc <!-- $rowdata[0] - $rowdata[1] --></td>
      </tr>
";
/*print"<li style=\"height:" . $height ."px;\" class=\"$tdclass\">
$printstarttime  <A HREF=\"./reserveepg.php?epgid=$epgid\">$title</A> $desc($rowdata[0] - $rowdata[1])
</li>\n";
*/
}//for
//print "</ul>\n";
print "</table>\n";

}//if
}//end function

function calcendtime($start,$lengthmin){//����͡���λ����(Ex:200510170130) 
$startyear =   substr($start,0,4);
$startmonth =   substr($start,4,2);
$startday =   substr($start,6,2);
$starthour =   substr($start,8,2);
$startmin =   substr($start,10,2);
//int mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
$endtime = date ("YmdHi",mktime($starthour  , $startmin + $lengthmin , 0, $startmonth  , $startday, $startyear));

return ($endtime );
}//end function


function z2h($string){ //����͡�Ⱦ�Ѳ�����ʸ��
	$stringh = mb_convert_kana($string, "a", "EUC-JP");
 return ($stringh );
}

function foldate2rfc822($start){//����͡�RFC822��������λ���ɽ��
	$startyear =   substr($start,0,4);
	$startmonth =   substr($start,4,2);
	$startday =   substr($start,6,2);
	$starthour =   substr($start,8,2);
	$startmin =   substr($start,10,2);

	$rfc822 = date ("r",mktime($starthour  , $startmin , 0, $startmonth  , $startday, $startyear));
	
	return ($rfc822);
}//end sub

function foldate2print($start){//����͡����ܸ�������ɽ��
	$startyear =   substr($start,0,4);
	$startmonth =   substr($start,4,2);
	$startday =   substr($start,6,2);
	$starthour =   substr($start,8,2);
	$startmin =   substr($start,10,2);

	$printabledate = date ("Y/m/d H:i",mktime($starthour  , $startmin , 0, $startmonth  , $startday, $startyear));	
	return ($printabledate);
}//end sub

function getserveruri(){//����͡������Х��ɥ쥹 Ex.www.dcc-jpl.com:8800/soft/foltia/

//���URI�Ȥ�Ω��
$sv6 = $_SERVER['SCRIPT_NAME'];///dameNews/sarasorjyu/archives.php
$sv8 = $_SERVER['SERVER_NAME'];//sync.dcc-jpl.com
$sv9 = $_SERVER['SERVER_PORT'];
if ($sv9 == 80){
	$port = "";
}else{
	$port = ":$sv9";
}
$a = split("/", $sv6);
array_pop($a);

$scriptpath = implode("/", $a);

$serveruri = "$sv8$port$scriptpath";
return ($serveruri );
}//end sub


function getserverfqdn(){//����͡������Х��ɥ쥹 Ex.www.dcc-jpl.com:8800

//���URI�Ȥ�Ω��
$sv6 = $_SERVER['SCRIPT_NAME'];///dameNews/sarasorjyu/archives.php
$sv8 = $_SERVER['SERVER_NAME'];//sync.dcc-jpl.com
$sv9 = $_SERVER['SERVER_PORT'];
if ($sv9 == 80){
	$port = "";
}else{
	$port = ":$sv9";
}
$a = split("/", $sv6);
array_pop($a);

$scriptpath = implode("/", $a);

$serveruri = "$sv8$port";
return ($serveruri );
}//end sub


function printdiskusage(){//����͡��ʤ�
list (, $all, $use , $free, $usepercent) =  getdiskusage();

print "
<div style=\"width:100%;border:1px solid black;text-align:left;\"><span style=\"float:right;\">$free</span>
<div style=\"width:$usepercent;border:1px solid black;background:white;\">$use/$all($usepercent)</div>
</div>
";
//exec('ps ax | grep ffmpeg |grep MP4 ' ,$ffmpegprocesses);
}//end sub


function getdiskusage(){//����͡�����[,��������, �������� , ��������, ���ѳ��]

global $recfolderpath,$recfolderpath;

	exec ( "df -h  $recfolderpath | grep $recfolderpath", $hdfreearea);
	$freearea = preg_split ("/[\s,]+/", $hdfreearea[0]);

    return $freearea;
	
}//endsub


function printtrcnprocesses(){

$ffmpegprocesses = `ps ax | grep ffmpeg | grep -v grep |  wc -l `;
$uptime = exec('uptime');

print "<div style=\"text-align:left;\">";
print "$uptime<br>\n";
print "�ȥ饳���Ư��:$ffmpegprocesses<br>\n";
print "</div>";

}//endsub


function warndiskfreearea(){

global $demomode;

if ($demomode){
print "<!-- demo mode -->";
}else{

global $recfolderpath,$hdfreearea ;

	exec ( "df   $recfolderpath | grep $recfolderpath", $hdfreearea);
	$freearea = preg_split ("/[\s,]+/", $hdfreearea[0]);
$freebytes = $freearea[3];
if ($freebytes == "" ){
//
//print "<!-- err:\$freebytes is null -->";
}elseif($freebytes > 1024*1024*100 ){// 100GB�ʾ夢���Ƥ��
//�ʤˤ⤷�ʤ�
print "<style type=\"text/css\"><!-- --></style>";
}elseif($freebytes > 1024*1024*50 ){// 100GB�ʲ�
print "<style type=\"text/css\"><!--
	body {
	background-color: #CCCC99;
 	}
-->
</style>
";
}elseif($freebytes > 1024*1024*30 ){// 50GB�ʲ�
print "<style type=\"text/css\"><!--
	body {
	background-color:#CC6666;
 	}
-->
</style>
";
}elseif($freebytes > 0 ){// 30GB�ʲ�
print "<style type=\"text/css\"><!--
	body {
	background-color:#FF0000;
 	}
-->
</style>
";
}else{ //�������� 0�Х���
print "<style type=\"text/css\"><!--
	body {
	background-color:#000000;
 	}
-->
</style>
";
}//endif freebytess

}//endif demomode

}//endsub



function foldatevalidation($foldate){

if (strlen($foldate) == 12 ){

	$startyear =   substr($foldate,0,4);
	$startmonth =   substr($foldate,4,2);
	$startday =   substr($foldate,6,2);
	$starthour =   substr($foldate,8,2);
	$startmin =   substr($foldate,10,2);

	$startepoch = date ("U",mktime($starthour  , $startmin , 0, $startmonth  , $startday, $startyear));	
	$nowe = time();
	if ($startepoch > $nowe){
	//print "$foldate:$startepoch:$nowe";
		return TRUE;
	}else{
		return FALSE;
	}	//end if $startepoch > $nowe
}else{
	return FALSE;
}//end if ($foldate) == 12 

}//end function



function login($con,$name,$passwd){
global $environmentpolicytoken;

//�������Ƴ�ǧ
 if (((mb_ereg('[^0-9a-zA-Z]', $name)) ||(mb_ereg('[^0-9a-zA-Z]', $passwd) ))){
	
	//print "���顼����\n";
	//print "<!-- DEBUG name/passwd format error-->";
	redirectlogin();
	
}else{
//print "�������\n";
//db����
escape_string($name);
escape_string($passwd);

$query = "
SELECT memberid ,userclass,name,passwd1 
FROM foltia_envpolicy 
WHERE foltia_envpolicy.name  = '$name'  
	";
	$useraccount = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$useraccountrows = pg_num_rows($useraccount);
	
	if ($useraccountrows == 1 ){
		$rowdata = pg_fetch_row($useraccount, 0);
		$memberid = $rowdata[0];
		$userclass = $rowdata[1];
		$username =  $rowdata[2];
		$dbpasswd = $rowdata[3];
	}else{
		header("HTTP/1.0 401 Unauthorized");
		//print "<!-- DEBUG DB record error ($useraccountrows)-->";
		redirectlogin();
	}//end if


// passwd��db���������
if ($userclass == 0){
$dbpasswd = "$dbpasswd";
}else{
// db passwd�ȥȡ������Ϣ�뤷
$dbpasswd = "$dbpasswd"."$environmentpolicytoken";
}
//���줬���ϤȰ��פ����ǧ��
if ($passwd == $dbpasswd) {
//print "ǧ������<br>$dbpasswd  $passwd\n";
}else{
//print "ǧ�ڼ���<br>$dbpasswd  $passwd\n";
		header("HTTP/1.0 401 Unauthorized");
		//print "<!-- DEBUG passwd unmatch error>";
		redirectlogin();
}
}//end if mb_ereg
}//end function login




function redirectlogin(){

print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n";
print "<html><head>\n";
print "<title>foltia:Invalid login</title>\n";
print "</head><body>\n";
print "<h1>Invalid login</h1>";
print "<p>foltia�ؤΥ��������ˤϥ�����ɬ�פǤ������������������Ͽ��<a href=\"./accountregist.php\">�����餫�顣</a></p><hr>\n";
print "<address>foltia by DCC-JPL Japan/foltia Project.  <a href = \"http://www.dcc-jpl.com/soft/foltia/\">http://www.dcc-jpl.com/soft/foltia/</a></address>\n";
print "</body></html>\n";



exit;
}//end function redirectlogin

function getuserclass($con){
global $useenvironmentpolicy;
$username = $_SERVER['PHP_AUTH_USER'];

if ($useenvironmentpolicy == 1){
$query = "
SELECT memberid ,userclass,name,passwd1 
FROM foltia_envpolicy 
WHERE foltia_envpolicy.name  = '$username'  
	";
		$useraccount = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$useraccountrows = pg_num_rows($useraccount);
	
	if ($useraccountrows == 1 ){
		$rowdata = pg_fetch_row($useraccount, 0);
		//$userclass = $rowdata[1];
		return ($rowdata[1]);
	}else{
	return (99);//���顼
	}//end if
	
}else{
	return (0);//�Ķ��ݥꥷ���Ȥ�ʤ��Ȥ��ϤĤͤ��ø��⡼��
}//end if
}//end function getuserclass



function getmymemberid($con){
global $useenvironmentpolicy;
$username = $_SERVER['PHP_AUTH_USER'];

if ($useenvironmentpolicy == 1){
$query = "
SELECT memberid ,userclass,name,passwd1 
FROM foltia_envpolicy 
WHERE foltia_envpolicy.name  = '$username'  
	";
		$useraccount = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$useraccountrows = pg_num_rows($useraccount);
	
	if ($useraccountrows == 1 ){
		$rowdata = pg_fetch_row($useraccount, 0);
		//$userclass = $rowdata[1];
		return ($rowdata[0]);
	}else{
	return (-1);//���顼
	}//end if
	
}else{
	return (0);//�Ķ��ݥꥷ���Ȥ�ʤ��Ȥ��ϤĤͤ��ø��⡼��
}//end if
}//end function getuserclass


function getmemberid2name($con,$memberid){
global $useenvironmentpolicy;
//$username = $_SERVER['PHP_AUTH_USER'];

if ($useenvironmentpolicy == 1){
$query = "
SELECT memberid ,userclass,name,passwd1 
FROM foltia_envpolicy 
WHERE foltia_envpolicy.memberid  = '$memberid'  
	";
		$useraccount = m_query($con, $query, "DB������˼��Ԥ��ޤ���");
	$useraccountrows = pg_num_rows($useraccount);
	
	if ($useraccountrows == 1 ){
		$rowdata = pg_fetch_row($useraccount, 0);
		return ($rowdata[2]);
	}else{
	return ("");//���顼
	}//end if
	
}else{
	return ("");
}//end if



}//end function getmemberid2name

?>
