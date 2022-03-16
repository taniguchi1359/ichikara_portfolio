<?php
# 現在のページ
$ARTICLE_PER_PAGE = 4;
$current = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$offset = ($current - 1) * $ARTICLE_PER_PAGE;

# データベース処理機能を読み込み
require_once 'database.php';

# <article class="grid"><a href="article.php"><p class="blogtitle"><img src="images/blogimage.jpg" width="200" height="150" alt=""><br>ブログタイトル</p></a></article>
connect();# 接続

# DB:SELECT
$getY = empty($_GET['y']) ? 0 : (int)$_GET['y'];
$getM = empty($_GET['m']) ? 0 : (int)$_GET['m'];
$y = $getY ? ' YEAR(`posts`.`created_at`)=' . $getY : '';
$m = $getM ? ($y != '' ? ' AND' : '') . ' MONTH(`posts`.`created_at`)=' . $getM : '';
$where = $y != '' ? ' WHERE' . $y . $m : '';
$search = !empty($_GET['input1']) ? str_replace("'", "''", $_GET['input1']) : '';
if($search) {
	$where .= $where ? ' AND' : ' WHERE';
	$where .= " (`posts`.`title` LIKE '%{$search}%' OR `posts`.`content` LIKE '%{$search}%' OR `posts`.`youtube` LIKE '%{$search}%')";
}
$posts = select('SELECT `posts`.`id`,`posts`.`title`,`posts`.`created_at`,`images`.`src`,`images`.`alt` FROM `posts` LEFT JOIN `images` ON `posts`.`icon`=`images`.`id`' . $where . ' ORDER BY `posts`.`id` DESC LIMIT ' . $ARTICLE_PER_PAGE . ' OFFSET ' . $offset . ';');
$ym = select('SELECT `posts`.`created_at` FROM `posts` LEFT JOIN `images` ON `posts`.`icon`=`images`.`id`;');
$html = '';
$py = 0;
$pm = 0;

# 記事HTML作成
foreach($posts as $post) $html .= '<article class="grid"><a href="article.php?id=' . $post['id'] . '"><p class="blogtitle"><img src="' . ($post['src'] ?? 'images/blogimage.jpg') . '" width="200" height="150" alt="' . ($post['alt'] ?? '') . '"><br>' . htmlspecialchars($post['title'], ENT_QUOTES) . '</p></a></article>';

# アーカイブHTML作成
$archives='';//変数定義HTML
$yearly = select('SELECT YEAR(`created_at`) AS `year` FROM `posts` GROUP BY `year` ORDER BY `year` DESC;');
foreach($yearly as $year) {
	$monthly = select('SELECT MONTH(`created_at`) AS `month` FROM `posts` WHERE YEAR(`created_at`)=' . $year['year'] . ' GROUP BY `month`;');
	$checked = $year['year'] == $getY ? " checked":'';
	$archives .='<div><input type="checkbox" class="toggle" id="year' . $year['year'] . '"'. $checked .'><div class="toggle-outer"><label for="year' . $year['year'] . '">▼</label><span>' . $year['year'] . '年</span></div><div class="toggle-inner"><ul>';
	foreach($monthly as $month) {
		$archives .= '<li><a href="?y=' . $year['year'] . '&m=' . $month['month'] . '">' . $year['year'] . '年' . $month['month'] . '月</a></li>';
	}
	$archives .='</ul></div></div>';
}

# DB:SELECT
$pageCount = ceil(select('SELECT COUNT(1) AS c FROM `posts` LEFT JOIN `images` ON `posts`.`icon`=`images`.`id`' . $where . ';')[0]['c'] / 4);
$link->close();# 切断

$query = '';
if($y) {
	$query = 'y=' . $getY;
	if($m) $query .= '&m=' . $getM;
}
$query .= $search ? ($query ? '&' : '') . 'input1=' . $search : '';
$page = '';
if($pageCount < 6) {
	for($i = 1; $i <= $pageCount; ++$i) {
		$pquery = '';
		if($i > 1) {
			$page .= '・';
			$pquery = ($query ? '&' : '') . 'page=' . $i;
		}
		$page .= $i != $current ? '<a href="?' . $query . $pquery . '">' . $i . '</a>' : $i;
	}
} else {
	if($current > 3) $page .= '<a href="?' . $query . '">1</a>…';
	$firstPage = max(1, $current - 2);
	$lastPage = min($firstPage + 4, $pageCount);
	for($i = $firstPage; $i <= $lastPage; ++$i) {
		if($i > $firstPage) $page .= '・';
		$pquery = $i > 1 ? ($query ? '&' : '') . 'page=' . $i : '';
		$page .= $i !== $current ? '<a href="?' . $query . $pquery . '">' . $i . '</a>' : $i;
	}
	$pquery = ($query ? '&' : '') . 'page=' . $pageCount;
	if($current < $pageCount - 2) $page .= '…<a href="?' . $query . $pquery . '">' . $pageCount . '</a>';
}
?><!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>イチから始める農業生活-TOPページ-</title>
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

<div id="wrapper" class="inner">
	<div id="main">
		<!-- ブログ記事 -->
		<section class="gridWrapper"><?php echo $html; ?></section>
		<!-- /ブログ記事 -->

		<!-- サイドバー -->
		<div class="Side">
			<div class="sns"><iframe width="240" height="135" src="https://www.youtube.com/embed/dEyR_DDBCNk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
			<div>
				<form action="" method="get">
					<!-- 任意の<input>要素＝入力欄などを用意する -->
					<input type="hidden" name="y" value="<?php echo $getY;?>">
					<input type="hidden" name="m" value="<?php echo $getM;?>">
					<input type="text" name="input1">
					<!-- 検索ボタンを用意する -->
					<button>検索<button>
				</form>
			</div>
			<div>
				<div>アーカイブ</div>
				<?php echo $archives; ?>
			</div>
		</div>
		<!-- /サイドバー -->

		<!-- ページ送り -->
		<div id="readmorebutton"><?php echo $page; ?></div>
		<!-- /ページ送り -->
	</div>
	<!-- /main -->

	<!-- プロフィール -->
	<section class="content">
		<h1 class="heading">概要欄/プロフィール</h1>
		<article>
			<img src="images/kao.jpg" width="140" height="120" alt="" class="alignleft frame">
			<p>概要欄文章とプロフィール</p>
		</article>
	</section>
	<!-- /プロフィール -->
</div>
<!-- / WRAPPER -->
</body>
</html>