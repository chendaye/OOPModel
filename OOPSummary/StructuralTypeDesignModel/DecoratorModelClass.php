<?php
/**
 * 装饰器模式
 *
 * 动态地为类的实例添加功能
 *
 * 例子
 * Zend Framework: Zend_Form_Element 实例的装饰器
 *Web Service层：REST服务的JSON与XML装饰器（当然，在此只能使用其中的一种）
 */

namespace DesignPatterns\Structural\Decorator{
    interface RenderableInterface
    {
        public function renderData(): string;
    }

    class Webservice implements RenderableInterface
    {
        /**
         * @var string
         */
        private $data;

        public function __construct(string $data)
        {
            $this->data = $data;
        }

        public function renderData(): string
        {
            return $this->data;
        }
    }

    /**
     * the Decorator MUST implement the RendererInterface contract, this is the key-feature
     * of this design pattern. If not, this is no longer a Decorator but just a dumb
     * wrapper.
     */
    abstract class RendererDecorator
    {
        /**
         * @var RenderableInterface
         */
        protected $wrapped;

        /**
         * @param RenderableInterface $renderer
         */
        public function __construct(RenderableInterface $renderer)
        {
            $this->wrapped = $renderer;
        }
    }

    class XmlRenderer extends RendererDecorator
    {
        public function renderData(): string
        {
            $doc = new \DOMDocument();
            $data = $this->wrapped->renderData();
            $doc->appendChild($doc->createElement('content', $data));

            return $doc->saveXML();
        }
    }

    class JsonRenderer extends RendererDecorator
    {
        public function renderData(): string
        {
            return json_encode($this->wrapped->renderData());
        }
    }
}


/**
 * 测试
 */
namespace DesignPatterns\Structural\Decorator\Tests{

    use DesignPatterns\Structural\Decorator;
    use PHPUnit\Framework\TestCase;

    class DecoratorTest extends TestCase
    {
        /**
         * @var Decorator\Webservice
         */
        private $service;

        protected function setUp()
        {
            $this->service = new Decorator\Webservice('foobar');
        }

        public function testJsonDecorator()
        {
            $service = new Decorator\JsonRenderer($this->service);

            $this->assertEquals('"foobar"', $service->renderData());
        }

        public function testXmlDecorator()
        {
            $service = new Decorator\XmlRenderer($this->service);

            $this->assertXmlStringEqualsXmlString('<?xml version="1.0"?><content>foobar</content>', $service->renderData());
        }
    }
}



?>