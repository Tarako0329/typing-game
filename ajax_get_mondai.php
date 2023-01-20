<?php
/*
*params:POST
*   lv     ：問題のレベル
*/
require_once "php_header.php";

if(!empty($_POST)){
	// DBとの接続
	$sql = "select * from mondai_list where lv = ?";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue(1, $_POST['lv'], PDO::PARAM_INT);
	$stmt->execute();
	$MondaiList = $stmt->fetchAll();
}else{
	echo "不正アクセス";
	exit;
}

log_writer2("ajax_get_mondai.php",json_encode($MondaiList, JSON_UNESCAPED_UNICODE),"lv3");
log_writer2("ajax_get_mondai.php","Get user!! yattane !","lv1");

// ヘッダーを指定することによりjsonの動作を安定させる
header('Content-type: application/json');
// htmlへ渡す配列$productListをjsonに変換する
echo json_encode($MondaiList, JSON_UNESCAPED_UNICODE);
?>


