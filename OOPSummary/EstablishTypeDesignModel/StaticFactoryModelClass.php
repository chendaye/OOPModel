<?php
/**
 * 静态工厂
 *
 * 和抽象工厂类似，静态工厂模式用来创建一系列互相关联或依赖的对象，
 * 和抽象工厂模式不同的是静态工厂模式只用一个静态方法就解决了所有类型的对象创建，通常被命名为``工厂`` 或者 构建器
 *
 * Zend Framework 框架中的: Zend_Cache_Backend 和 _Frontend 使用了静态工厂设计模式 创建后端缓存或者前端缓存对象
 */

namespace DesignPatterns\Creational\StaticFactory{
    /**
     * Note1: Remember, static means global state which is evil because it can't be mocked for tests
     * Note2: Cannot be subclassed or mock-upped or have multiple different instances.
     */
    final class StaticFactory
    {
        /**
         * @param string $type
         *
         * @return FormatterInterface
         */
        public static function factory(string $type): FormatterInterface
        {
            if ($type == 'number') {
                return new FormatNumber();
            }

            if ($type == 'string') {
                return new FormatString();
            }

            throw new \InvalidArgumentException('Unknown format given');
        }
    }

    interface FormatterInterface
    {
    }

    class FormatString implements FormatterInterface
    {
    }

    class FormatNumber implements FormatterInterface
    {
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Creational\StaticFactory\Tests{
    use DesignPatterns\Creational\StaticFactory\StaticFactory;
    use PHPUnit\Framework\TestCase;

    class StaticFactoryTest extends TestCase
    {
        public function testCanCreateNumberFormatter()
        {
            $this->assertInstanceOf(
                'DesignPatterns\Creational\StaticFactory\FormatNumber',
                StaticFactory::factory('number')
            );
        }

        public function testCanCreateStringFormatter()
        {
            $this->assertInstanceOf(
                'DesignPatterns\Creational\StaticFactory\FormatString',
                StaticFactory::factory('string')
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testException()
        {
            StaticFactory::factory('object');
        }
    }
}



?>