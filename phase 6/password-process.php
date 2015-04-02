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
global $db;
$db = ierg4210_DB();
$q = $db->prepare('SELECT salt, password FROM users WHERE email = ?');
if ($q->execute(array($user))
&& ($r = $q->fetch())
&& ($r['password'] == hash_hmac('sha1', $_POST['oldpassword'], $r['salt']))&& ($_SESSION['nouce'] == $_POST['nouce'])&&$_SESSION['nouce']){
$storedPW = hash_hmac('sha1', $_POST['newpassword'], $r['salt']);
$q = $db->prepare("update users set password=? where email = ?");
$q->execute(array($storedPW,$user));
session_destroy();
setcookie("auth", '');
echo '<HTML><HEAD><META HTTP-EQUIV="REFRESH" CONTENT="3; URL=';
echo "index.php";
echo '"></HEAD><BODY>Your password is changed successfully<br/>You will be redirected to index page in 3 seconds</BODY></HTML>';
}
else{
echo '<HTML><HEAD><META HTTP-EQUIV="REFRESH" CONTENT="3; URL=';
echo "admin.php";
echo '"></HEAD><BODY>Your password is incorrect<br/>You will be redirected to admin panel page in 3 seconds</BODY></HTML>';
}
?>
