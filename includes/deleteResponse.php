<?php 
require_once("database.php");

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') :
	
	$dbh = (new DBConnection())->pdo_conn();
	extract($_POST); //$num 
	
	$sql = "DELETE FROM messages WHERE message_id = '$num' LIMIT 1";
	$deleteNew = $dbh->prepare($sql);
	if($deleteNew->execute())
		$message = "Успешно изтрихте съобщението!";
	else
		$message = "Проблем с изтриването на съобщението #$num";
	
	echo json_encode($message);
		
endif;