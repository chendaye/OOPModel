<?php
/**
 * 空对象模式
 *
 * nullobject不是GoF设计模式的一个模式，但经常出现足以被认为是一个模式。它有以下好处：
 * 客户端代码被简化
 * 减少空指针异常的机会
 * 更少的条件需要较少的测试用例
 * 方法返回一个对象或空应该返回一个对象或nullobject。
 * nullobjects简化样板代码（如如果！is_null（$ obj））{ $ obj -> callsomething()；}只是$ obj -> callsomething()；消除客户代码的条件检查。
 */

namespace DesignPatterns\Behavioral\NullObject{
    class Service
    {
        /**
         * @var LoggerInterface
         */
        private $logger;

        /**
         * @param LoggerInterface $logger
         */
        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

        /**
         * do something ...
         */
        public function doSomething()
        {
            // notice here that you don't have to check if the logger is set with eg. is_null(), instead just use it
            $this->logger->log('We are in '.__METHOD__);
        }
    }


    /**
     * Key feature: NullLogger must inherit from this interface like any other loggers
     */
    interface LoggerInterface
    {
        public function log(string $str);
    }

    class PrintLogger implements LoggerInterface
    {
        public function log(string $str)
        {
            echo $str;
        }
    }

    class NullLogger implements LoggerInterface
    {
        public function log(string $str)
        {
            // do nothing
        }
    }
}


/**
 * 测试
 */
namespace DesignPatterns\Behavioral\NullObject\Tests{

    use DesignPatterns\Behavioral\NullObject\NullLogger;
    use DesignPatterns\Behavioral\NullObject\PrintLogger;
    use DesignPatterns\Behavioral\NullObject\Service;
    use PHPUnit\Framework\TestCase;

    class LoggerTest extends TestCase
    {
        public function testNullObject()
        {
            $service = new Service(new NullLogger());
            $this->expectOutputString('');
            $service->doSomething();
        }

        public function testStandardLogger()
        {
            $service = new Service(new PrintLogger());
            $this->expectOutputString('We are in DesignPatterns\Behavioral\NullObject\Service::doSomething');
            $service->doSomething();
        }
    }

}


?>