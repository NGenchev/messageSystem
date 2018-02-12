<?php
	## This system is created by NGenchev & NGeorgiev
	session_start();
	if(isset($_SESSION) && $_SESSION['logged']===TRUE): //check user logged already
		header('Location: messages.php');
	endif;

	if(isset($_POST['LogIn'])): //check user login credetentials
		extract($_POST); // code, password
		require_once('includes/database.php');
		require_once('includes/functions.php');
		$dbh = new DBConnection();
		$dbh = $dbh->pdo_conn();

		$func = new Functions();
		$password = $func->encrypt($password);

		$query = "SELECT * FROM users WHERE user_code = '{$code}' AND user_password = '{$password}'";
		$sql = $dbh->prepare($query);
		$sql->execute();

		$count = $sql->rowCount();
		if($count === 1)
		{
			if(session_id() == '' || !isset($_SESSION))
				session_start();

			$userRow = $sql->fetch();

			// set data for user
			$_SESSION['logged'] = TRUE;
			$_SESSION['name'] = $userRow['user_name'];
			$_SESSION['type'] = $userRow['user_type'];

			header('Location: messages.php');
		}
		else
			$message = "Невалидни данни!";

	endif;

	## Please contact us at (thewinner10000001[at]gmail[dot]com) for help, upgrade or bugs!
?>
<!doctype html>
<html>
	<head>
		<meta charset='utf-8'>
		<title> Teachers Message System </title>
		<link rel='stylesheet' href='styles/main.css'>
	</head>
	<body>
		<div class="login-page">
		  <div class="form">
		  	<h3>
		  		Служебен вход за съобщения
		  	</h3>
		    <form class="login-form" action="" method="POST">
		      <p class="message"><?= $message ?? null ?></p>
		      <input type="text" maxlength="2" pattern="[0-9]{0,2}" name="code" placeholder="Код на учител"/>
		      <input type="password" minlenght="6" pattern=".{5,100}" name="password" placeholder="Парола"/>
		      <button type='submit' name='LogIn'>Вход в системата</button>
		    </form>
		  </div>
		</div>
	</body>
</html>