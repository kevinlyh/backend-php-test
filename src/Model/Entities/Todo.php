<?php

namespace Model\Entities;

use Model\Model;

class Todo extends Model {
    public $id = null;
    public $userId = null;
    public $description = null;
    public $completed = null;

    protected $_columns = [
        'id' => 'id',
        'user_id' => 'userId',
        'description' => 'description',
        'completed' => 'completed',
    ];

    public function __construct($id = null) {
        parent::__construct($id);
        $this->_classname = get_class();
    }

    protected  function getTableName(){
        return "todos";
    }
}