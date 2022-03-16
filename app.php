<?php
function h($text, $ent = ENT_QUOTES, $charset = NULL, $double = true) {
	echo htmlspecialchars($text, $ent, $charset, $double);
}

# データベース処理機能を読み込み
require_once 'database.php';

$mode = '';
function ex() {
	global $link;
# モードがなければ終了
	if(empty($_GET['mode'])) return;
	$mode = $_GET['mode'];

	switch($_GET['mode']) {
		case 'img':
			$up_file  = '';
			$ok = false;
			$tmp_file = isset($_FILES['up']['tmp_name']) ? $_FILES['up']['tmp_name'] : '';
			$org_file = isset($_FILES['up']['name']) ? $_FILES['up']['name'] : '';
			$alt = '';
			if(!empty($tmp_file) && is_uploaded_file($tmp_file)) {
				$split = explode('.', $org_file);# ファイル名を分割して…
				$ext = end($split);# 拡張子を取得
				if(!empty($ext) && $ext != $org_file) {
					$up_file = 'img/' . date('Ymd_His.') . mt_rand(1000, 9999) . '.' . $ext;
					$ok = move_uploaded_file($tmp_file, $up_file);
					if($ok) {
						//$link->query('INSERT…');
						$alt = !empty($_POST['alt']) ? "'" . str_replace("'", "''", $_POST['alt']) . "'" : 'NULL';
						connect('ichikara');# 接続
						execute("INSERT INTO `images` (`original`,`src`,`alt`) VALUES('{$org_file}','{$up_file}',{$alt});");
						$id = select("SELECT MAX(`id`) AS `currentID` FROM `images`;")[0]['currentID'];
						$link->close();# 切断
					}
				}
			}
?><!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8">
	<title>ファイル受信ページ</title>
</head><body>
	<div>画像:<?php echo $ok ? '<img src="' . $up_file . '" alt=' . $alt . '>' : 'アップロードは失敗しました。'; ?></div>
	<div>ID:<?php echo $id; ?></div>
	<div>元のファイル名:<?php echo $org_file; ?></div>
	<div>一時ファイル名:<?php echo $tmp_file; ?></div>
	<div>実際のファイル:<?php echo $up_file; ?></div>
	<div><a href="image.php">アップロードページへ戻る</p></div>
</body>
</html><?php
			break;

		case 'create':# $_POST => title, content, icon, youtube
			if(empty($_POST['title']) || empty($_POST['content'])) return;
			connect('ichikara');# 接続
			$youtube = !empty($_POST['youtube']) ? "'" . str_replace("'", "''", $_POST['youtube']) . "'" : 'NULL';
			$iconid = empty($_POST['icon']) ? 'NULL' : (int)$_POST['icon'];
			$sql = "INSERT INTO `posts` (`title`,`content`,`youtube`,`icon`) VALUES('" . str_replace("'", "''", $_POST['title']) . "','" . str_replace("'", "''", $_POST['content']) . "'," . $youtube . ',' . $iconid . ');';
			$icon = '';
			if($iconid != 'NULL') {
				$s = select('SELECT `src` FROM `images` WHERE `id`=' . $iconid . ';');
				if(count($s)) $icon = $s[0]['src'];
			}
			$ok = execute($sql);
			$link->close();# 切断
?><!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8">
	<title>投稿結果</title>
	<style>.error { color: red; }</style>
	<link rel="stylesheet" href="style.css">
</head><body>
<!-- タイトル -->
<div id="mainBanner" class="mainImg">
  <div class="inner">
		<img src="images/ichigo.jpg" alt="" width="930" height="290">
    <div class="title">
			<h1>イチから始める農業生活</h1>
		</div>
	</div>
</div>
<!-- / タイトル -->

<center<?php if(!$ok) echo ' class="error"'; ?> style="margin-bottom:15px;" ><?php echo $ok ? '投稿完了' : $sql; ?></center>

<div id="wrapper" class="inner">
	<section id="main">
		<section class="content">
			<h3 class="heading"><?php h($_POST['title']); ?></h3>  <!--タイトル-->
			<article>
				<div><?php h($_POST['content']); ?></div>  <!--本文-->
				<div><img src="<?php h($icon); ?>" class="alignright frame"></div><!--サムネ-->
				<div><iframe width="440" height="300" src="<?php if(!empty($_POST['youtube'])) h($_POST['youtube']); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><!--youtube-->
			</article>
		</section>
	</section>
</div>
<center style="margin-bottom:15px;"><a href="post.php">投稿ページへ戻る</a></center>
</body></html><?php
			break;
		case 'update':
			if(empty($_POST['choice']) || empty($_POST['title']) || empty($_POST['content'])) return;
			connect('ichikara');# 接続
			$youtube = !empty($_POST['youtube']) ? "'" . str_replace("'", "''", $_POST['youtube']) . "'" : 'NULL';
			$iconid = empty($_POST['icon']) ? 'NULL' : (int)$_POST['icon'];
			$sql = "UPDATE `posts` SET `title`='" . str_replace("'", "''", $_POST['title']) . "',`content`='" . str_replace("'", "''", $_POST['content']) . "',`youtube`=" . $youtube . ',`icon`=' . $iconid . ' WHERE `id`=' . (int)$_POST['choice'] . ';';
			$icon = '';
			if($iconid != 'NULL') {
				$s = select("SELECT `src` FROM `images` WHERE `id`={$iconid};");
				if(count($s)) $icon = $s[0]['src'];
			}
			$ok = execute($sql);
			$link->close();# 切断
?><!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8">
	<title>投稿結果</title>
	<style>.error { color: red; }</style>
	<link rel="stylesheet" href="style.css">
</head><body>
<!-- タイトル -->
<div id="mainBanner" class="mainImg">
  <div class="inner">
		<img src="images/ichigo.jpg" alt="" width="930" height="290">
    <div class="title">
			<h1>イチから始める農業生活</h1>
		</div>
	</div>
</div>
<!-- / タイトル -->

<center<?php if(!$ok) echo ' class="error"'; ?> style="margin-bottom:15px;"><?php echo $ok ? '修正完了' : $sql; ?></center>

<div id="wrapper" class="inner">
	<section id="main">
		<section class="content">
			<h3 class="heading"><?php h($_POST['title']); ?></h3>  <!--タイトル-->
			<article>
				<div><?php h($_POST['content']); ?></div>  <!--本文-->
				<div><img src="<?php h($icon); ?>" class="alignright frame"></div><!--サムネ-->
				<div><iframe width="440" height="300" src="<?php if(!empty($_POST['youtube'])) h($_POST['youtube']); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><!--youtube-->
			</article>
		</section>
	</section>
</div>
<center style="margin-bottom:15px;"><a href="post.php">投稿ページへ戻る</a></center>
</body></html><?php
			break;
		case 'delete':
			if(empty($_POST['choice'])) return;
			connect('ichikara');# 接続
			$sql = 'DELETE FROM `posts` WHERE `id`=' . (int)$_POST['choice'] . ';';
			execute($sql);
			$link->close();# 切断
			header('Location: post.php');
			break;
	}
}
ex();
?>