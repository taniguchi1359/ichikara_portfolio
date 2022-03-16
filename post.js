function preview(id) {
	const dataId = document.getElementById(id).value;
	const prev = document.getElementById('preview_' + id);
	if(!dataId) {
		document.getElementById('image_tag').textContent = '';
		prev.innerHTML = '';
		return;
	}
	const img = data.images[dataId];

	// <img src="images/corridor.jpg" alt="大学の廊下" width="200" height="auto">
	prev.innerHTML = '<img src="' + img.src + '" alt="' + img.alt + '" width="' + document.getElementById('image-slider').value + '" height="auto">';

	if(id == 'icon') return;

	// [img id=1 width=200]
	document.getElementById('image_tag').textContent = '[img id=' + dataId + ' width=' + document.getElementById('image-slider').value + ']';
}

let mode = 'create';
window.onload = function() {
	document.getElementById('choice').onchange = function() {
		const dataId = document.getElementById('choice').value;
		if(!dataId) {// 新規作成
			mode = 'create';
			document.forms.myForm.action = 'app.php?mode=create';
			document.getElementById('post').textContent = '投稿';
			document.getElementById('delete').textContent = 'リセット';
			document.getElementById('title').value = '';
			document.getElementById('content').value = '';
			document.getElementById('youtube').value = '';
			document.getElementById('icon').selectedIndex = 0;
			preview('icon');
			return;
		}
		const post = data.posts[dataId];

		mode = 'update';
		document.forms.myForm.action = 'app.php?mode=update';
		document.getElementById('post').textContent = '修正';
		document.getElementById('delete').textContent = '削除';
		document.getElementById('title').value = post.title;
		document.getElementById('content').value = post.content;
		document.getElementById('youtube').value = post.youtube;
		const icons = document.querySelectorAll('#icon option');
		let iconId = 0;
		for(let i = 0; i < icons.length; ++i) {
			if(icons[i].value == post['icon']) {
				iconId = i;
				break;
			}
		}
		document.getElementById('icon').selectedIndex = iconId;
		preview('icon');
	};
	document.getElementById('delete').onclick = function() {
		if(mode == 'create') {
			if(!confirm('リセットしますか？')) return false;
			document.getElementById('title').value = '';
			document.getElementById('content').value = '';
			document.getElementById('youtube').value = '';
			document.getElementById('icon').selectedIndex = 0;
			return false;
		}
		if(!confirm('本当に削除しますか？')) return false;
		document.forms.myForm.action = 'app.php?mode=delete';
	};

	document.getElementById('icon').onchange = function() { preview('icon'); };
	document.getElementById('image').onchange = function() { preview('image'); };
	document.getElementById('image-slider').oninput = function() {
		document.getElementById('display-size').textContent = this.value;
		const image = document.querySelector('#preview_image img');
		if(!image) return;
		image.width = this.value;

		// [img id=1 width=200]
		document.getElementById('image_tag').textContent = '[img id=' + document.getElementById('image').value + ' width=' + this.value + ']';
	};
};