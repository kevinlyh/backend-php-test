<?php
namespace Model;

abstract class Model{
    protected $_tableName;
    protected $pk = 'id';
    protected $_ID = null;
    protected $_columns;
    protected $_modify;
    protected $_DB;
    protected $_classname;

    /**
     * Create a new Model instance.
     *
     * @param  number  $id
     * @return void
     */
    public function __construct($id = null){
        global $app;
        if (isset($app)) {
            $this->_DB = $app['db'];
        } else {
            $app = require __DIR__.'/../app.php';
            $this->_DB = $app['db'];
        }
        $this->_tableName = $this->getTableName();
        if(isset($id)) {
            $this->_ID = $id;
        }
    }

    abstract protected function getTableName();

    private function formWhereClause($conditions) {
        $where = '';
        if (is_array($conditions)) {
            foreach ($conditions as $key => $val) {
                $where .= "`" . $key . "` = '" . $val . "' AND ";
            }
            $where = substr($where, 0, strlen($where) - 5);
        } else {
            $where = $conditions;
        }
        return $where;
    }

    protected function fromQueryResult($result) {
        if ($result === null) return null;
        $model = new $this->_classname($result[$this->pk]);
        foreach ($model->_columns as $key=>$val) {
            $model->$val = $result[$key];
        }
        return $model;
    }

    protected function fromQueryResults($results) {
        if ($results === null || !is_array($results) || count($results) === 0) return null;
        $models = array();
        foreach ($results as $result) {
            $model = $this->fromQueryResult($result);
            array_push($models, $model);
        }
        return $models;
    }

    public function __call($method,$param){
        $type   = substr($method,0,3);
        $member = lcfirst(substr($method,3));
        switch($type){
            case 'get':
                return $this->getMember($member);
                break;
            case 'set':
                return $this->setMember($member,$param[0]);
        }
        return false;
    }

    public function setMember($key,$val){
        if(property_exists($this,$key)){
            $this->$key = $val;
            $this->_modify[$key] = 1;
        }else{
            return false;
        }
    }

    public function getMember($key){
        if(property_exists($this,$key)){
            return $this->$key;
        }
        return false;
    }

    public function get($id) {
        $sql = "SELECT * FROM " . $this->_tableName . " WHERE " . $this->pk . " = " . $id;
        $result = $this->_DB->fetchAssoc($sql);
        return $this->fromQueryResult($result);
    }

    public static function find($id) {
        return (new static)->get($id);
    }

    public function fetchAll($conditions='1=1', $limit = null) {
        $where = $this->formWhereClause($conditions);
        $sql = "SELECT * FROM " . $this->_tableName . " WHERE " . $where;
        if ($limit !== null) {
            $sql .= $limit;
        }
        return $this->fromQueryResults($this->_DB->fetchAll($sql));
    }

    public static function first($conditions='1=1') {
        $results = (new static())->fetchAll($conditions);
        if (count($results) > 0) {
            return $results[0];
        }
        return null;
    }

    public function count($conditions='1=1') {
        $where = $this->formWhereClause($conditions);
        $sql = "SELECT COUNT(*) as cnt FROM " . $this->_tableName . " WHERE " . " " . $where;
        $result = $this->_DB->fetchAssoc($sql);
        return $result['cnt'];
    }

    public static function all($conditions='1=1') {
        return (new static)->fetchAll($conditions);
    }

    public static function paginate($conditions='1=1', $pageNumber = 1, $perPage = 10) {
        $count = (new static)->count($conditions);
        $limit = "ORDER BY id ASC LIMIT " . $perPage . " OFFSET " . ($pageNumber - 1) * $perPage . " ";
        $data = (new static)->fetchAll($conditions, $limit);
        $page = array(
          'count' => $count,
          'pageNumber' => $pageNumber,
          'perPage' => $perPage,
        );
        return array(
            'page' => $page,
            'data' => $data,
        );
    }

    public function save(){
        if(isset($this->_ID)){
            $sql = "UPDATE ".$this->_tableName." SET ";
            $sql_update = '';
            foreach($this->_columns as $col => $varName){
                if(array_key_exists( $varName, $this->_modify)){
                    $sql_update .=  $col . " = '" . $this->getMember($varName) . "',";
                }
            }
            $sql .= substr($sql_update,0,strlen($sql_update) - 1);
            $sql .= ' WHERE ' . $this->pk . ' = ' . $this->_ID;
        }else{
            $sql = "INSERT INTO ".$this->_tableName." (";
            $field = '';
            $values = '';
            foreach($this->_columns as $col => $varName){
                if(array_key_exists($varName, $this->_modify)){
                    $field  .= "`" . $col . "`,";
                    $values .= "'" . $this->getMember($varName) . "',";
                }
            }
            $fields = substr($field,0,strlen($field)-1);
            $values   = substr($values,0,strlen($values)-1);
            $sql .= $fields." ) VALUES (".$values.")";
        }
        $this->_DB->executeUpdate($sql);
        if (!isset($this->_ID)) {
            $this->_ID = $this->_DB->lastInsertId();
            $this->setMember($this->pk, $this->_ID);
        }
    }

    public function delete(){
        if(isset($this->_ID)){
            $sql = "DELETE FROM ".$this->_tableName." WHERE ".$this->pk." = ".$this->_ID;
            $this->_DB->executeUpdate($sql);
        }
    }

    public function toArray() {
        $arr = array();
        foreach($this->_columns as $col => $varName) {
            $arr[$col] = $this->$varName;
        }
        return $arr;
    }
}
