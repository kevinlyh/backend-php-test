<?php
namespace Tests;

use Model\Entities\Todo;
use Model\Entities\User;

class TodoModelTest extends WebBaseCase {
    use createApplication;

    public function testCreateTodo() {
        $user = User::first('1=1');

        $attrs = array(
            'userId' => $user->id,
            'description' => 'todo model unit test',
        );
        $countBeforeCreate = count(Todo::all());
        $todo = Todo::create($attrs);
        $countAfterCreate = count(Todo::all());

        $this->assertObjectHasAttribute('id', $todo);
        $this->assertEquals($user->id, $todo->userId);
        $this->assertEquals('todo model unit test', $todo->description);
        $this->assertGreaterThan(1, $todo->id);
        $this->assertEquals($countAfterCreate, $countBeforeCreate + 1);
    }

    public function testUpdateTodo() {
        $todo = Todo::first();
        $orginDescr = $todo->description;
        $newDescr = $orginDescr . ' update model test';
        $todo->setDescription($newDescr);
        $todo->save();
        $latestTodo = Todo::first();

        $this->assertEquals($newDescr, $latestTodo->description);
        $this->assertEquals($todo->description, $latestTodo->description);
        $this->assertNotEquals($orginDescr, $todo->description);
        $todo->setDescription($orginDescr);
        $todo->save();
        $this->assertNotEquals($newDescr, $todo->description);
    }
}
