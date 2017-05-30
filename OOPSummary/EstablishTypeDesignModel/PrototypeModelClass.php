<?php
/**
 * 原型模式
 *
 * 通过创建一个原型对象，然后复制原型对象来避免通过标准的方式创建大量的对象产生的开销
 * 大量的数据对象(比如通过ORM获取1,000,000行数据库记录然后创建每一条记录对应的对象实体)
 */

namespace DesignPatterns\Creational\Prototype{
    abstract class BookPrototype
    {
        /**
         * @var string
         */
        protected $title;

        /**
         * @var string
         */
        protected $category;

        abstract public function __clone();

        public function getTitle(): string
        {
            return $this->title;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }
    }

    class BarBookPrototype extends BookPrototype
    {
        /**
         * @var string
         */
        protected $category = 'Bar';

        public function __clone()
        {
        }
    }

    class FooBookPrototype extends BookPrototype
    {
        /**
         * @var string
         */
        protected $category = 'Foo';

        public function __clone()
        {
        }
    }
}


/**
 * 测试
 */
namespace DesignPatterns\Creational\Prototype\Tests{
    use DesignPatterns\Creational\Prototype\BarBookPrototype;
    use DesignPatterns\Creational\Prototype\FooBookPrototype;
    use PHPUnit\Framework\TestCase;

    class PrototypeTest extends TestCase
    {
        public function testCanGetFooBook()
        {
            $fooPrototype = new FooBookPrototype();
            $barPrototype = new BarBookPrototype();

            for ($i = 0; $i < 10; $i++) {
                $book = clone $fooPrototype;
                $book->setTitle('Foo Book No ' . $i);
                $this->assertInstanceOf(FooBookPrototype::class, $book);
            }

            for ($i = 0; $i < 5; $i++) {
                $book = clone $barPrototype;
                $book->setTitle('Bar Book No ' . $i);
                $this->assertInstanceOf(BarBookPrototype::class, $book);
            }
        }
    }

}


?>