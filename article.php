<?php
# データベース処理機能を読み込み
require_once 'database.php';

# 変数宣言+初期化
$title = '';
$src = '';
$alt = '';
$content = '';
$youtube = '';

if(!empty($_GET['id'])) {
	connect('ichikara');# 接続
	$post = select('SELECT `posts`.`title`,`posts`.`content`,`posts`.`youtube`,`images`.`src`,`images`.`alt` FROM `posts` LEFT JOIN `images` ON `posts`.`icon`=`images`.`id` WHERE `posts`.`id`=' . (int)$_GET['id'] . ';');
	$link->close();# 切断
	if(count($post)) {
		$title = $post[0]['title'];
		$content = $post[0]['content'];
		$src = $post[0]['src'];
		$alt = $post[0]['alt'];
		$youtube = $post[0]['youtube'];
	}
}

function content($c) {
	global $link;
	connect('ichikara');# 接続

# [img id=1 width=200]
	$expr = '/\[img id=(\d+) width=(\d+)\]/';
	$matches = [];

# https://www.php.net/manual/ja/function.preg-match-all.php
	preg_match_all($expr, $c, $matches);
	$tags = [];

#	[
#		[# $matches[0]
#			'[img id=1 width=320]',
#			'[img id=2 width=200]',
#			'[img id=3 width=40]'
#		],
#		[# $matches[1]
#			'1',
#			'2',
#			'3'
#		],
#		[# $matches[2]
#			'320',
#			'200',
#			'40'
#		]
#	]

	$i = 0;
	foreach($matches[1] as $id) {
		$img = select("SELECT `src`,`alt` FROM `images` WHERE `id`={$id};");
		$tags[] = '<img src="' . $img[0]['src'] . '" alt="' . $img[0]['alt'] . '" width="' . $matches[2][$i++] . '">';
	}
	$html = '';
	$i = 0;
	foreach(preg_split($expr, $c) as $text) {
		$html .= $text;
		if(!empty($tags[$i])) $html .= $tags[$i++];
	}
	$link->close();# 切断

	echo str_replace("\n", '<br>', $html);
}

#youtubeURL書き換え
# ~.com/?v=fiesigej&s=1
# 動画IDを取り出す正規表現 '/v=([^&]+)/'
function youtube($y) {
	$expr = '/v=([^&]+)/';
	$matches = [];
	preg_match($expr, $y, $matches);
	if(!empty($matches[1])){
		echo '<iframe id="a_youtube" width="240" height="135" src="https://www.youtube.com/embed/'.$matches[1].'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
	}
}
?><!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>イチから始める農業生活-ブログ記事-</title>
<link rel="stylesheet" href="destyle.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<link rel="stylesheet" href="style.css">
<script src="script.js"></script>
</head>
<body>
<!-- タイトル -->
<header id="mainBanner" class="mainImg">
	<div class="inner">
		<a href="./"><img src="images/ichigo.jpg"><div class="title"><h1>イチから始める農業生活</h1></div></a>
	</div>
</header>
<!-- / タイトル -->

<div id="wrapper">
	<!-- コンテンツ -->
	<section id="main">
		<section class="content">
			<h3 class="heading"><?php echo $title; ?></h3>
			<article>
				<p><?php content($content); ?></p>
				<div><?php youtube($youtube); ?></div>
			</article>
		</section>
	</section>
</div>
<!-- / WRAPPER -->

<div id="readmorebutton"><a href="./">&raquo; TOPへ</a></p>
</body>
</html>