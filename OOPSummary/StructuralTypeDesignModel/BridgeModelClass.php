<?
/**
 * 桥接模式
 *
 * 解耦一个对象的实现与抽象，这样两者可以独立地变化
 *
 * 例子
 */

namespace DesignPatterns\Structural\Bridge{
    interface FormatterInterface
    {
        public function format(string $text);
    }

    class PlainTextFormatter implements FormatterInterface
    {
        public function format(string $text)
        {
            return $text;
        }
    }

    class HtmlFormatter implements FormatterInterface
    {
        public function format(string $text)
        {
            return sprintf('<p>%s</p>', $text);
        }
    }

    abstract class Service
    {
        /**
         * @var FormatterInterface
         */
        protected $implementation;

        /**
         * @param FormatterInterface $printer
         */
        public function __construct(FormatterInterface $printer)
        {
            $this->implementation = $printer;
        }

        /**
         * @param FormatterInterface $printer
         */
        public function setImplementation(FormatterInterface $printer)
        {
            $this->implementation = $printer;
        }

        abstract public function get();
    }

    class HelloWorldService extends Service
    {
        public function get()
        {
            return $this->implementation->format('Hello World');
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\Bridge\Tests{
    use DesignPatterns\Structural\Bridge\HelloWorldService;
    use DesignPatterns\Structural\Bridge\HtmlFormatter;
    use DesignPatterns\Structural\Bridge\PlainTextFormatter;
    use PHPUnit\Framework\TestCase;

    class BridgeTest extends TestCase
    {
        public function testCanPrintUsingThePlainTextPrinter()
        {
            $service = new HelloWorldService(new PlainTextFormatter());
            $this->assertEquals('Hello World', $service->get());

            // now change the implementation and use the HtmlFormatter instead
            $service->setImplementation(new HtmlFormatter());
            $this->assertEquals('<p>Hello World</p>', $service->get());
        }
    }
}




?>