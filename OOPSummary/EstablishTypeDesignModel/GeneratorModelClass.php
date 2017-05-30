<?php
/**
 * 生成器模式
 *
 * 生成器的目的是将复杂对象的创建过程（流程）进行抽象，生成器表现为接口的形式。
 * 在特定的情况下，比如如果生成器对将要创建的对象有足够多的了解，那么代表生成器的接口(interface)可以是一个抽象类(也就是说可以有一定的具体实现，就像众所周知的适配器模式)。
 * 如果对象有复杂的继承树，理论上创建对象的生成器也同样具有复杂的继承树。
 * 提示：生成器通常具有流畅的接口，推荐阅读关于 PHPUnit 的mock生成器获取更好的理解。
 *
 * 例子
 * PHPUnit: Mock 生成器
 */

namespace DesignPatterns\Creational\Builder{
    use DesignPatterns\Creational\Builder\Parts\Vehicle;

    /**
     * Director is part of the builder pattern. It knows the interface of the builder
     * and builds a complex object with the help of the builder
     *
     * You can also inject many builders instead of one to build more complex objects
     */
    class Director
    {
        public function build(BuilderInterface $builder): Vehicle
        {
            $builder->createVehicle();
            $builder->addDoors();
            $builder->addEngine();
            $builder->addWheel();

            return $builder->getVehicle();
        }
    }


    interface BuilderInterface
    {
        public function createVehicle();

        public function addWheel();

        public function addEngine();

        public function addDoors();

        public function getVehicle(): Vehicle;
    }

    class TruckBuilder implements BuilderInterface
    {
        /**
         * @var Parts\Truck
         */
        private $truck;

        public function addDoors()
        {
            $this->truck->setPart('rightDoor', new Parts\Door());
            $this->truck->setPart('leftDoor', new Parts\Door());
        }

        public function addEngine()
        {
            $this->truck->setPart('truckEngine', new Parts\Engine());
        }

        public function addWheel()
        {
            $this->truck->setPart('wheel1', new Parts\Wheel());
            $this->truck->setPart('wheel2', new Parts\Wheel());
            $this->truck->setPart('wheel3', new Parts\Wheel());
            $this->truck->setPart('wheel4', new Parts\Wheel());
            $this->truck->setPart('wheel5', new Parts\Wheel());
            $this->truck->setPart('wheel6', new Parts\Wheel());
        }

        public function createVehicle()
        {
            $this->truck = new Parts\Truck();
        }

        public function getVehicle(): Vehicle
        {
            return $this->truck;
        }
    }

    class CarBuilder implements BuilderInterface
    {
        /**
         * @var Parts\Car
         */
        private $car;

        public function addDoors()
        {
            $this->car->setPart('rightDoor', new Parts\Door());
            $this->car->setPart('leftDoor', new Parts\Door());
            $this->car->setPart('trunkLid', new Parts\Door());
        }

        public function addEngine()
        {
            $this->car->setPart('engine', new Parts\Engine());
        }

        public function addWheel()
        {
            $this->car->setPart('wheelLF', new Parts\Wheel());
            $this->car->setPart('wheelRF', new Parts\Wheel());
            $this->car->setPart('wheelLR', new Parts\Wheel());
            $this->car->setPart('wheelRR', new Parts\Wheel());
        }

        public function createVehicle()
        {
            $this->car = new Parts\Car();
        }

        public function getVehicle(): Vehicle
        {
            return $this->car;
        }
    }

}

namespace DesignPatterns\Creational\Builder\Parts{
    abstract class Vehicle
    {
        /**
         * @var object[]
         */
        private $data = [];

        /**
         * @param string $key
         * @param object $value
         */
        public function setPart($key, $value)
        {
            $this->data[$key] = $value;
        }
    }

    class Truck extends Vehicle
    {
    }

    class Car extends Vehicle
    {
    }

    class Engine
    {
    }

    class Wheel
    {
    }
    class Door
    {
    }
}

namespace DesignPatterns\Creational\Builder\Tests{
    use DesignPatterns\Creational\Builder\Parts\Car;
    use DesignPatterns\Creational\Builder\Parts\Truck;
    use DesignPatterns\Creational\Builder\TruckBuilder;
    use DesignPatterns\Creational\Builder\CarBuilder;
    use DesignPatterns\Creational\Builder\Director;
    use PHPUnit\Framework\TestCase;

    class DirectorTest extends TestCase
    {
        public function testCanBuildTruck()
        {
            $truckBuilder = new TruckBuilder();
            $newVehicle = (new Director())->build($truckBuilder);

            $this->assertInstanceOf(Truck::class, $newVehicle);
        }

        public function testCanBuildCar()
        {
            $carBuilder = new CarBuilder();
            $newVehicle = (new Director())->build($carBuilder);

            $this->assertInstanceOf(Car::class, $newVehicle);
        }
    }
}







?>