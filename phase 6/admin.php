<?php
session_start();
include_once('lib/db.inc.php');
function auth() {
if (!empty($_SESSION['auth']))
return $_SESSION['auth']['em'];
if (!empty($_COOKIE['auth'])) {
if ($t = json_decode($_COOKIE['auth'], true)) {
if (time() > $t['exp']) return false; // to expire the user

global $db; // validate if auth. token matches the DB record
$db = ierg4210_DB();
$q = $db->prepare('SELECT salt, password FROM users WHERE email = ?');
if ($q->execute(array($t['em']))
&& ($r = $q->fetch())
&& $t['k'] == hash_hmac('sha1', $t['exp'] . $r['password'],
$r['salt'])) {
$_SESSION['auth'] = $_COOKIE['auth'];
return $t['em'];
}
return false; // or header('Location: login.php');exit();
}
}
}
$user=auth();
if(!$user){
 header('Location: login.php');
 exit();
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	
	<title>IERG4210 Shop - Admin Panel</title>
	<link href="incl/admin.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<?php
	echo "hello!".$user;
	echo '<br/>';
	echo '<a href="logout.php">log out!</a>';
	echo '</br>';
	echo '<a href="changepassword.php">change password!</a>';
	?>
<h1>IERG4210 Shop - Admin Panel</h1>
<article id="main">

<section id="categoryPanel">
	<fieldset>
		<legend>New Category</legend>
		<form id="cat_insert" method="POST" action="admin-process.php?action=cat_insert" onsubmit="return false;">
			<label for="cat_insert_name">Name</label>
			<div><input id="cat_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input id="nouce" type="hidden" name="nouce" value=
<?php
	$nouce=rand(10000000,99999999);
	$_SESSION['nouce'] =  $nouce;
	echo $nouce;
?>

			/>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
	
	<!-- Generate the existing categories here -->
	<ul id="categoryList"></ul>
</section>

<section id="categoryEditPanel" class="hide">
	<fieldset>
		<legend>Editing Category</legend>
		<form id="cat_edit" method="POST" action="admin-process.php?action=cat_edit" onsubmit="return false;">
			<label for="cat_edit_name">Name</label>
			<div><input id="cat_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" id="cat_edit_catid" name="catid" />
			<input id="nouce" type="hidden" name="nouce" value=
<?php
	echo $nouce;
?>

			/>
			<input type="submit" value="Submit" /> <input type="button" id="cat_edit_cancel" value="Cancel" />
		</form>
	</fieldset>
</section>

<section id="productPanel">
	<fieldset>
		<legend>New Product</legend>
		<form id="prod_insert" method="POST" action="admin-process.php?action=prod_insert" enctype="multipart/form-data">
			<label for="prod_insert_catid">Category *</label>
			<div><select id="prod_insert_catid" name="catid"></select></div>

			<label for="prod_insert_name">Name *</label>
			<div><input id="prod_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

			<label for="prod_insert_price">Price *</label>
			<div><input id="prod_insert_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_insert_description">Description</label>
			<div><textarea id="prod_insert_description" name="description" pattern="^[\w\-, ]$"></textarea></div>

			<label for="prod_insert_name">Image *</label>
			<div><input type="file" name="file" required="true" accept="image/jpeg" /></div>
			<input id="nouce" type="hidden" name="nouce" value=
<?php
	echo $nouce;
?>

			/>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
	

	
	<!-- Generate the corresponding products here -->
	<ul id="productList"></ul>

</section>

	<section id="productEditPanel" class="hide">
	<fieldset>
		<legend>Editing Product</legend>
		<form id="prod_edit" method="POST" action="admin-process.php?action=prod_edit" enctype="multipart/form-data">
			<label for="prod_edit_catid">Category *</label>
			<div><select id="prod_edit_catid" name="catid"></select></div>

			<label for="prod_edit_name">Name *</label>
			<div><input id="prod_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

			<label for="prod_edit_price">Price *</label>
			<div><input id="prod_edit_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_edit_description">Description</label>
			<div><textarea id="prod_edit_description" name="description" pattern="^[\w\-, ]$"></textarea></div>

			<label for="prod_edit_name">Image *</label>
			<div><input type="file" name="file" required="true" accept="image/jpeg" /></div>
			<input type="hidden" id="prod_edit_pid" name="pid" />
			<input id="nouce" type="hidden" name="nouce" value=
<?php
	echo $nouce;
?>

			/>
			<input type="submit" value="Submit" /><input type="button" id="prod_edit_cancel" value="Cancel" />
		</form>
	</fieldset>
	</section>



<div class="clear"></div>
</article>
<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript">
(function(){

	function updateUI() {
		myLib.post({action:'cat_fetchall',nouce:<?php
	echo $nouce;
?>}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug): 
			for (var options = [], listItems = [],
					i = 0, cat; cat = json[i]; i++) {
				options.push('<option value="' , parseInt(cat.catid) , '">' , cat.name.escapeHTML() , '</option>');
				listItems.push('<li id="cat' , parseInt(cat.catid) , '"><span class="name">' , cat.name.escapeHTML() , '</span> <span class="delete">[Delete]</span> <span class="edit">[Edit]</span></li>');
			}
			el('prod_insert_catid').innerHTML = '<option></option>' + options.join('');
			el('prod_edit_catid').innerHTML = '<option></option>' + options.join('');
			el('categoryList').innerHTML = listItems.join('');
		});
		el('productList').innerHTML = '';
	}
	function updateProd(id){
	myLib.post({action:'prod_fetchall',catid : id,nouce:<?php
	echo $nouce;
?>}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug): 
			for (var options = [], listItems = [],
					i = 0, prod; prod = json[i]; i++) {
				options.push('<option value="' , parseInt(prod.pid) , '">' , prod.name.escapeHTML() , '</option>');
				listItems.push('<li id="prod' , parseInt(prod.pid) , '"><span class="name">' , prod.name.escapeHTML() , '</span> <span class="delete1">[Delete]</span> <span class="edit1">[Edit]</span></li>');
			}
			el('productList').innerHTML = listItems.join('');
		});
	}
	updateUI();
	
	el('categoryList').onclick = function(e) {
		if (e.target.tagName != 'SPAN')
			return false;
		
		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^cat/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;
		
		// handle the delete click
		if ('delete' === target.className) {
			confirm('Sure?') && myLib.post({action: 'cat_delete', catid: id,nouce:<?php
	echo $nouce;
?>		}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});
		
		// handle the edit click
		} else if ('edit' === target.className) {
			// toggle the edit/view display
			el('categoryEditPanel').show();
			el('categoryPanel').hide();
			
			// fill in the editing form with existing values
			el('cat_edit_name').value = name;
			el('cat_edit_catid').value = id;
		
		//handle the click on the category name
		} else {
			el('prod_insert_catid').value = id;
			// populate the product list or navigate to admin.php?catid=<id>
			updateProd(id);
		}
	}
	el('productList').onclick = function(e) {
		if (e.target.tagName != 'SPAN')
			return false;
		
		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^prod/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;
		// handle the delete click
		if ('delete1' === target.className) {
			confirm('Sure?') && myLib.post({action: 'prod_delete', pid: id,nouce:<?php
	echo $nouce;
?>}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});
		
		// handle the edit click
		} else if ('edit1' === target.className) {
			// toggle the edit/view display
			el('productEditPanel').show();
			el('productPanel').hide();
			
			// fill in the editing form with existing values
			el('prod_edit_name').value = name;
			el('prod_edit_pid').value = id;
		
		//handle the click on the category name
		} 
	}
	
	el('cat_insert').onsubmit = function() {
		return myLib.submit(this, updateUI);
	}
	el('cat_edit').onsubmit = function() {
		return myLib.submit(this, function() {
			// toggle the edit/view display
			el('categoryEditPanel').hide();
			el('categoryPanel').show();
			updateUI();
		});
	}
	el('cat_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('categoryEditPanel').hide();
		el('categoryPanel').show();
	}
	
	
	el('prod_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('productEditPanel').hide();
		el('productPanel').show();
	}
})();
</script>
</body>
</html>
