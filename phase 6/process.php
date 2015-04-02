<?php
include_once('lib/db.inc.php');

function ierg4210_cat_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	if ($q->execute())
		return $q->fetchAll();
}
function ierg4210_shoppingcart() {
$_POST['pid'] = (int) $_POST['pid'];
global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products where pid = ? ;");
	if ($q->execute(array($_POST['pid'])))
		return $q->fetch();
		}
// input validation

function ierg4210_loadproduct(){
	global $db;
	$db = ierg4210_DB();
	$_GET['catid']=(int)$_GET['catid'];
	$q = $db->prepare("SELECT * FROM products where catid = ? ;");
	if ($q->execute(array($_GET['catid'])))
		return $q->fetchAll();
		}

function ierg4210_buildOrder(){
global $db;
$db = ierg4210_DB();
$a=json_decode($_POST['list'], true);
$count=0;
$totalprice=0;
$salt=rand(10000000,99999999);
$string='HKD,hx011@ie.cuhk.edu.hk,';
while(isset($a[$count])){
$a[$count]['pid']=(int)$a[$count]['pid'];
$a[$count]['qty']=(int)$a[$count]['qty'];
if($a[$count]['qty']<0)
return;
$string.=$a[$count]['pid'].','.$a[$count]['qty'].',';
$q = $db->prepare("SELECT * FROM products where pid = ?;");
if($q->execute(array($a[$count]['pid'])))
$m=$q->fetch();
else
return;
$string.=$m['price'].',';
$totalprice+=$m['price'] * $a[$count]['qty'];

$count++;
}
$string.=$totalprice;
$digest = hash_hmac('sha1', $string,$salt);
$q = $db->prepare("insert into orders (salt,digest) values(?,?);");
$q->execute(array($salt,$digest));
$data['digest']=$digest;
$data['invoice']= $db->lastInsertId();
return $data;
}
header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
try {
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo  json_encode($returnVal);
} catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}
?>
