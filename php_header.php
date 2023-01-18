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

if(!empty($_SERVER['SCRIPT_URI'])){
    define("ROOT_URL",substr($_SERVER['SCRIPT_URI'],0,mb_strrpos($_SERVER['SCRIPT_URI'],"/")+1));
}else{
    define("ROOT_URL","http://".MAIN_DOMAIN."/");
}
define("EXEC_MODE",$_ENV["EXEC_MODE"]);


if(EXEC_MODE=="Test" || EXEC_MODE=="Local"){
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

$pass=dirname(__FILE__);

//ツアーガイド実行中か否かを判断する
$_SESSION["tour"]=(empty($_SESSION["tour"])?"":$_SESSION["tour"]);

//DB接続関連
define("DNS","mysql:host=".$_ENV["SV"].";dbname=".$_ENV["DBNAME"].";charset=utf8");
define("USER_NAME", $_ENV["DBUSER"]);
define("PASSWORD", $_ENV["PASS"]);

//メール送信関連
define("HOST", $_ENV["HOST"]);
define("PORT", $_ENV["PORT"]);
define("FROM", $_ENV["FROM"]);
define("PROTOCOL", $_ENV["PROTOCOL"]);
define("POP_HOST", $_ENV["POP_HOST"]);
define("POP_USER", $_ENV["POP_USER"]);
define("POP_PASS", $_ENV["POP_PASS"]);

//システム通知
define("SYSTEM_NOTICE_MAIL",$_ENV["SYSTEM_NOTICE_MAIL"]);

//契約・支払関連のキー情報
define("SKEY", $_ENV["SKey"]);
define("PKEY", $_ENV["PKey"]);
define("PLAN_M", $_ENV["PLAN_M"]);
define("PLAN_Y", $_ENV["PLAN_Y"]);
define("PAY_CONTRACT_URL", $_ENV["PAY_contract_url"]);
define("PAY_CANCEL_URL", $_ENV["PAY_cancel_url"]);

//WEATHER_ID
define("WEATHER_ID", $_ENV["WEATHER_ID"]);

//サイトタイトルの取得
$title = $_ENV["TITLE"];

//暗号化キー
$key = $_ENV["KEY"];

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

//スキンの取得
/* headerでuidの再取得はできない、かつ、マシンIDはGUIDなのでほぼ一意となるのでuidは使用しないように変更する
$sql = "select value from PageDefVal where uid=? and machin=? and page=? and item=?";
$stmt = $pdo_h->prepare($sql);
$stmt->bindValue(1, (!empty($_SESSION['user_id'])?$_SESSION['user_id']:NULL), PDO::PARAM_INT);
$stmt->bindValue(2, MACHIN_ID, PDO::PARAM_STR);
$stmt->bindValue(3, "menu.php", PDO::PARAM_STR);
$stmt->bindValue(4, "COLOR", PDO::PARAM_STR);//name属性を指定
$stmt->execute();
*/
$sql = "select value from PageDefVal where machin=? and page=? and item=?";
$stmt = $pdo_h->prepare($sql);
$stmt->bindValue(1, MACHIN_ID, PDO::PARAM_STR);
$stmt->bindValue(2, "menu.php", PDO::PARAM_STR);
$stmt->bindValue(3, "COLOR", PDO::PARAM_STR);//name属性を指定
$stmt->execute();
$log_time=date("Y/m/d H:i:s");

if($stmt->rowCount()==0){
    $color_No = 0;
    log_writer2("php_header.php","php_header.php@Cannot Get skin_color_cd：MACHIN_ID=[".MACHIN_ID."]\n","lv3");
}else{
    $buf = $stmt->fetch();
    $color_No = $buf["value"];
} 
//log_writer("php_header.php _SERVER values ",$_SERVER);
//log_writer("php_header.php end _SESSION values ",$_SESSION);

?>




