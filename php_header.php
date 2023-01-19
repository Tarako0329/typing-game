<?php
date_default_timezone_set('Asia/Tokyo');
require "./vendor/autoload.php";
//.envの取得
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define("MAIN_DOMAIN",$_ENV["MAIN_DOMAIN"]);

$rtn=session_set_cookie_params(24*60*60*24*3,'/','.'.MAIN_DOMAIN,true);
if($rtn==false){
    echo "ERROR:session_set_cookie_params";
    exit();
}
session_start();

require "functions.php";
define("EXEC_MODE",$_ENV["EXEC_MODE"]);


if(EXEC_MODE=="Local"){
    //テスト環境はミリ秒単位
    //$time="8";
    $time=date('Ymd-His');
    error_reporting( E_ALL );
}else{
    //本番はリリースした日を指定
    $time="20221111-01";
    //$time=date('Ymd');
    error_reporting( E_ALL & ~E_NOTICE );
}

//DB接続関連
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["DBUSER"]);
define("PASSWORD", $_ENV["PASS"]);

// DBとの接続
$pdo_h = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());

//端末IDを発行し、10年の有効期限でCookieにセット
if(!isset($_COOKIE['machin_id'])){
    $machin_id = getGUID();
    setCookie("machin_id", $machin_id, time()+60*60*24*365*10, "/", "", TRUE, TRUE); 
}else{
    $machin_id = $_COOKIE['machin_id'];
}
define("MACHIN_ID", $machin_id);

//log_writer("php_header.php _SERVER values ",$_SERVER);
//log_writer("php_header.php end _SESSION values ",$_SESSION);

?>




