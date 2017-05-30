<?php
/**
 * 单例模式
 *
 * 单例模式已经被考虑列入到反模式中！请使用依赖注入获得更好的代码可测试性和可控性！
 * 使应用中只存在一个对象的实例，并且使这个单实例负责所有对该对象的调用。
 * 数据库连接器
 * 日志记录器 （可能有多个实例，比如有多个日志文件因为不同的目的记录不同到的日志）
 * 应用锁文件 （理论上整个应用只有一个锁文件 ...）
 */

namespace DesignPatterns\Creational\Singleton{
    final class Singleton
    {
        /**
         * @var Singleton
         */
        private static $instance;

        /**
         * gets the instance via lazy initialization (created on first usage)
         */
        public static function getInstance(): Singleton
        {
            if (null === static::$instance) {
                static::$instance = new static();
            }

            return static::$instance;
        }

        /**
         * is not allowed to call from outside to prevent from creating multiple instances,
         * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
         */
        private function __construct()
        {
        }

        /**
         * prevent the instance from being cloned (which would create a second instance of it)
         */
        private function __clone()
        {
        }

        /**
         * prevent from being unserialized (which would create a second instance of it)
         */
        private function __wakeup()
        {
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Creational\Singleton\Tests{
    use DesignPatterns\Creational\Singleton\Singleton;
    use PHPUnit\Framework\TestCase;

    class SingletonTest extends TestCase
    {
        public function testUniqueness()
        {
            $firstCall = Singleton::getInstance();
            $secondCall = Singleton::getInstance();

            $this->assertInstanceOf(Singleton::class, $firstCall);
            $this->assertSame($firstCall, $secondCall);
        }
    }
}




?>