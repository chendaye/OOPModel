<?php
/**
 * 简单工厂
 *
 * 简单的创建对象型工厂模式
 * It differs from the static factory because it is not static. Therefore, you can have multiple factories,
 * differently parametrized, you can subclass it and you can mock it. It always should be preferred over a static factory!
 */

namespace DesignPatterns\Creational\SimpleFactory{
    class SimpleFactory
    {
        public function createBicycle(): Bicycle
        {
            return new Bicycle();
        }
    }

    class Bicycle
    {
        public function driveTo(string $destination)
        {
        }
    }

    $factory = new SimpleFactory();
    $bicycle = $factory->createBicycle();
    $bicycle->driveTo('Paris');
}


/**
 * 测试
 */
namespace DesignPatterns\Creational\SimpleFactory\Tests{
    use DesignPatterns\Creational\SimpleFactory\Bicycle;
    use DesignPatterns\Creational\SimpleFactory\SimpleFactory;
    use PHPUnit\Framework\TestCase;

    class SimpleFactoryTest extends TestCase
    {
        public function testCanCreateBicycle()
        {
            $bicycle = (new SimpleFactory())->createBicycle();
            $this->assertInstanceOf(Bicycle::class, $bicycle);
        }
    }

}


?>