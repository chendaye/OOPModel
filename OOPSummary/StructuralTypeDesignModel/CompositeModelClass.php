<?php
/**
 * 组合模式
 *
 * 以单个对象的方式来对待一组对象
 *
 * 例子
 * form类的实例包含多个子元素，而它也像单个子元素那样响应render()请求，当调用``render()``方法时，它会历遍所有的子元素，调用``render()``方法
 *Zend_Config: 配置选项树, 其每一个分支都是 Zend_Config 对象
 */

namespace DesignPatterns\Structural\Composite{
    interface RenderableInterface
    {
        public function render(): string;
    }

    /**
     * The composite node MUST extend the component contract. This is mandatory for building
     * a tree of components.
     */
    class Form implements RenderableInterface
    {
        /**
         * @var RenderableInterface[]
         */
        private $elements;

        /**
         * runs through all elements and calls render() on them, then returns the complete representation
         * of the form.
         *
         * from the outside, one will not see this and the form will act like a single object instance
         *
         * @return string
         */
        public function render(): string
        {
            $formCode = '<form>';

            foreach ($this->elements as $element) {
                $formCode .= $element->render();
            }

            $formCode .= '</form>';

            return $formCode;
        }

        /**
         * @param RenderableInterface $element
         */
        public function addElement(RenderableInterface $element)
        {
            $this->elements[] = $element;
        }
    }

    class InputElement implements RenderableInterface
    {
        public function render(): string
        {
            return '<input type="text" />';
        }
    }

    class TextElement implements RenderableInterface
    {
        /**
         * @var string
         */
        private $text;

        public function __construct(string $text)
        {
            $this->text = $text;
        }

        public function render(): string
        {
            return $this->text;
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\Composite\Tests{
    use DesignPatterns\Structural\Composite;
    use PHPUnit\Framework\TestCase;

    class CompositeTest extends TestCase
    {
        public function testRender()
        {
            $form = new Composite\Form();
            $form->addElement(new Composite\TextElement('Email:'));
            $form->addElement(new Composite\InputElement());
            $embed = new Composite\Form();
            $embed->addElement(new Composite\TextElement('Password:'));
            $embed->addElement(new Composite\InputElement());
            $form->addElement($embed);

            // This is just an example, in a real world scenario it is important to remember that web browsers do not
            // currently support nested forms

            $this->assertEquals(
                '<form>Email:<input type="text" /><form>Password:<input type="text" /></form></form>',
                $form->render()
            );
        }
    }
}


?>