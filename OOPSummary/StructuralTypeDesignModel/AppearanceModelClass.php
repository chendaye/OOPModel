<?php
/**
 * 外观模式
 *
 * 外观模式的目的不是为了让你避免阅读烦人的API文档（当然，它有这样的作用），
 * 它的主要目的是为了减少耦合并且遵循得墨忒耳定律（Law of Demeter）
 * Facade通过嵌入多个（当然，有时只有一个）接口来解耦访客与子系统，当然也降低复杂度。
 * Facade 不会禁止你访问子系统
 * 你可以（应该）为一个子系统提供多个 Facade
 * 因此一个好的 Facade 里面不会有 new 。如果每个方法里都要构造多个对象，那么它就不是 Facade，
 * 而是生成器或者[抽象|静态|简单] 工厂 [方法]。
 * 优秀的 Facade 不会有 new，并且构造函数参数是接口类型的。如果你需要创建一个新实例，则在参数中传入一个工厂对象
 *
 *
 * 门面模式
 * 为子系统中的一组接口提供一个一致的界面，Facade模式定义了一个高层次的接口，使得子系统更加容易使用【GOF95】
 * 外部与子系统的通信是通过一个门面(Facade)对象进行。
 * 【门面模式中主要角色】
门面(Facade)角色：
此角色将被客户端调用
知道哪些子系统负责处理请求
将用户的请求指派给适当的子系统

子系统(subsystem)角色：
实现子系统的功能
处理由Facade对象指派的任务
没有Facade的相关信息，可以被客户端直接调用
可以同时有一个或多个子系统，每个子系统都不是一个单独的类，而一个类的集合。每个子系统都可以被客户端直接调用，或者被门面角色调用。子系统并知道门面模式的存在，对于子系统而言，门面仅仅是另一个客户端。

【门面模式的优点】
1、它对客户屏蔽了子系统组件，因而减少了客户处理的对象的数目并使得子系统使用起来更加方便
2、实现了子系统与客户之间的松耦合关系
3、如果应用需要，它并不限制它们使用子系统类。因此可以在系统易用性与能用性之间加以选择

【门面模式适用场景】
1、为一些复杂的子系统提供一组接口
2、提高子系统的独立性
3、在层次化结构中，可以使用门面模式定义系统的每一层的接口

【门面模式与其它模式】
抽象工厂模式(abstract factory模式)：Abstract Factory模式可以与Facade模式一起使用以提供一个接口，这一接口可用来以一种子系统独立的方式创建子系统对象。Abstract Factory模式也可以代替Facade模式隐藏那些与平台相关的类
调停者模式：Mediator模式与Facade模式的相似之处是，它抽象了一些已有类的功能。然而，Mediator目的是对同事之间的任意通讯进行抽象，通常集中不属于任何单个对象的功能。Mediator的同事对象知道中介者并与它通信，而不是直接与其他同类对象通信。相对而言，Facade模式仅对子系统对象的接口进行抽象，从而使它们更容易使用；它并定义不功能，子系统也不知道facade的存在
单例模式(singleton模式)：一般来说，仅需要一个Facade对象，因此Facade对象通常属于Singleton对象。
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