<?php
/**
 * 依赖注入模式
 *
 * 实现了松耦合的软件架构，可得到更好的测试，管理和扩展的代码
 *
 * 例子
 * Doctrine2 ORM 使用了依赖注入，它通过配置注入了 Connection 对象。为了达到方便测试的目的，可以很容易的通过配置创建一个mock的``Connection`` 对象。
 * Symfony 和 Zend Framework 2 也有了专门的依赖注入容器，用来通过配置数据创建需要的对象(比如在控制器中使用依赖注入容器获取所需的对象)
 */

namespace DesignPatterns\Structural\DependencyInjection{
    class DatabaseConfiguration
    {
        /**
         * @var string
         */
        private $host;

        /**
         * @var int
         */
        private $port;

        /**
         * @var string
         */
        private $username;

        /**
         * @var string
         */
        private $password;

        public function __construct(string $host, int $port, string $username, string $password)
        {
            $this->host = $host;
            $this->port = $port;
            $this->username = $username;
            $this->password = $password;
        }

        public function getHost(): string
        {
            return $this->host;
        }

        public function getPort(): int
        {
            return $this->port;
        }

        public function getUsername(): string
        {
            return $this->username;
        }

        public function getPassword(): string
        {
            return $this->password;
        }
    }

    class DatabaseConnection
    {
        /**
         * @var DatabaseConfiguration
         */
        private $configuration;

        /**
         * @param DatabaseConfiguration $config
         */
        public function __construct(DatabaseConfiguration $config)
        {
            $this->configuration = $config;
        }

        public function getDsn(): string
        {
            // this is just for the sake of demonstration, not a real DSN
            // notice that only the injected config is used here, so there is
            // a real separation of concerns here

            return sprintf(
                '%s:%s@%s:%d',
                $this->configuration->getUsername(),
                $this->configuration->getPassword(),
                $this->configuration->getHost(),
                $this->configuration->getPort()
            );
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\DependencyInjection\Tests{
    use DesignPatterns\Structural\DependencyInjection\DatabaseConfiguration;
    use DesignPatterns\Structural\DependencyInjection\DatabaseConnection;
    use PHPUnit\Framework\TestCase;

    class DependencyInjectionTest extends TestCase
    {
        public function testDependencyInjection()
        {
            $config = new DatabaseConfiguration('localhost', 3306, 'domnikl', '1234');
            $connection = new DatabaseConnection($config);

            $this->assertEquals('domnikl:1234@localhost:3306', $connection->getDsn());
        }
    }
}




?>