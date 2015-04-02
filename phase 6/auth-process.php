<?php
session_start();
/* apply server-side validations here */
include_once('lib/db.inc.php');
global $db;
$db = ierg4210_DB();
$q = $db->prepare('SELECT salt, password FROM users WHERE email = ?');
if ($q->execute(array($_POST['em']))
&& ($r = $q->fetch())
&& ($r['password'] == hash_hmac('sha1', $_POST['password'], $r['salt']))&& ($_SESSION['nouce'] == $_POST['nouce'])&&$_SESSION['nouce']){
$exp = time() + 3600 * 24 * 3; // 3days
$token = array(
'em'=>$_POST['em'],
'exp'=>$exp, // expiry date
'k'=> hash_hmac('sha1', $exp . $r['password'], $r['salt']),);
// create the cookie
setcookie('auth', json_encode($token), $exp,NULL,NULL,NULL,TRUE);
// put it also in $_SESSION
$_SESSION['auth'] =  $token;
// change the PHPSESSID after login
session_regenerate_id();
header("Location:admin.php");
exit();
// When successfully authenticated,
// 1. create authentication token
// 2. redirect to admin.php
} else {
echo '<HTML><HEAD><META HTTP-EQUIV="REFRESH" CONTENT="3; URL=';
echo "login.php";
echo '"></HEAD><BODY>Your email or password is incorrect<br/>You will be redirected to login page in 3 seconds</BODY></HTML>';
}
?>