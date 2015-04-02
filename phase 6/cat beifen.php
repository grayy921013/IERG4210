<?php
include_once('lib/db.inc.php');
global $db;
	$db = ierg4210_DB();
	$catid=(int)$_GET['catid'];
	$a=  $db->prepare("SELECT * FROM categories where catid = ?;");
	if($a->execute(array($catid))){
	if($a->fetch()){
	$q = $db->prepare("SELECT * FROM products where catid = ? LIMIT 100;");
	if($q->execute(array($catid))){
		include("header.html");
		echo '<div id="content">';
		echo '<ul id="itemContainer">';
		$num=0;
		while ($p= $q->fetch()){
		$num++;
		echo '<li>';
		echo '<div class="item">';
		echo '<div class="itemimg">';
		echo '<a href="prod.php?pid='.$p['pid'].'">';
		echo '<image src="incl/img/'.$p['pid'].'.jpg"/>';
		echo '</div>';
		echo '<p>';
		echo '<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2F54.200.131.210%2Fprod.php%3Fpid%3D'.$p['pid'].'&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:35px;" allowTransparency="true"></iframe>';
		echo $p['name'].'<br>'.'price:'.$p['price']."/kg";
		echo '</a><br></p><span>';
		echo '<image src="images/addtocart.png" onclick="addToCart('.$p['pid'].');initShopList();" />';
		echo '</span></div>';
		echo '</li>';
		}
		if($num==0)
		echo 'Still No Product In This Category!';
		echo '</ul>';
		echo '</div>';
		echo '<div class="holder"></div>';
		include("footer.html");
	}
	}
	else{
	include("header.html");
	echo '<div id="content">'.'No Such Category!!!'.'</div>';
	include("footer.html");
	}
	}
?>
