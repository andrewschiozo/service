<?php

namespace dao\Sistema;

use util\Config;
use model\Sistema\Model;

abstract class DB extends \PDO implements \JsonSerializable
{
    public    $conn;
    protected $table;
    protected $view;
    protected Model $model;
    protected $dao;

    protected $blackList = ['conn', 'table', 'view', 'model', 'dao', 'blackList'];

    public function __construct($config = Config::SYSDB)
    {
        try
        {
            $this->conn = new \PDO($config['dsn'], $config['user'], $config['pass']);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        }
        catch(\PDOException $e)
        {
            echo ($e->getmessage());
        }
        
    }

    protected function getConn()
    {
        return $this->conn;
    }

    public function __get(string $attr)
    {
        if(property_exists($this, $attr))
        {
            return ($attr == 'created_at' || $attr == 'updated_by') ? 
                    \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.v', $this->$attr) :
                    $this->$attr;
        }
    }

    public function jsonSerialize()
	{
		$obj = new \stdClass();
        
        foreach(get_object_vars($this) as $attr => $value)
        {
            if(!in_array($attr, $this->blackList))
            {
                $obj->$attr = $value;
            }
        }
		return $obj;
	}

    public function get($id = null)
    {
        $queryString = 'SELECT * FROM ' . $this->table . ' WHERE 1 = 1 ';
        $queryString .= $id ? ' AND id = ' . $id : '';
        
        $result = [];
        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->dao);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        return $result;
    }
}