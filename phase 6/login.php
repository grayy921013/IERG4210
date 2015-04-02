
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
if($user){
 header('Location: admin.php');
 exit();
}
?>

<html>
<head>
<title>please log in</title>
</head>
<body>
<section id="login">
	<fieldset>
		<legend>Please Log In!</legend>
		<form id="login" method="POST" action="auth-process.php">
			<label for="emailaddress">Email</label>
			<div><input id="emailaddress" type="text" name="em" required="true" pattern="^[\w=+\-\/][\w=\'+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$" /></div>
			<label for="password">Password</label>
			<div><input id="Password" type="password" name="password" required="true" pattern="^[\w\-, ]+$" /></div>
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