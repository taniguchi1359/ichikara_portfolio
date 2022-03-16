<?php
# 接続
const HOST = '';# ホスト名
const USER = '';# 接続ユーザー名
const PASS = '';# パスワード
const DB = "";#データベース名

$link;//globalで使うmysqliオブジェクト

# $dbname = DatabaseName: 接続するデータベース名(省略可能)
function connect() {
	global $link;
	$link = new mysqli(HOST, USER, PASS, DB);
	$ok = !$link->connect_error;
	if($ok) $link->set_charset('utf8mb4');# 文字コード指定 UTF-8ではなくutf8mb4
	return $ok;
}

function select($sql, $display = false) {
	global $link;
	$result = $link->query($sql);
	$rows = [];
	while($row = $result->fetch_assoc()) $rows[] = $row;
	$result->close();
	if($display) echo "{$sql}\n";
	return $rows;
}

function execute($sql, $display = false) {
	global $link;
	$link->query($sql);
	if($display) echo "{$sql}\n";
	return !$link->error;
}
?>