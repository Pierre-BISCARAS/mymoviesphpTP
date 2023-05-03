<?php

require_once(dirname(__FILE__).'/../vendor/autoload.php');
use MongoDB\Client;

class myDbClass {
    
    private $servername = '';
    private $port = '';
    private $username= '';
    private $password = '';
    private $dbname = '';

    function __construct()
    {
        require_once(dirname(__FILE__).'/../conf/config.php');

        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__).'/../');
        $dotenv->load();

        $this->servername = $_ENV['MDB_SRV'];
        $this->port = $_ENV['MDB_PORT'];
        $this->username = $_ENV['MDB_USER'];
        $this->password = $_ENV['MDB_PASS'];
        $this->dbname = $_ENV['MDB_DBNAME'];
    }

    public function getClient()
    {
        $uri = 'mongodb://';
        //$uri .= $this->username.':'.$this->password.'@';
        $uri .= $this->servername;
        $uri .= '/'.$this->dbname;
        $client = new MongoDB\Client(
            $uri
        );
        return $client;
    }

    public function getCollections()
    {
        $uri = 'mongodb://';
        //$uri .= $this->username.':'.$this->password.'@';
        $uri .= $this->servername;
        $uri .= '/'.$this->dbname;
        $client = new MongoDB\Client(
            $uri
        );
        $db = $client->selectDatabase($this->dbname);
        return $db->listCollections();
    }

    public function getCollection($name_collection)
    {
        $uri = 'mongodb://';
        //$uri .= $this->username.':'.$this->password.'@';
        $uri .= $this->servername;
        $uri .= '/'.$this->dbname;
        $client = new MongoDB\Client(
            $uri
        );
        $prefix_dbname = $this->dbname;
        return $client->$prefix_dbname->$name_collection;
    }
}