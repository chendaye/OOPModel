<?php
/**
 * 中介模式
 *
 * 这种模式提供了一个简单的方法来去耦许多组件一起工作。这是一个很好的替代观察员，如果你有一个“中央情报”，像一个控制器（但不是在意义上的MVC）。
 * 所有的组件（称为同事）只能连接到mediatorinterface和这是一件好事，因为在面向对象的程序设计，一个好朋友胜过许多。这是这种模式的主要特征。
 */

namespace DesignPatterns\Behavioral\Mediator{
    /**
     * MediatorInterface is a contract for the Mediator
     * This interface is not mandatory but it is better for Liskov substitution principle concerns.
     */
    interface MediatorInterface
    {
        /**
         * sends the response.
         *
         * @param string $content
         */
        public function sendResponse($content);

        /**
         * makes a request
         */
        public function makeRequest();

        /**
         * queries the DB
         */
        public function queryDb();
    }

    /**
     * Mediator is the concrete Mediator for this design pattern
     *
     * In this example, I have made a "Hello World" with the Mediator Pattern
     */
    class Mediator implements MediatorInterface
    {
        /**
         * @var Subsystem\Server
         */
        private $server;

        /**
         * @var Subsystem\Database
         */
        private $database;

        /**
         * @var Subsystem\Client
         */
        private $client;

        /**
         * @param Subsystem\Database $database
         * @param Subsystem\Client $client
         * @param Subsystem\Server $server
         */
        public function __construct(Subsystem\Database $database, Subsystem\Client $client, Subsystem\Server $server)
        {
            $this->database = $database;
            $this->server = $server;
            $this->client = $client;

            $this->database->setMediator($this);
            $this->server->setMediator($this);
            $this->client->setMediator($this);
        }

        public function makeRequest()
        {
            $this->server->process();
        }

        public function queryDb(): string
        {
            return $this->database->getData();
        }

        /**
         * @param string $content
         */
        public function sendResponse($content)
        {
            $this->client->output($content);
        }
    }

    /**
     * Colleague is an abstract colleague who works together but he only knows
     * the Mediator, not other colleagues
     */
    abstract class Colleague
    {
        /**
         * this ensures no change in subclasses.
         *
         * @var MediatorInterface
         */
        protected $mediator;

        /**
         * @param MediatorInterface $mediator
         */
        public function setMediator(MediatorInterface $mediator)
        {
            $this->mediator = $mediator;
        }
    }

    /**
     * Client is a client that makes requests and gets the response.
     */
    class Client extends Colleague
    {
        public function request()
        {
            $this->mediator->makeRequest();
        }

        public function output(string $content)
        {
            echo $content;
        }
    }

    class Database extends Colleague
    {
        public function getData(): string
        {
            return 'World';
        }
    }

    class Server extends Colleague
    {
        public function process()
        {
            $data = $this->mediator->queryDb();
            $this->mediator->sendResponse(sprintf("Hello %s", $data));
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Tests\Mediator\Tests{

    use DesignPatterns\Behavioral\Mediator\Mediator;
    use DesignPatterns\Behavioral\Mediator\Subsystem\Client;
    use DesignPatterns\Behavioral\Mediator\Subsystem\Database;
    use DesignPatterns\Behavioral\Mediator\Subsystem\Server;
    use PHPUnit\Framework\TestCase;

    class MediatorTest extends TestCase
    {
        public function testOutputHelloWorld()
        {
            $client = new Client();
            new Mediator(new Database(), $client, new Server());

            $this->expectOutputString('Hello World');
            $client->request();
        }
    }
}



?>