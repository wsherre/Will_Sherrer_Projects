<?php
class dbh{
	private $host = "Wills-Mac.local";
	private $user = "metube";
	private $pwd = "metube_database";
	private $dbName = "metube";

	protected function connect(){
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
		$pdo = new PDO($dsn, $this->user, $this->pwd);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $pdo;
	}

	public function query($sql, $values='None'){
		if ($values == 'None'){
			$stmt = $this->connect()->query($sql);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql;}
			else{
				$array = array();
				while($row = $stmt->fetch()){
					array_push($array, $row);
				}
				return $array;
			}
		}else{
			$stmt = $this->connect()->prepare($sql);
			$stmt->execute($values);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql; print_r($values);}
			else{
				$array = array();
				while($row = $stmt->fetch()){
					array_push($array, $row);
				}
				return $array;
			}
		}
	}

	public function insert($sql, $values='None'){
		if ($values == 'None'){
			$stmt = $this->connect()->query($sql);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql;}
		}else{
			$stmt = $this->connect()->prepare($sql);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql; print_r($values);}
			else{
				$stmt->execute($values);
			}
		}
	}

	public function delete($sql, $values='None'){
		if ($values == 'None'){
			$stmt = $this->connect()->query($sql);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql;}
		}else{
			$stmt = $this->connect()->prepare($sql);
			if(!$stmt){ echo "ERROR WITH SQL STATEMENT: " . $sql; print_r($values);}
			else{
				$stmt->execute($values);
			}
		}
	}
}
?>
