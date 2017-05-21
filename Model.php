<?php
/**
*  ActiveRecordLite by voTkaPoweR
*  url: https://github.com/votkapower/ActiveRecordLite
*  @author: Dimitar Papazov [BG] voTkaPoweR
*/
class Model
{
	private static $host = 'localhost'; //fill this ..
	private static $username = 'root'; //fill this ..
	private static $password = ''; //fill this ..
	private static $dbname = 'database'; //fill this ..
	//------ don't change the code below if you dont understand it -----
	private $props = [];
	public $table = false;
	public $primary_key = 'id';
	public $fillable = [];
	private  $connection = null;

	 function __construct()
	 {

	 		if(is_null($this->connection)) 
	 		{
			 	// Load configuration as an array. Use the actual location of your configuration file
				$this->connection = new mysqli(self::$host, self::$username, self::$password,  self::$dbname);
				$this->connection->set_charset("utf8");

				if(count($this->fillable) < 1)
				{
					$properties_q = $this->connection->query("SHOW COLUMNS FROM ".$this->table." ");	
					while ($r = $properties_q->fetch_object()) 
					{
						 $this->fillable[] = $r->Field;
					}
					unset($properties_q);
				}
			}	
		return $this;
	 }

    public function __destruct() {
      $this->disconnectDb();
    }


    public function disconnectDb() {
      $this->connection->close();
      $this->connection = null;
    }

	 public  function find($id=false)
	 {
	 	   if(!$this->table || !$id) return false;
		   $this->fillable = [];	
			$properties_q = $this->connection->query("SHOW COLUMNS FROM ".$this->table." ");
			while ($r = $properties_q->fetch_object()) 
			{
				 $this->fillable[] = $r->Field;
				 $this->{$r->Field} = null;
			}
			unset($properties_q);

		   $properties = $this->connection->query('SELECT * FROM '.$this->table.' where '.$this->primary_key.'= '.$id)->fetch_object();
		    if(count($properties))
			{
				foreach ($properties as $key => $value) {
					if($this->{$key} == null) $this->{$key} = $value;
				}
			}
			unset($properties);

		return $this;
	 }

	 public  function all()
	 {
	 	   if(!$this->table) return false;
	    	return json_decode(json_encode($this->connection->query('SELECT * FROM '.$this->table)->fetch_all(MYSQLI_ASSOC)));
		
	 }


	 public function save()
	 {
   		if(!$this->table) return false;
		if($this->{$this->primary_key} !== null)
		{
			// update
			$set = '';
			foreach ($this->fillable as $col_title) {
				if($col_title == $this->primary_key) continue;

				if(isset($this->{$col_title}) && mb_strlen(trim(htmlspecialchars($this->{$col_title})))>0)
				{
					$set .=' `'.$col_title.'` = "'.$this->{$col_title} .'",';
				}
			}
			$set = substr($set, 0, -1);
			// die(var_dump('UPDATE '.$this->table . ' SET '.$set.'  WHERE id='.$this->{$this->primary_key}));
			$this->connection->query('UPDATE '.$this->table . ' SET '.$set.'  WHERE '.$this->primary_key.'='.$this->{$this->primary_key});
			return $this->{$this->primary_key};
		}
		else 
		{
			
			$cols = '';
			$vals = '';
			foreach ($this->fillable as $col_title) {
				if($col_title == $this->primary_key) continue;
				if(isset($this->{$col_title}) && mb_strlen(trim(htmlspecialchars($this->{$col_title})))>0)
				{
					$cols .= ' `'.$col_title.'`,';
					$vals .= "'".htmlspecialchars($this->{$col_title}) . "',";
				}
			}
			$cols = substr($cols, 0, -1);
			$vals = substr($vals, 0, -1);
			$this->connection->query("INSERT INTO ".$this->table . " (".$cols.")VALUES(".$vals.")");
			return  $this->connection->insert_id;
		}
		
		return $this;
	 }
	


	 public function delete($id=null)
	 {
		if( $id != null ) $this->{$this->primary_key} = $id;
	 	if($this->table &&  isset($this->{$this->primary_key}) && $this->{$this->primary_key} != null)
	 	{
	 		$this->connection->query("DELETE FROM `".$this->table."` where `".$this->primary_key."`='".$this->{$this->primary_key}."' LIMIT 1");

	 		return true;
	 	}
	 	return false;
	 }

	
	// public function __get($property)
	// {
	// 	return $this->{$property};
	// }
	
}