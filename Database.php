<?php
	session_start();

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
date_default_timezone_set("Asia/Bangkok");
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
		if ($stmt->rowCount() > 1)
			{
			if ($userRow['password'] === $upassword)
				{
					$_SESSION['userLogin'] =  $userRow['usercode'];
				echo " Login SuccessFull ";
				}
			}
		}



	}

class Crud
{
	public function getRows($table,$conditions = array()){
        $sql = 'SELECT ';
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
        $sql .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions['where'] as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        
        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by']; 
        }
        
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit']; 
        }
		global $dbcon;
        $query = $dbcon->pdo->prepare($sql);
        $query->execute();
        
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data = $query->rowCount();
                    break;
                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data = '';
            }
        }else{
            if($query->rowCount() > 0){
                $data = $query->fetchAll();
            }
        }
        return !empty($data)?$data:false;
	}
	


    public function delete($table,$conditions){
        $whereSql = '';
        if(!empty($conditions) && is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
		$sql = "DELETE FROM ".$table.$whereSql;
		global $dbcon;
		$delete = $dbcon->pdo->prepare($sql);
		$delete->execute();
        return $delete?$delete:false;
    }

    public function update($table,$data,$conditions){
        if(!empty($data) && is_array($data)){
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $colvalSet .= $pre.$key."='".$val."'";
                $i++;
            }
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
			$sql = "UPDATE ".$table." SET ".$colvalSet.$whereSql;
			global $dbcon;
            $query = $dbcon->pdo->prepare($sql);
            $update = $query->execute();
            return $update?$query->rowCount():false;
        }else{
            return false;
        }
    }

	public function insert($table,$data){
        if(!empty($data) && is_array($data)){
            $columns = '';
            $values  = '';
            $i = 0;
            if(!array_key_exists('created',$data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }

            $columnString = implode(',', array_keys($data));
            $valueString = ":".implode(',:', array_keys($data));
            $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
			global $dbcon;
            $query = $dbcon->pdo->prepare($sql);
            foreach($data as $key=>$val){
                 $query->bindValue(':'.$key, $val);
            }
            $insert = $query->execute();
            return $insert?$dbcon->pdo->lastInsertId():false;
        }else{
            return false;
        }
    }


}


// $reg = new User();

// $reg-> registration("gsmashik","5645656gg","oleeahmmed@gmail.com","user");

// if (isset($notification))
// 	{
// 	echo $notification;
// 	}

// if ($reg->checkUserExist('olee', 'ashiks.mou.909@gmail.com', "user") == false)
// 	{
// 	$reg->registration("olee", "5645656ggdgd", "ashiks.mou.909@gmail.com", "user");
// 	}
//   else
// 	{
// 	$notification = " User Already Exist ";
// 	}

// if (isset($notification))
// 	{
// 	echo $notification;
// 	}

// 	$reg->login("gsmashik","5645656gg","user");
// if (isset($_SESSION['userLogin'])) {
// 	echo $_SESSION['userLogin'];
// }