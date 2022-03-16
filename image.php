<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>画像アップロード</title>
	<style>.mb-1{margin-bottom: 1em;}</style>
</head>
<body>
<form method="post" enctype="multipart/form-data" action="app.php?mode=img">
	<div class="mb-1"><label for="up">画像を選択</label><br><input type="file" name="up"></div>
	<div class="mb-1"><label for="alt">画像の説明</label><br><input type="text" name="alt" id="alt" placeholder="画像の説明　教材:大学の廊下"></div>
	<div><button>アップロード</button></div>
</form>
</body>
</html>