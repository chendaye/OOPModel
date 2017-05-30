<?php
/**
 * 抽象工厂模式
 *
 * 创建一系列互相关联或依赖的对象时不需要指定将要创建的对象对应的类，因为这些将被创建的对象对应的类都实现了同一个接口。
 * 抽象工厂的使用者不需要关心对象的创建过程，它只需要知道这些对象是如何协调工作的。
 */

namespace DesignPatterns\Creational\AbstractFactory{
    /**
     * In this case, the abstract factory is a contract for creating some components
     * for the web. There are two ways of rendering text: HTML and JSON
     */
    abstract class AbstractFactory
    {
        abstract public function createText(string $content): Text;
    }

    class JsonFactory extends AbstractFactory
    {
        public function createText(string $content): Text
        {
            return new JsonText($content);
        }
    }

    class HtmlFactory extends AbstractFactory
    {
        public function createText(string $content): Text
        {
            return new HtmlText($content);
        }
    }

    abstract class Text
    {
        /**
         * @var string
         */
        private $text;

        public function __construct(string $text)
        {
            $this->text = $text;
        }
    }


    class JsonText extends Text
    {
        // do something here
    }

    class HtmlText extends Text
    {
        // do something here
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Creational\AbstractFactory\Tests{
    use DesignPatterns\Creational\AbstractFactory\HtmlFactory;
    use DesignPatterns\Creational\AbstractFactory\HtmlText;
    use DesignPatterns\Creational\AbstractFactory\JsonFactory;
    use DesignPatterns\Creational\AbstractFactory\JsonText;
    use PHPUnit\Framework\TestCase;

    class AbstractFactoryTest extends TestCase
    {
        public function testCanCreateHtmlText()
        {
            $factory = new HtmlFactory();
            $text = $factory->createText('foobar');

            $this->assertInstanceOf(HtmlText::class, $text);
        }

        public function testCanCreateJsonText()
        {
            $factory = new JsonFactory();
            $text = $factory->createText('foobar');

            $this->assertInstanceOf(JsonText::class, $text);
        }
    }

}



?>