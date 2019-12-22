<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
abstract class WebBaseCase extends TestCase
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * PHPUnit setUp for setting up the application.
     *
     * Note: Child classes that define a setUp method must call
     * parent::setUp().
     */
    public function setUp()
    {
        $this->app = $this->createApplication();
    }

    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    abstract public function createApplication();

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     *
     * @return Client A Client instance
     */
    public function createClient(array $server = array())
    {
        return new Client($this->app, $server);
    }
}