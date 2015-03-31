<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
    exit();
}


class Connection {

    private $connection;

    function __construct() {  
    }


   /* function connect() {      
        include_once dirname(__FILE__) . '/../conf.php';
		//establish the connection with the database server
		 $this->connection = mysql_connect(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD) ;
		//connect to the database
		 $blnConnected = mysql_select_db (MYSQL_DB_NAME, $this->connection) or die("Unable to connect to database" . mysql_error());
		    if ($blnConnected==false) {
		        echo "Unable to connect to database " .  mysql_error();
		    }
        return $this->connection;
    }*/


	function connect() {   
        include_once dirname(__FILE__) . '/../conf.php';
		//establish the connection with the database server
		$this->connection = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DB_NAME);
	
		 //$this->connection = mysql_connect(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD) ;
		//connect to the database
		 /*$blnConnected = mysql_select_db (MYSQL_DB_NAME, $this->connection) or die("Unable to connect to database" . mysql_error());
		    if ($blnConnected==false) {
		        echo "Unable to connect to database " .  mysql_error();
		    }*/
        return $this->connection;
    }

}



?>
