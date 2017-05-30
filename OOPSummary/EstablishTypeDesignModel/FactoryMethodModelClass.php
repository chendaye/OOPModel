<?php
/**
 * 工厂模式
 *
 *
 * 比简单工厂模式好的一点是工厂方法可以通过继承实现不同的创建对象的逻辑。
 * 举个简单的例子，这些抽象类都仅仅是一个接口
 * 这个模式是一个 “真正” 的设计模式，因为它遵循了依赖反转原则（Dependency Inversion Principle） 众所周知这个 “D” 代表了真正的面向对象程序设计。
 * 它意味着工厂方法类依赖于类的抽象，而不是具体将被创建的类，这是工厂方法模式与简单工厂模式和静态工厂模式最重要的区别
 */

namespace DesignPatterns\Creational\FactoryMethod{
    abstract class FactoryMethod
    {
        const CHEAP = 'cheap';
        const FAST = 'fast';

        abstract protected function createVehicle(string $type): VehicleInterface;

        public function create(string $type): VehicleInterface
        {
            $obj = $this->createVehicle($type);
            $obj->setColor('black');

            return $obj;
        }
    }

    class ItalianFactory extends FactoryMethod
    {
        protected function createVehicle(string $type): VehicleInterface
        {
            switch ($type) {
                case parent::CHEAP:
                    return new Bicycle();
                case parent::FAST:
                    return new CarFerrari();
                default:
                    throw new \InvalidArgumentException("$type is not a valid vehicle");
            }
        }
    }

    class GermanFactory extends FactoryMethod
    {
        protected function createVehicle(string $type): VehicleInterface
        {
            switch ($type) {
                case parent::CHEAP:
                    return new Bicycle();
                case parent::FAST:
                    $carMercedes = new CarMercedes();
                    // we can specialize the way we want some concrete Vehicle since we know the class
                    $carMercedes->addAMGTuning();

                    return $carMercedes;
                default:
                    throw new \InvalidArgumentException("$type is not a valid vehicle");
            }
        }
    }

    interface VehicleInterface
    {
        public function setColor(string $rgb);
    }

    class CarMercedes implements VehicleInterface
    {
        /**
         * @var string
         */
        private $color;

        public function setColor(string $rgb)
        {
            $this->color = $rgb;
        }

        public function addAMGTuning()
        {
            // do additional tuning here
        }
    }

    class CarFerrari implements VehicleInterface
    {
        /**
         * @var string
         */
        private $color;

        public function setColor(string $rgb)
        {
            $this->color = $rgb;
        }
    }

    class Bicycle implements VehicleInterface
    {
        /**
         * @var string
         */
        private $color;

        public function setColor(string $rgb)
        {
            $this->color = $rgb;
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Creational\FactoryMethod\Tests{
    use DesignPatterns\Creational\FactoryMethod\Bicycle;
    use DesignPatterns\Creational\FactoryMethod\CarFerrari;
    use DesignPatterns\Creational\FactoryMethod\CarMercedes;
    use DesignPatterns\Creational\FactoryMethod\FactoryMethod;
    use DesignPatterns\Creational\FactoryMethod\GermanFactory;
    use DesignPatterns\Creational\FactoryMethod\ItalianFactory;
    use PHPUnit\Framework\TestCase;

    class FactoryMethodTest extends TestCase
    {
        public function testCanCreateCheapVehicleInGermany()
        {
            $factory = new GermanFactory();
            $result = $factory->create(FactoryMethod::CHEAP);

            $this->assertInstanceOf(Bicycle::class, $result);
        }

        public function testCanCreateFastVehicleInGermany()
        {
            $factory = new GermanFactory();
            $result = $factory->create(FactoryMethod::FAST);

            $this->assertInstanceOf(CarMercedes::class, $result);
        }

        public function testCanCreateCheapVehicleInItaly()
        {
            $factory = new ItalianFactory();
            $result = $factory->create(FactoryMethod::CHEAP);

            $this->assertInstanceOf(Bicycle::class, $result);
        }

        public function testCanCreateFastVehicleInItaly()
        {
            $factory = new ItalianFactory();
            $result = $factory->create(FactoryMethod::FAST);

            $this->assertInstanceOf(CarFerrari::class, $result);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @expectedExceptionMessage spaceship is not a valid vehicle
         */
        public function testUnknownType()
        {
            (new ItalianFactory())->create('spaceship');
        }
    }

}



?>