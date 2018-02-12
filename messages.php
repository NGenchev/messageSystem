<?php
	session_start();
	if(!isset($_SESSION) || $_SESSION['logged']!=TRUE): //check user logged already
		header('Location: index.php');
	endif;
	require_once('includes/database.php');
	require_once('includes/functions.php');

	$func = new Functions();

	$dbh = new DBConnection();
	$dbh = $dbh->pdo_conn();
?>
<!DOCTYPE html>
<html>
<head>
<title>Служебни съобщения</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles/layout.css" type="text/css" media="all">
<link rel="stylesheet" href="styles/mediaqueries.css" type="text/css" media="all">
<script src="scripts/jquery.1.9.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="scripts/jquery-mobilemenu.min.js"></script>
<!--[if lt IE 9]>
<link rel="stylesheet" href="styles/ie.css" type="text/css" media="all">
<script src="scripts/ie/css3-mediaqueries.min.js"></script>
<script src="scripts/ie/ie9.js"></script>
<script src="scripts/ie/html5shiv.min.js"></script>
<![endif]-->
<style type="text/css">
div.full_width{margin-top:20px;}
div.full_width:first-child{margin-top:0;}
div.full_width div{color:#666666; background-color:#DEDEDE;}
</style>
</head>
<body>
<div class="wrapper row1">
  <header id="header" class="clear">
    <div id="hgroup">
      <h1><a href="#">Служебни съобщения</a></h1>
      <h2>Уеб сайт за учители и нови съобщения</h2>
    </div>
  </header>
</div>
<!-- ################################################################################################ -->
<div class="wrapper row2">
  <nav id="topnav">
    <ul class="clear">
      	<li class="active first"><a href="messages.php">Съобщения</a></li>
      	<li><a href="logout.php">Изход</a></li> 	
    </ul>
  </nav>
</div>
<!-- content -->
<div class="wrapper row3">
  <div id="container">
    <!-- #########################    B E G I N    ######################################### -->
    <h1>Добре дошли в системата, г-н/г-жо <?= $_SESSION['name'] ?>!</h1>

    <?php if($_SESSION['type'] === 1): ?>
    	<?php 
    		if(isset($_POST['addNewMessage']) && $_SESSION['type'] === 1):
    			extract($_POST);

    			$query = "INSERT INTO messages (`message_content`, `message_author`) VALUES ('{$content}', '{$_SESSION['name']}')";
    			$sql = $dbh->prepare($query);
    			$sql->execute();
    			if($sql->rowCount() == 1)
    				$msg = "Успешно добавихте съобщение!";
    			else
    				$msg = "Проблем с добавянето на съобщение, моля свържете се с администратор!";
    		endif;

    		if(isset($_POST['addNewTeacher']) && $_SESSION['type'] === 1):
    			extract($_POST);
    			$pass = $func->encrypt($password);

    			$deleteSql = "DELETE FROM users WHERE user_code = '$code'"; //if already exist DELETE!
    			$deleteSql = $dbh->prepare($deleteSql);
    			$deleteSql->execute();
    			$count = $deleteSql->rowCount();
    			
    			$query = "INSERT INTO users (`user_code`, `user_name`, `user_type`, `user_password`) VALUES ('{$code}', '{$name}', '{$rights}', '{$pass}')";
    			$sql = $dbh->prepare($query);
    			$sql->execute();
    			if($sql->rowCount() == 1)
    				$msg2 = $count == 1 ? "Премахнахте учител №{$code} и добавихте новият учител!" : "Успешно добавихте учител!";
    			else
    				$msg2 = "Проблем с добавянето на учител, моля свържете се с администратор!";
    		endif;
    	?>
    	<div class='FunctionBox'>
    		<h1> Администраторски панел: </h1>
    		<BR>
	    	<div class='box'>
		    	<h3> Статус: <?= $msg ?? "няма" ?> </h3>
		    	<form action='' method="post">
		    		<textarea rows='5' cols='50' name='content'></textarea> <BR>
		    		<button type='submit' name='addNewMessage'>Публикувай!</button> 
		    	</form>
		    </div>

	    	<div class="box">
	    	<h3> Статус: <?= $msg2 ?? "няма" ?> </h3>
	    	<form action='' method="post">
	    		<input type='number' min='0' max='100' name='code' placeholder="Код на учител"> <BR>
	    		<input type='text' name='name' placeholder="Име на учител"> <BR>
	    		<input type='number' min='100000' max='999999' pattern='[0-9]{6}' name='password' placeholder="Парола"> <BR>
	    		<input type="checkbox" id="rights" name="rights" value="1">
    			<label for="rights">Администраторски права</label> <BR><BR>
	    		<button type='submit' name='addNewTeacher'>Добави!</button> 
	    	</form>
	    	</div>
		</div>
    	<script type='text/javascript'>
			function deleteMsg(id)
			  {
				var x = confirm('Сигурни ли сте, че искате да изтриете съобщение #'+id);
				if(x)
				{
					$.ajax({
						url: 'includes/deleteResponse.php',
						type: 'POST',
						data: {num:id},
						success: function (data) {
							alert(JSON.parse(JSON.parse(JSON.stringify(data))));
							$('#msg-'+id).hide(500);
						}
					});
				}
			  }
			</script>
    <?php endif; // check for add new message ?>

    <?php
		$query = "SELECT * FROM messages ORDER BY message_added DESC";
		$sql = $dbh->prepare($query);
		$sql->execute();

		if($sql->rowCount() == 0)
			echo "<h1 style='font-size: 25pt!important; color: RED;'> Няма добавени съобщения! </h1>";
		else
			$messages = $sql->fetchAll();

		$index = 1;
    ?>

    <?php 
    	if($sql->rowCount() != 0):
    		foreach($messages as $msg){ 
    ?>
    	<?= ($index % 2 == 1) ? "<div class=\"full_width clear\">" : ""; ?>
    	<div id="msg-<?= $msg['message_id'] ?>" class="one_half <?= ($index % 2 == 1) ? "first" : ""; ?>">
      		<?= $msg['message_content'] ?>
      		<span>Добавено от <?= $msg['message_author'] ?> <?= $func->timeToAgo($msg['message_added']); ?>
      			<?php if($_SESSION['type'] === 1): ?>
      				| <button onclick='deleteMsg(<?= $msg['message_id'] ?>)' style="width: 80px; height: 25px; color: red; font-size: 9pt;">X</button>
      			<?php endif; ?>
      		</span>
      	</div>
    	<?= ($index % 2 == 0 || $index == $sql->rowCount()) ? "</div>" : ""; ?>
    <?php 
    		$index++; 
			}// endforeach;
 		endif; 
 	?>
    <!-- ############################## E N D ################################################ -->
  </div>
</div>
<!-- Footer -->
<div class="wrapper row4">
  <footer id="footer" class="clear">
    <p class="fl_left">Copyright &copy; 2018 - Всички права са запазени - <a href="http://pgevarna.com">ПГЕ - гр. Варна</a></p>
    <p class="fl_right">Код: <a href="http://fb.me/Nicholas.0nLin3.Genchev" title="NGenchev on facebook">Н. Генчев a.k.a. NGenchev</a></p>
  </footer>
</div>
</body>
</html>