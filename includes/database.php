<?php
/**
	protected $dbh;
	
	function __construct(){
		$this->dbh = (new DBConnection())->pdo_conn();
	}
 */
class DBConnection
{
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;
	private $db_char;
	private $dsn;
	protected $dbh;

	function __construct($host = 'localhost', $user = 'root', $pass = '', $name = 'messages', $char = 'utf8')
	{
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_name = $name;
		$this->db_char = $char;
	}
	
	public function pdo_conn()
	{
		$this->dsn = "mysql:host=". $this->db_host .";dbname=". $this->db_name .";charset=". $this->db_char;
		$opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		
		try
		{
			$this->dbh = new PDO($this->dsn, $this->db_user, $this->db_pass, $opt);
		}
		catch(PDOException $e) 
		{
			print "Проблем със свързването към БД!: " . $e->getMessage() . "<br/>";
			die();
		}
		
		return $this->dbh;
	}
	
	function __destruct()
	{
		$this->dbh = NULL;
	}
}