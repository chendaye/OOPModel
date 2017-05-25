<?php
/**
 * 适配器模式
 *
 * 将某个类的接口转换成与另一个接口兼容。适配器通过将原始接口进行转换，给用户提供一个兼容接口，使得原来因为接口不同而无法一起使用的类可以得到兼容
 *
 * 例子
 * 数据库客户端库适配器
 *使用不同的webservices，通过适配器来标准化输出数据，从而保证不同webservice输出的数据是一致的
 */
namespace DesignPatterns\Structural\Adapter{
    /**
     * Interface BookInterface
     * @package DesignPatterns\Structural\Adapter
     */
    interface BookInterface
    {
        public function turnPage();

        public function open();

        public function getPage(): int;
    }

    /**
     * Class Book
     * @package DesignPatterns\Structural\Adapter
     */
    class Book implements BookInterface
    {
        /**
         * @var int
         */
        private $page;

        public function open()
        {
            $this->page = 1;
        }

        public function turnPage()
        {
            $this->page++;
        }

        public function getPage()
        {
            return $this->page;
        }
    }

        /**
         * This is the adapter here. Notice it implements BookInterface,
         * therefore you don't have to change the code of the client which is using a Book
         */
    class EBookAdapter implements BookInterface
    {
        /**
         * @var EBookInterface
         */
        protected $eBook;

        /**
         * @param EBookInterface $eBook
         */
        public function __construct(EBookInterface $eBook)
        {
            $this->eBook = $eBook;
        }

        /**
         * This class makes the proper translation from one interface to another.
         */
        public function open()
        {
            $this->eBook->unlock();
        }

        public function turnPage()
        {
            $this->eBook->pressNext();
        }

        /**
         * notice the adapted behavior here: EBookInterface::getPage() will return two integers, but BookInterface
         * supports only a current page getter, so we adapt the behavior here
         *
         * @return int
         */
        public function getPage(): int
        {
            return $this->eBook->getPage()[0];
        }
    }

    interface EBookInterface
    {
        public function unlock();

        public function pressNext();

        /**
         * returns current page and total number of pages, like [10, 100] is page 10 of 100
         *
         * @return int[]
         */
        public function getPage(): array;
    }

    /**
     * this is the adapted class. In production code, this could be a class from another package, some vendor code.
     * Notice that it uses another naming scheme and the implementation does something similar but in another way
     */
    class Kindle implements EBookInterface
    {
        /**
         * @var int
         */
        private $page = 1;

        /**
         * @var int
         */
        private $totalPages = 100;

        public function pressNext()
        {
            $this->page++;
        }

        public function unlock()
        {
        }

        /**
         * returns current page and total number of pages, like [10, 100] is page 10 of 100
         *
         * @return int[]
         */
        public function getPage(): array
        {
            return [$this->page, $this->totalPages];
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\Adapter\Tests{
    use DesignPatterns\Structural\Adapter\Book;
    use DesignPatterns\Structural\Adapter\EBookAdapter;
    use DesignPatterns\Structural\Adapter\Kindle;
    use PHPUnit\Framework\TestCase;

    class AdapterTest extends TestCase
    {
        public function testCanTurnPageOnBook()
        {
            $book = new Book();
            $book->open();
            $book->turnPage();

            $this->assertEquals(2, $book->getPage());
        }

        public function testCanTurnPageOnKindleLikeInANormalBook()
        {
            $kindle = new Kindle();
            $book = new EBookAdapter($kindle);

            $book->open();
            $book->turnPage();

            $this->assertEquals(2, $book->getPage());
        }
    }
}






?>