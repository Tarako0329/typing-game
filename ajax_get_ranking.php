<?php
/*
*params:POST
*   lv     ：問題のレベル
*/
require_once "php_header.php";
$level = $_GET["level"];

	// DBとの接続
	$sql = "SELECT 
		DENSE_RANK() OVER(ORDER BY score desc) as 順位
		,ranking.* 
		from ranking
		where level = :level";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("level",$level,PDO::PARAM_STR);
	$stmt->execute();
	$sougou = $stmt->fetchAll();
	
	$sql = "SELECT 
		DENSE_RANK() OVER(PARTITION BY area ORDER BY score desc) as 順位
		,ranking.* 
		from ranking
		where level = :level";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("level",$level,PDO::PARAM_STR);
	$stmt->execute();
	$area = $stmt->fetchAll();
	
	$sql = "SELECT 
		DENSE_RANK() OVER(PARTITION BY nendai ORDER BY score desc) as 順位
		,ranking.* 
		from ranking
		where level = :level";
	$stmt = $pdo_h->prepare($sql);
	$stmt->bindValue("level",$level,PDO::PARAM_STR);
	$stmt->execute();
	$nendai = $stmt->fetchAll();

	$result = array(
		"sougou" => $sougou
		,"area" => $area
		,"nendai" => $sougou
	);

//log_writer2("ajax_get_mondai.php",json_encode($MondaiList, JSON_UNESCAPED_UNICODE),"lv3");
//log_writer2("ajax_get_mondai.php","Get user!! yattane !","lv1");

// ヘッダーを指定することによりjsonの動作を安定させる
header('Content-type: application/json');
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit();
?>