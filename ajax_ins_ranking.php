<?php
/*
*params:POST
*   lv     ：問題のレベル
*/
require_once "php_header.php";

if(!empty($_POST) && $_POST['score']>0){
	// DBとの接続
	$sql = "insert into ranking(level,nikname,score,area,nendai,guid) values(:level,:nikname,:score,:area,:nendai,:guid)";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("level", $_POST['level'], PDO::PARAM_STR);
	$stmt->bindValue("nikname", $_POST['nikname'], PDO::PARAM_STR);
	$stmt->bindValue("score", $_POST['score'], PDO::PARAM_INT);
	$stmt->bindValue("area", $_POST['area'], PDO::PARAM_STR);
	$stmt->bindValue("nendai", $_POST['nendai'], PDO::PARAM_STR);
	$stmt->bindValue("guid", $_POST['guid'], PDO::PARAM_STR);
	$stmt->execute();
	$msg = '登録完了';
}else{
	$msg = '登録なし';
	//echo "不正アクセス";
	//exit;
}

// ヘッダーを指定することによりjsonの動作を安定させる
header('Content-type: application/json');
// htmlへ渡す配列$productListをjsonに変換する
echo json_encode($msg, JSON_UNESCAPED_UNICODE);
exit();
?>