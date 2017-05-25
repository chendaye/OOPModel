<?php
/**
 * 外观模式
 *
 * 外观模式的目的不是为了让你避免阅读烦人的API文档（当然，它有这样的作用），它的主要目的是为了减少耦合并且遵循得墨忒耳定律（Law of Demeter）
 * Facade通过嵌入多个（当然，有时只有一个）接口来解耦访客与子系统，当然也降低复杂度。
 * Facade 不会禁止你访问子系统
 * 你可以（应该）为一个子系统提供多个 Facade
 * 因此一个好的 Facade 里面不会有 new 。如果每个方法里都要构造多个对象，那么它就不是 Facade，而是生成器或者[抽象|静态|简单] 工厂 [方法]。
 * 优秀的 Facade 不会有 new，并且构造函数参数是接口类型的。如果你需要创建一个新实例，则在参数中传入一个工厂对象
 */

namespace DesignPatterns\Structural\Facade{
    class Facade
    {
        /**
         * @var OsInterface
         */
        private $os;

        /**
         * @var BiosInterface
         */
        private $bios;

        /**
         * @param BiosInterface $bios
         * @param OsInterface   $os
         */
        public function __construct(BiosInterface $bios, OsInterface $os)
        {
            $this->bios = $bios;
            $this->os = $os;
        }

        public function turnOn()
        {
            $this->bios->execute();
            $this->bios->waitForKeyPress();
            $this->bios->launch($this->os);
        }

        public function turnOff()
        {
            $this->os->halt();
            $this->bios->powerDown();
        }
    }

    interface OsInterface
    {
        public function halt();

        public function getName(): string;
    }

    interface BiosInterface
    {
        public function execute();

        public function waitForKeyPress();

        public function launch(OsInterface $os);

        public function powerDown();
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\Facade\Tests{
    use DesignPatterns\Structural\Facade\Facade;
    use DesignPatterns\Structural\Facade\OsInterface;
    use PHPUnit\Framework\TestCase;

    class FacadeTest extends TestCase
    {
        public function testComputerOn()
        {
            /** @var OsInterface|\PHPUnit_Framework_MockObject_MockObject $os */
            $os = $this->createMock('DesignPatterns\Structural\Facade\OsInterface');

            $os->method('getName')
                ->will($this->returnValue('Linux'));

            $bios = $this->getMockBuilder('DesignPatterns\Structural\Facade\BiosInterface')
                ->setMethods(['launch', 'execute', 'waitForKeyPress'])
                ->disableAutoload()
                ->getMock();

            $bios->expects($this->once())
                ->method('launch')
                ->with($os);

            $facade = new Facade($bios, $os);

            // the facade interface is simple
            $facade->turnOn();

            // but you can also access the underlying components
            $this->assertEquals('Linux', $os->getName());
        }
    }
}


?>