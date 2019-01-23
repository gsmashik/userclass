<?php

// ////////////////////////////////////////////////////////////

$host = "localhost"; //  Server Name Of Host Location Where Your Database You Can Use Ip Address Like 127.0.0.1  .
$db = "test"; // Put Here Your Database Name Where You Store And Receive Information .
$user = "root"; //Your Server Login User Name
$pass = "usbw"; // Server Login Password by Default "" for Local Pc
$db_type;
class Database

	{
	public $charset = "utf8mb4";

 // Which Unicode Use To Received And Store Data Opreation utf8mb4m give More Freture than utf8

	public $port = 3306;

 // port number of server

	public $pdo;

	public $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => FALSE, PDO::ATTR_PERSISTENT => true, ];

	public

	function __construct($db_type, $host, $db, $user, $pass)
		{
		if (!isset($this->pdo))
			{
			if ($db_type == "mysql")
				{
				$dsn = "mysql:host=$host;dbname=$db;charset={$this->charset};{$this->charset}";
				try
					{
					$this->pdo = new PDO($dsn, $user, $pass, $this->options);
					echo "connected";
					}

				catch(PDOEXCEPTION $e)
					{
					echo $e->getMessage();
					}
				}
			elseif ($db_type == "pgsql")
				{
				$dsn = "pgsql:host=$host;dbname=$db;charset={$this->charset};{$this->charset}";
				try
					{
					$this->pdo = new PDO($dsn, $user, $pass, $this->options);

					// $dsn = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";

					echo "connected";
					}

				catch(PDOEXCEPTION $e)
					{
					echo $e->getMessage();
					}
				}
			elseif ($db_type == "sql")
				{
				$dsn = "sqlsrv:server=$host;Database=$db;";
				try
					{
					$this->pdo = new PDO($dsn, $user, $pass);

					// PDO( "sqlsrv:server=$serverName ; Database=AdventureWorks", "", "");

					echo "connected";
					}

				catch(PDOEXCEPTION $e)
					{
					echo $e->getMessage();
					}
				}
			}
		}
	}

$dbcon = new Database("mysql", "localhost", "test", "root", "usbw");

// To Use This Connection First  global $dbv; then use     $stmt = $dbv->pdo->prepare( $sql);

$notification = "";
class User

	{
	public $userExist = false;

	public

	function registration($userCode, $userPass, $email, $table)
		{
		$userid = "";
		$userpassword = "";
		$useremail = "";
		$userid = trim(htmlspecialchars($userCode)); // Escape Html Code  And Remove Space From Start And End
		$userpassword = md5(trim(htmlspecialchars($userPass))); // Escape Html Code  And Remove Space From Start And End
		$useremail = trim(htmlspecialchars($email)); // Escape Html Code  And Remove Space From Start And End
		global $dbcon;
		$sql = " INSERT INTO {$table} (usercode,email,password) VALUES (:userid,:email,:password) ";
		$stmt = $dbcon->pdo->prepare($sql);
		$stmt->bindparam(":userid", $userid);
		$stmt->bindparam(":email", $useremail);
		$stmt->bindparam(":password", $userpassword);
		$stmt->execute();
		global $notification;
		$notification = " Registration Successfull";
		}

	public

	function checkUserExist($userCode, $email, $table)
		{
		global $dbcon;
		$sql = " SELECT * FROM {$table} WHERE  usercode='{$userCode}' AND email ='{$email}' ";
		$stmt = $dbcon->pdo->prepare($sql);
		$stmt->execute();
		if ($stmt->rowCount() > 0)
			{
			return true;
			}
		}

	public

	function login($userCode, $upass, $table)
		{
		global $dbcon;
		$upassword = md5(trim(htmlspecialchars($upass))); // Escape Html Code  And Remove Space From Start And End
		$sql = " SELECT * FROM {$table} WHERE usercode =:userid ";
		$stmt = $dbcon->pdo->prepare($sql);
		$stmt->bindparam(":userid", $userCode);

		// $stmt->bindparam(":userpassword",$upassword );

		$stmt->execute();
		$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() == 1)
			{
			if ($userRow['password'] === $upassword)
				{
				echo " Login SuccessFull ";
				}
			}
		}
	}

$reg = new User();

// $reg-> registration("gsmashik","5645656gg","oleeahmmed@gmail.com","user");

if (isset($notification))
	{
	echo $notification;
	}

if ($reg->checkUserExist('olee', 'ashiks.mou.909@gmail.com', "user") == false)
	{
	$reg->registration("olee", "5645656ggdgd", "ashiks.mou.909@gmail.com", "user");
	}
  else
	{
	$notification = " User Already Exist ";
	}

if (isset($notification))
	{
	echo $notification;
	}