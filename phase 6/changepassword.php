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
	
	<title>IERG4210 Shop - Password Change Panel</title>
</head>

<body>
<?php
	echo "hello!".$user;
	echo '<br/>';
	?>
<article id="main">

<section id="changepassword">
	<fieldset>
		<legend>change password</legend>
		<form id="login" method="POST" action="password-process.php">
			<label for="oldpassword">Old Password</label>
			<div><input id="oldpassword" type="password" name="oldpassword" required="true" pattern="^[\w\-, ]+$"/></div>
			<label for="newpassword">New Password</label>
			<div><input id="newpassword" type="password" name="newpassword" required="true" pattern="^[\w\-, ]+$" /></div>
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
	

</section>


</body>
</html>
