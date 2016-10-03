<?php

	class Database {

		private $usuario = 'grupo_76';
		private $pass ='WKcMXebcLdXFpvdQ';
		private $db = 'grupo_76';
		private $host = 'localhost';

		private $dbhandler;  
		private $error;
		private $statement;
		

		public function __construct() {

			//seteamos el DB Source Name a MySQL
			$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db;
			$opciones = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        	);
			//creamos una instancia de PDO
			try {
				$this->dbhandler = new PDO($dsn, $this->usuario, $this->pass, $opciones);	
			}
			catch (PDOException $e) {
				echo ('Fallo conexion a la base de datos: '.$e->getMessage());
			}
		}
		
		public function query($query){
    		$this->statement = $this->dbhandler->prepare($query);
		}

		public function bind($param, $value, $type = null){
		    if (is_null($type)) {
		        switch (true) {
		            case is_int($value):
		                $type = PDO::PARAM_INT;
		                break;
		            case is_bool($value):
		                $type = PDO::PARAM_BOOL;
		                break;
		            case is_null($value):
		                $type = PDO::PARAM_NULL;
		                break;
		            default:
		                $type = PDO::PARAM_STR;
		        }
		    }
		    $this->statement->bindValue($param, $value, $type);
		}

		
		public function ejecutar() {
  			return $this->statement->execute();
		}

		//retorna un array de las filas resultantes de la consulta	
		public function resultado() {
   			$this->ejecutar();
  			return $this->statement->fetchAll(PDO::FETCH_ASSOC);    //PDO::FETCH_ASSOC retorna las filas como un arreglo asociativo con los nombres de los campos como claves
		}

		public function columna($nro) {
			$this->ejecutar();
			return $this->statement->fetchColumn($nro);
		}

		public function filas() {
  			$this->ejecutar();
			return $this->statement->rowCount();
		}
		
		public function lastInsertId(){
			return $this->dbhandler->lastInsertId();
		}
		

	}
?>