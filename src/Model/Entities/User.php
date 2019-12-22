<?php

namespace Model\Entities;

use Model\Model;

class User extends Model {
    public $id = null;
    public $username = null;
    public $password = null;

    protected $_columns = [
        'id' => 'id',
        'username' => 'username',
        'password' => 'password'
    ];

    public function __construct($id = null) {
        parent::__construct($id);
        $this->_classname = get_class();
    }

    protected  function getTableName(){
        return "users";
    }
}