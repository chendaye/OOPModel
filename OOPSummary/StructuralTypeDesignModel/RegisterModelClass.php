<?php
/**
 * 注册模式
 *
 * 为了实现在应用程序中经常使用的对象的中央存储，通常只使用静态方法（或使用单件模式）使用抽象类来实现。
 * 请记住，这引入了全局状态，这在任何时候都应该避免！使用依赖注入来实现它！
 *
 * 例子
 * Zend Framework 1: Zend_Registry 持有应用的logger对象，前端控制器等。
 * Yii 框架: CWebApplication 持有所有的应用组件，如 CWebUser, CUrlManager, 等。
 */

namespace DesignPatterns\Structural\Registry{
    abstract class Registry
    {
        const LOGGER = 'logger';

        /**
         * this introduces global state in your application which can not be mocked up for testing
         * and is therefor considered an anti-pattern! Use dependency injection instead!
         *
         * @var array
         */
        private static $storedValues = [];

        /**
         * @var array
         */
        private static $allowedKeys = [
            self::LOGGER,
        ];

        /**
         * @param string $key
         * @param mixed  $value
         *
         * @return void
         */
        public static function set(string $key, $value)
        {
            if (!in_array($key, self::$allowedKeys)) {
                throw new \InvalidArgumentException('Invalid key given');
            }

            self::$storedValues[$key] = $value;
        }

        /**
         * @param string $key
         *
         * @return mixed
         */
        public static function get(string $key)
        {
            if (!in_array($key, self::$allowedKeys) || !isset(self::$storedValues[$key])) {
                throw new \InvalidArgumentException('Invalid key given');
            }

            return self::$storedValues[$key];
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\Registry\Tests{
    use DesignPatterns\Structural\Registry\Registry;
    use stdClass;
    use PHPUnit\Framework\TestCase;

    class RegistryTest extends TestCase
    {
        public function testSetAndGetLogger()
        {
            $key = Registry::LOGGER;
            $logger = new stdClass();

            Registry::set($key, $logger);
            $storedLogger = Registry::get($key);

            $this->assertSame($logger, $storedLogger);
            $this->assertInstanceOf(stdClass::class, $storedLogger);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testThrowsExceptionWhenTryingToSetInvalidKey()
        {
            Registry::set('foobar', new stdClass());
        }

        /**
         * notice @runInSeparateProcess here: without it, a previous test might have set it already and
         * testing would not be possible. That's why you should implement Dependency Injection where an
         * injected class may easily be replaced by a mockup
         *
         * @runInSeparateProcess
         * @expectedException \InvalidArgumentException
         */
        public function testThrowsExceptionWhenTryingToGetNotSetKey()
        {
            Registry::get(Registry::LOGGER);
        }
    }

}


?>