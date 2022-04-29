<?php
class CONNECTION_FACTORY {

	protected static $connection;

	public function __construct(){
		if (!isset(self::$connection)) {
			$config = parse_ini_file(__DIR__ ."/../dbconnect/_{$_SERVER["SERVER_NAME"]}.ini");
			self::$connection = adoNewConnection($config['Type']);
			self::$connection->setCharset('utf8');
			self::$connection->connect($config['Host'],$config['User'],$config['Password'],$config['Database']);
			self::$connection->setFetchMode(ADODB_FETCH_ASSOC);
		}
		if (self::$connection === false) {
			exit("Erro ao connectar ao banco de dados");
		}
	}

	public static function instance(){
		return new self();
	}

	public function getConnection() {
		return self::$connection;
	}
}

class ENTITY extends CONNECTION_FACTORY {

	protected $entity;

	public function __construct($entity){
		parent::__construct();
		$this->entity = $entity;
	}

	public static function instance($entity){
		return new self($entity);
	}

	public function select(){
		return self::$connection->execute("SELECT * FROM {$this->entity} ORDER BY 1");
	}

	public function insert($data){
		$fields = implode(",",array_keys($data));
		$binds = preg_replace( "/\w+/i", "?", $fields);
		self::$connection->execute("INSERT INTO {$this->entity} ($fields) VALUES ($binds)", array_values($data) );
		return self;
	}

	public function getID(){
		return self::$connection->Insert_ID();
	}

	public function delete($id){
		self::$connection->execute("DELETE FROM {$this->entity} WHERE id = ?", Array( $id ) );
		return self;
	}

	public function update($data,$id){
		$binds = preg_replace( array('/(\w+)/'), array('\1 = ?'), implode(",",array_keys($data)));
		$values = array_values($data);
		array_push($values, $id);
		self::$connection->execute("UPDATE {$this->entity} SET $binds WHERE id = ?", $values);
		return self;
	}

}

class CONN {
	public static function get(){
		return CONNECTION_FACTORY::instance()->getConnection();
	}
}
?>
