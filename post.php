<?php
# データベース処理機能を読み込み
require_once 'database.php';

# 接続
connect('ichikara');

#imagesテーブルの中身をすべて取得
$images = select("SELECT `id`,`original`,`src`,`alt` FROM `images`;");

$options = '<option value="">なし</option>';
$data = [];
$data['images'] = [ ['src' => '', 'alt' => ''] ];
foreach($images as $img) {
	$options .= '<option value="' . $img['id'] . '">' . $img['original'] . '</option>';
	$data['images'][$img['id']] = [
		'src' => $img['src'],
		'alt' => $img['alt']
	];
}

#postsテーブルの中身をすべて取得

$posts = select("SELECT `id`,`title`,`content`,`youtube`,`icon` FROM `posts`;");

$choice = '';

$data['posts'] = [ ['id' => '', 'title' => '', 'content' => '', 'youtube' => '', 'icon' => ''] ];
foreach($posts as $post) {
	$choice = '<option value="' . $post['id'] . '">' . $post['title'] . '</option>' . $choice;
	$data['posts'][$post['id']] = [
		'title' => $post['title'],
		'content' => $post['content'],
		'youtube' => $post['youtube'],
		'icon' => $post['icon']
	];
}
$choice = '<option value="">新規投稿</option>' . $choice;

# 切断
$link->close();
?><!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>イチから始める農業生活-投稿ページ-</title>
	<link rel="stylesheet" href="destyle.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	<link rel="stylesheet" href="style.css">
	<style>#delete { color: red; font-weight: bold; }</style>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	<script>const data = <?php echo json_encode($data); ?>;</script>
	<script src="post.js"></script>
</head>
<body>

<!-- タイトル -->
<div id="mainBanner" class="mainImg">
  <div class="inner">
		<img src="images/ichigo.jpg" alt="" width="930" height="290">
    <div class="title">
		<h1>イチから始める農業生活　-編集ページ-</h1>
    </div>
  </div>
</div>
<!-- / タイトル -->

<div id="wrapper">
	<form method="post" action="app.php?mode=create" name="myForm">

		<div><label for="choice">記事選択</label><br><select name="choice" id="choice"><?php echo $choice; ?></select></div>

		<div><label for="title">タイトル</label><br><input type="text" name="title" id="title"></div>
		<?php #改行コード（/n）→<br>に書き換え ?>
		<div><label for="content">本文</label><br><textarea name="content" id="content" rows="10" cols="60" placeholder="本文をここに記入してください 画像タグ：[img id=1 width=100]"></textarea></div>
		<?php #URL　差し替え（+""+""+）?>
		<div><label for="youtube">Youtube</label><br><input type="text" name="youtube" id="youtube" placeholder="youtubeURLを入力"></div>
		<div><a href="image.php" target="new">画像アップロードページ</a></div>

		<div><label for="image">本文画像選択</label><br><select name="image" id="image"><?php echo $options; ?></select></div>
		<div>画像タグ：<span id="image_tag"></span></div>
		<div id="slider-container" class="center-pad"><span id="image-size">画像サイズ：<span id="display-size">200</span>px</span><br><input type="range" id="image-slider" value="200" min="100" max="1000" step="1"></div>
		<div id="preview_image"></div>

		<div><label for="icon">サムネ画像選択</label><br><select name="icon" id="icon"><?php echo $options; ?></select></div>
		<div id="preview_icon"></div>

		<!-- 投稿→修正　リセット→削除 -->
		<div><button id="post">投稿</button></div>
		<div><button id="delete">リセット</button></div>
	</form>
</div>
</body>
</html>