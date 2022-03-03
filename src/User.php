<?php 
/**
 * Cache - A simple php user class for AfriMarketSquare
 * @author      Peter Chigozie(NG) peterujah
 * @copyright   Copyright (c), 2021 Peter(NG) peterujah
 * @license     MIT public license
 */
namespace Peterujah\NanoBlock;
use \Peterujah\NanoBlock\DBController;
class User{
    const GUEST = "_user_guest_class_";
    const LIVE = "_user_live_class_";
    private $index = "login";
    private $tableSelect = "*";
    private $userTable = "user_table_name";
    private $userIdentifier = "user_id";
    protected $db_config = array();
    protected $db;
    protected $conn_handler;
    protected $instanceQuery;
    public function __construct($db, $conf = array()) {
        $this->db = $db;
	$this->setDbConfig($conf);
        $this->setUserQuery("
            SELECT {$this->tableSelect}
            FROM  {$this->userTable} 
            WHERE {$this->userIdentifier} = :check_user_key
            LIMIT 1
        ");
    }
  
    public function setUserQuery($query){
      $this->instanceQuery = $query;
    }
  
    public function add($index, $value){
        $_SESSION[$this->db][$index] = $value;
        return $this;
    }

    public function id(){
        return $this->get("id");
    }

    public function online(){
        return (isset($_SESSION[$this->db]) && !empty($_SESSION[$this->db]));
    }

    public function forceAuthLogin(){
        if(!$this->online()){
            header("Location: {$this->index}"); exit();
        }
    }

    public function arrayData(){
        return $_SESSION[$this->db]??[];
    }

    public function get($index){
        return $_SESSION[$this->db][$index]??null;
    }

    public function remove($index){
        unset($_SESSION[$this->db][$index]);
        return $this;
    }

    public function clear(){
        unset($_SESSION[$this->db]);
        return $this;
    }
	
    public function setDbConfig($conf){
	$this->db_config = $conf;
    	return $this;
    }

    public function conn(){
        if(empty($this->conn_handler) && class_exists('\Peterujah\NanoBlock\DBController') && !empty($this->db_config)){
		 $this->conn_handler = new DBController($this->db_config);
        }else{
		throw new \Exception('\Peterujah\NanoBlock\DBController PDO connection is required, make sure you pass your inatll class first');
	 }
        return $this->conn_handler;
    }
	
    /*public function instant(){
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use instance() instead.', E_USER_DEPRECATED);
        return $this->instance();
    }*/

    public function instance(){
        $this->conn()->prepare($this->instanceQuery);
        $this->conn()->bind(":check_user_key", $this->get("key"));
        $this->conn()->execute();		
        $user = $this->conn()->getOne();
        $this->conn()->free();
        return $user;
    }
}

