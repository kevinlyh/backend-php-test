<?php
namespace Tests;

class ExampleControllerTest extends WebBaseCase {
    use createApplication;

    public function testPostLogin() {
        $client = $this->createClient();
        $client->request('POST',
            '/login',
            ['username' => 'user1', 'password' => 'user1']);
        $this->assertEquals($client->getResponse()->getStatusCode(), 302);
        $this->contains('Redirecting to /todo');
    }

    public function testGetTodo() {
        $this->loginAsUser1();
        $client = $this->createClient();
        $crawler = $client->request('GET',
            '/todo');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->contains('#UserDescription');
        $this->assertGreaterThan(1, count($crawler->filter('form')));
    }
}