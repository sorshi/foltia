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
        $value = escape_string($value);
        $value = htmlspecialchars($value);
	return ($value);
    }
  }
	//GET�ѿ����ե�����ǥ�����
	  function getgetnumform($key) {
//    if ($_GET["{$key}"] != "") {
    if (isset($_GET["{$key}"] )) {
		$value = $_GET["{$key}"];
		$value = ereg_replace("[^-0-9]", "", $value);
		$value = escape_numeric($value);
	return ($value);
    }
  }
	
	//�ե�����ǥ�����
	  function getform($key) {
    if ($_POST["{$key}"] != "") {
		$value = $_POST["{$key}"];
        $value = escape_string($value);
        $value = htmlspecialchars($value);
	return ($value);
    }
  }
	//�������ѥե�����ǥ�����
	  function getnumform($key) {
    if ($_POST["{$key}"] != "") {
		$value = $_POST["{$key}"];
		$value = escape_string($value);
        $value = htmlspecialchars($value);
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
		<p class="error"><?php print "$message"; ?></p>
		<div class="index"><a href="./">�ȥå�</a></div>
	</body>
</html><?php
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

	/* SQL ʸ����Υ��������� */
	function escape_string($sql, $quote = FALSE) {
		if ($quote && strlen($sql) == 0) {
			return "null";
		}
		if (preg_match("/^pgsql/", DSN)){
		return ($quote ? "'" : "") .
		       pg_escape_string($sql) .
		       ($quote ? "'" : "");
		}else if (preg_match("/^sqlite/", DSN)){
		/*	return ($quote ? "'" : "") .
				sqlite_escape_string($sql) .
				($quote ? "'" : "");
		*/
		return($sql);
		}else{
			return "null";
		}
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
	
	/* DB����³ */
	function m_connect() { 
	try {
		$dbh = new PDO(DSN);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return($dbh);
	} catch (PDOException $e) {
		die_exit($e->getMessage() . ": �ǡ����١�������³����ޤ���Ǥ�����");
		}
		/* �ǡ����١����ȡ�PHP ������ʸ�������ɤ��㤦��� */
	}

	/* �ǡ����١����Ȥ���³���ڤ�Υ�� */
function m_close($dbh) {
	return null;
	}

//��ؿ���sql_query���֤�����
function m_query($dbh, $query, $errmessage) {
	try {
		$rtn = $dbh->query($query);
		return($rtn);
	} catch (PDOException $e) {
			/* ���顼��å������� SQL ʸ��Ф��Τϥ������ƥ����ɤ��ʤ����� */
			$msg = $errmessage . "<br>\n" .
		    $e->getMessage() . "<br>\n" .
		    var_export($e->errorInfo, true) . "<br>\n" .
			       "<small><code>" . htmlspecialchars($query) .
			       "</code></small>\n";
//		$dbh->rollBack();
		$dbh = null;
			die_exit($msg);
		}
	}
/* SQL ʸ��¹� */
function sql_query($dbh, $query, $errmessage,$paramarray=null) {
	try {
		$rtn = $dbh->prepare("$query");
		$rtn->execute($paramarray);
		return($rtn);
	} catch (PDOException $e) {
			/* ���顼��å������� SQL ʸ��Ф��Τϥ������ƥ����ɤ��ʤ����� */
			$msg = $errmessage . "<br>\n" .
		    $e->getMessage() . "<br>\n" .
		    var_export($e->errorInfo, true) . "<br>\n" .
			       "<small><code>" . htmlspecialchars($query) .
			       "</code></small>\n";
//		$dbh->rollBack();
		$dbh = null;
			die_exit($msg);
		}
	}

	/* select ������̤�ơ��֥��ɽ�� */
	function m_showtable($rs) {
		/* ������� */
	$maxrows = 0;
		
	$rowdata = $rs->fetch();
	if (! $rowdata) {
			echo("<p class=\"msg\">�ǡ�����¸�ߤ��ޤ���</p>\n");
			return 0;
		}
		
		/* �ե�����ɿ� */
	$maxcols = $rs->columnCount();
		?>
<table class="list" summary="�ǡ���������̤�ɽ��" border="1">
	<thead>
		<tr>
			<?php
				/* �ơ��֥�Υإå�������� */
				for ($col = 1; $col < $maxcols; $col++) {
					/* pg_field_name() �ϥե������̾���֤� */
		     $meta = $rs->getColumnMeta($col);
		     $f_name = htmlspecialchars($meta["name"]);
					echo("<th abbr=\"$f_name\">$f_name</th>\n");
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			/* �ơ��֥�Υǡ�������� */
	      do {
		      $maxrows++;

				echo("<tr>\n");
				/* �����ܤ˥�󥯤�ĥ�� */
				echo("<td><a href=\"edit.php?q_code=" .
				     urlencode($rowdata[0]) . "\">" .
				     htmlspecialchars($rowdata[1]) . "</a></td>\n");
				for ($col = 2; $col < $maxcols; $col++) { /* ����б� */
					echo("<td>".htmlspecialchars($rowdata[$col])."<br></td>\n");
				}
				echo("</tr>\n");
	      } while ($rowdata = $rs->fetch());
		?>
	</tbody>
</table>
		<?php
		return $maxrows;
	}


function m_viewdata($dbh, $code) {

/*����ȤäƤʤ����?*/
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
	$rowdata = $rs->fetch();
	if (! $rowdata) {
		print("���ȥǡ���������ޤ���<BR>");			
}else{
print "<table width=\"100%\"  border=\"0\">\n";
//print "<ul><!-- ($maxrows) $query -->\n";

		do {
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
		} while ($rowdata = $rs->fetch());//do
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

//	exec ( "df -h  $recfolderpath | grep $recfolderpath", $hdfreearea);
//	$freearea = preg_split ("/[\s,]+/", $hdfreearea[0]);
	exec ( "df -hP  $recfolderpath", $hdfreearea);
	$freearea = preg_split ("/[\s,]+/", $hdfreearea[count($hdfreearea)-1]);

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
		$rowdata = $useraccount->fetch();
		if (! $rowdata) {
			header("HTTP/1.0 401 Unauthorized");
			redirectlogin();
		}
	
		$memberid = $rowdata[0];
		$userclass = $rowdata[1];
		$username =  $rowdata[2];
		$dbpasswd = $rowdata[3];

		$rowdata = $useraccount->fetch();
		if ($rowdata) {
		header("HTTP/1.0 401 Unauthorized");
		redirectlogin();
		}

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
global $environmentpolicytoken;

print "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n";
print "<html><head>\n";
print "<title>foltia:Invalid login</title>\n";
print "</head><body>\n";
print "<h1>Invalid login</h1>";
print "<p>foltia�ؤΥ��������ˤϥ�����ɬ�פǤ����ƥ�����ϥ���ɤ�֥饦���Ƶ�ư�ǡ����������������Ͽ��<a href=\"./accountregist.php\">�����餫�顣</a></p>";
if ($environmentpolicytoken == ""){
}else{
	print "<p>�������β��̤�ɽ�����줿���ˤϥ������ƥ������ɤ��ѹ����줿�����Τ�ޤ���</p>";
}
print "</p><hr>\n";
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
		$rowdata = $useraccount->fetch();
		if (! $rowdata) {
			return (99);
		}
	
		$userclass = $rowdata[1];

		$rowdata = $useraccount->fetch();
		if ($rowdata) {
			return (99);
		}

		return ($userclass);
	
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
		$rowdata = $useraccount->fetch();
		if (! $rowdata) {
	return (-1);//���顼
		}

		$memberid = $rowdata[0];

		$rowdata = $useraccount->fetch();
		if ($rowdata) {
			return (-1);
		}

		return ($memberid);
	
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
		$rowdata = $useraccount->fetch();
		if (! $rowdata) {
	return ("");//���顼
		}
	
		$name = $rowdata[2];

		$rowdata = $useraccount->fetch();
		if ($rowdata) {
	return ("");
		}

		return ($name);

	}else{
		return ("");
	}//end if

}//end function getmemberid2name



function number_page($p,$lim){
//Autopager���ڡ�����󥯤ǻ��Ѥ��Ƥ���ؿ�
//�����ϴؿ��򤷤Ƥ���ե�����̾
//index.php  showplaylist.php  titlelist.php  showlib.php  showlibc.php
///////////////////////////////////////////////////////////////////////////
// �ڡ������η׻��ط�
// �裱���� : $p       : ���ߤΥڡ�����
// �裲���� : $lim     : ���ڡ����������ɽ������쥳���ɿ�
///////////////////////////////////////////////////////////////////////////

	if($p == 0){
		$p2 = 2;        //$p2�ν��������
	}else{
		$p2 = $p;       //���Υڡ��������ͤ�$p2����������
		$p2++;
	}

	if($p < 1){
		$p = 1;
	}
	//ɽ������ڡ������ͤ����
	$st = ($p -1) * $lim;

	//
	return array($st,$p,$p2);
}//end number_page


function page_display($query_st,$p,$p2,$lim,$dtcnt,$mode){
//Autopager���ڡ�����󥯤ǻ��Ѥ��Ƥ���ؿ�
//�����ϴؿ�����Ѥ��Ƥ���ե�����̾
//index.php��showplaylist.php��titlelist.php��showlib.php��showlibc.php
/////////////////////////////////////////////////////////////////////////////
// Autopager�����ȥڡ����Υ�󥯤�ɽ��
// �裱���� �� $query_st        : ���������
// �裲���� �� $p            : ���ߤΥڡ���������
// �裳���� �� $p2           : ���Υڡ���������
// �裴���� �� $lim          : 1�ڡ����������ɽ������쥳���ɿ�
// �裵���� �� $dtcnt        : �쥳���ɤ����
// �裶���� �� $mode         :�ڿ����ȡ�mode=new�ΤȤ��˥�󥯥ڡ�����ɽ�������ʤ��ե饰(index.php�Τߤǻ���)
////////////////////////////////////////////////////////////////////////////
	if($query_st == ""){
        //�ڡ����������
        $page = ceil($dtcnt / $lim);
		//$mode��ifʸ�ϡڿ����ȡۤβ��̤Τߤǻ���
		if($mode == ''){
			echo "$p/$page";         //  ���ߤΥڡ�����/�ڡ������
		}
        //�ڡ����Υ��ɽ��
        for($i=1;$i <= $page; $i++){
            print("<a href=\"".$_SERVER["PHP_SELF"]."?p=$i\" > $i </a>");
        }
        //Autopageing�ν���
        if($page >= $p2 ){
            print("<a rel=next href=\"".$_SERVER["PHP_SELF"]."?p=$p2\" > </a>");
        }
	}else{      //query_st���ͤ����äƤ����
		$query_st = $_SERVER['QUERY_STRING'];
        $page = ceil($dtcnt / $lim);
        echo "$p/$page";
        //�ڡ����Υ��ɽ��
        for($i=1;$i <= $page; $i++){
			$query_st =  preg_replace('/p=[0-9]+&/','',$query_st);    //p=0��9&�����ˤ�������ɽ��
            print("<a href=\"".$_SERVER["PHP_SELF"]."?p=$i&$query_st\" > $i </a>");
        }
        //Autopageing�ν���
        if($page >= $p2 ){
			$query_st =  preg_replace('/p=[0-9]+&/','',$query_st);
            print("<a rel=next href=\"".$_SERVER["PHP_SELF"]."?p=$p2&$query_st\" > </a>");
		}
	}
    return array($p2,$page);
}// end page_display

?>
