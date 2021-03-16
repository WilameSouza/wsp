<?php
header('Accept-Charset: utf-8');
if(stripos($_SERVER['PHP_SELF'],'.wsp')!==false){
$_WSP['version']='0.0.1 beta';
$_SERVER['SERVER_SOFTWARE'].=" WSP Framework";
header("X-Powered-By: WSP Framework for PHP");
$_WSP['OPENFILE']=@fopen($_SERVER['SCRIPT_FILENAME'],'r');
$_WSP['CODE']=@stream_get_contents($_WSP['OPENFILE']);
$_WSP['PRODUCTION_MODE']=false;
$_WSP['HEAD']=<<<HEAD
<meta charset="UTF-8"/>
HEAD;
$_WSP['BODY']=null;
$_WSP['BODY_ATTR']=null;
$__err=0;
$_WSP['HTML_ADD']=null;

function wspcode($code){

	$wspTagsAvailables = [
		'<wsp>' => "<?php",
		'</wsp>' => "?>",
		'<wspe>' => "<?=",
		'</wspe>' => "?>",
		'<tobody>' => "tobody(<<<HEREDOC\r\n",
		'</tobody>' => "\r\nHEREDOC);",
		'<wspstring>' => "<<<HEREDOC"."\r\n",
		'<wspstring/>' => "<<<HEREDOC"."\r\n",
		'</wspstring>' => "\r\nHEREDOC\r\n",
		'<wspgroup>' => null,
		'</wspgroup>' => null,
		'<string>' => "<<<'HEREDOC'"."\r\n",
		'<string/>' => "<<<'HEREDOC'"."\r\n",
		'</string>' => "\r\nHEREDOC\r\n",
		'show::','echo',
		'@viewport' => '\'<meta name="viewport" content="width=device-width,initial-scale=1"/>\'',
		'@viewport-no-scalable' => '<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0"/>',
		'se->' => 'if',
		'senao->' => 'else',
		'var-> ' => '$',
		'get::' => '$_GET',
		'post::' => '$_POST',
		'files::' => '$_FILES',
		'<wspkey>' => '{',
		'</wspkey>' => '}',
		'??def' => 'isset',

	];

	foreach($wspTagsAvailables as $wspTag => $phpTag)
	{
		$code=str_ireplace($wspTag,$phpTag,$code);
	}

	return $code;
}

// Busca pelo inicio e final do interpretador WSP
while(1){
$strt=false;
$fnl=false;
$inicioWSP = stripos($_WSP['CODE'],"<wsp>");
$finalWSP = stripos($_WSP['CODE'],"</wsp>");
if($inicioWSP!==false) $strt=true;
if($finalWSP!==false) $fnl=true;

if($finalWSP===false and $inicioWSP!==false){
	$__err+=1;
	$_WSP['ERRORS'][$__err]="Você não finalizou o interpretador com &lt;/wsp&gt; em seu código.";
}
if($inicioWSP===false and $finalWSP!==false){
	$__err+=1;
	$_WSP['ERRORS'][$__err]="Você não iniciou o interpretador mas o finalizou.<br/>Utilize &lt;wsp&gt; em seu código para iniciar o interpretador.";
}
if($strt===true and $fnl===true){
	$areaCod=($finalWSP + 6) - $inicioWSP;
	$codigo=substr($_WSP['CODE'],$inicioWSP,$areaCod);
	$coded=wspcode($codigo);
	$_WSP['CODE']=str_replace($codigo,$coded,$_WSP['CODE']);
}
else break;
}


$_WSP['FUNCTIONS']='function sql($db="wsp",$h="localhost",$u="root",$p="root",$pr=3301,$s="/data/user/0/com.sylkat.apache/files/usr/var/lib/mysql/mysql.sock"){
	return mysqli_connect($h,$u,$p,$db,$pr,$s);
}
function productionmode(bool $a=true){
global $_WSP;
	$_WSP[\'PRODUCTION_MODE\']=(bool) $a;
}
function sqlres($query=null){
	$__con=sql();
	$__res=mysqli_query($__con,$query);
	$out["total"]=0;
	$out["result"]=null;
	$cc=0;
	if($__res){
		while($res=mysqli_fetch_array($__res)){
			$cc+=1;
			$tcc=$cc - 1;
			foreach ($res as $k => $vv) {
				$res[$k]=utf8_encode($vv);
			}
			$out["result"][$tcc]=$res;
		}
		$out["total"]=$cc;
		return $out;
	}
	else return false;
}
function nohtml($a=true){
	global $_WSP;
	if($a==true) $_WSP["nohtml"]=true;
	if($a==false) $_WSP["nohtml"]=false;
}
function contentType($a){
	@header(\'Content-type: \'.$a);
}
nohtml(false);
function tobody($a=null){
	global $_WSP;
	$_WSP["BODY"].=utf8_encode($a);
}
function tobodyattr($a=null){
	global $_WSP;
	$_WSP[\'BODY_ATTR\'].=utf8_encode(" ".$a);
}
function tohead($a=null){
	global $_WSP;
	$_WSP["HEAD"].=utf8_encode("\r\n".$a);
}
';
if($__err>0){
	foreach ($_WSP['ERRORS'] as $err){
		$rd=rand(10001,99999);
		echo <<<HEREDOC
<script>
setTimeout(function(){
	document.getElementById('error{$rd}').style.transition='1s';
	document.getElementById('error{$rd}').style.opacity='0';
},5000);
</script>
<div style="padding:20px;display:block;position:fixed;top:10px;left:5px;right:5px;background-color:#f2bdbd;color:black;" id="error{$rd}">
<center><h3>Erro encontrado</h3></center>
<b>$err</b>
</div>
HEREDOC;
	}
}
$cc=substr_count( $_WSP['FUNCTIONS'], "\n" );
try {

@eval($_WSP['FUNCTIONS'].'try {
?>'.$_WSP['CODE'].'
<?php
} catch (Error $e) {
			tohead("<meta charset=\"utf-8\" name=\"viewport\" content=\"width=device-width,initial-scale=1,user-scalable=0\" />");
			tohead("<style>
			body{
				margin:0;
				background-color: #f0c080;
				color:black;
				min-width: 200px;
				min-height: 400px;
				font-family: Verdana;
				text-align: center;
			}
		</style>");
      tobody("<h2>Há um erro no script WSP: ".($e->getMessage())." na linha " .($e->getLine() - '.($cc + 1).')."</h2>");
}
?>'."<?php\r\n".'if($_WSP[\'nohtml\']===false) $_WSP["HTML_OUT"]=utf8_decode(<<<HEREDOC'."\r\n".'<html>
<head>
$_WSP[HEAD]
</head>
<body{$_WSP[BODY_ATTR]}>
$_WSP[BODY]
</body>
</html>'."\r\nHEREDOC\r\n);\r\n".'if($_WSP[\'PRODUCTION_MODE\']===true){'."\r\n".'$_WSP["HTML_OUT"]=str_ireplace("\n","",$_WSP["HTML_OUT"]);'.'$_WSP["HTML_OUT"]=str_ireplace("\r","",$_WSP["HTML_OUT"]);'."\r\n".'$_WSP["HTML_OUT"]=str_ireplace("	","",$_WSP["HTML_OUT"].$_WSP["HTML_ADD"]);'."\r\n"."}"."\r\n".'echo $_WSP[\'HTML_OUT\'];'."\r\n?>");
} catch (Error $e) {
?>
<html>
	<head>
		<meta charset="utf-8" name="viewport" content="width=device-width,initial-scale=1,user-scalable=0" />
		<style>
			body{
				margin:0;
				background-color: #e5aaaa;
				color:black;
				min-width: 200px;
				min-height: 400px;
				font-family: Verdana;
				text-align: center;
			}
		</style>
	</head>
	<body>
		<div>
			<h1>❌</h1>
			<h2>Erro fatal na sintaxe WSP</h2>
			<b>Por favor, verifique o código digitado e tente novamente.</b>
		</div>
	</body>
</html>
<?php
}
exit();
}
?>