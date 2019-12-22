<?php

namespace Tests;


use Model\Entities\User;

/**
 * Class TestCase
 * @package Tests
 */
trait createApplication {
    public function createApplication() {
        require_once __DIR__.'/../../vendor/autoload.php';
        $app = require __DIR__.'/../app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);
        $app['session.test'] = true;
        require __DIR__.'/../../config/dev.php';
        require __DIR__.'/../../src/controllers.php';
        return $app;
    }

    public function setUp() {
        parent::setUp();
    }

    public function loginAsUser1() {
        $this->createClient()->request('POST',
            '/login',
            ['username' => 'user1', 'password' => 'user1']);
    }
}