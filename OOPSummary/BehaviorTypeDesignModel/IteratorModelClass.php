<?php
/**
 * 迭代器模式
 *
 * 使物体的迭代，让它看起来像是一个对象的集合。
 *
 * 通过一个文件（一个对象当然也是一个对象）运行的所有行（其中有一个对象表示）行一行文件处理
 *
 * 标准PHP库（SPL）定义了一个接口迭代器，它最适合！你常常会想实现可数的接口，允许计数（元对象）你可迭代的对象
 */

namespace DesignPatterns\Behavioral\Iterator{
    class Book
    {
        /**
         * @var string
         */
        private $author;

        /**
         * @var string
         */
        private $title;

        public function __construct(string $title, string $author)
        {
            $this->author = $author;
            $this->title = $title;
        }

        public function getAuthor(): string
        {
            return $this->author;
        }

        public function getTitle(): string
        {
            return $this->title;
        }

        public function getAuthorAndTitle(): string
        {
            return $this->getTitle().' by '.$this->getAuthor();
        }
    }

    class BookList implements \Countable, \Iterator
    {
        /**
         * @var Book[]
         */
        private $books = [];

        /**
         * @var int
         */
        private $currentIndex = 0;

        public function addBook(Book $book)
        {
            $this->books[] = $book;
        }

        public function removeBook(Book $bookToRemove)
        {
            foreach ($this->books as $key => $book) {
                if ($book->getAuthorAndTitle() === $bookToRemove->getAuthorAndTitle()) {
                    unset($this->books[$key]);
                }
            }

            $this->books = array_values($this->books);
        }

        public function count(): int
        {
            return count($this->books);
        }

        public function current(): Book
        {
            return $this->books[$this->currentIndex];
        }

        public function key(): int
        {
            return $this->currentIndex;
        }

        public function next()
        {
            $this->currentIndex++;
        }

        public function rewind()
        {
            $this->currentIndex = 0;
        }

        public function valid(): bool
        {
            return isset($this->books[$this->currentIndex]);
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Iterator\Tests{

    use DesignPatterns\Behavioral\Iterator\Book;
    use DesignPatterns\Behavioral\Iterator\BookList;
    use DesignPatterns\Behavioral\Iterator\BookListIterator;
    use DesignPatterns\Behavioral\Iterator\BookListReverseIterator;
    use PHPUnit\Framework\TestCase;

    class IteratorTest extends TestCase
    {
        public function testCanIterateOverBookList()
        {
            $bookList = new BookList();
            $bookList->addBook(new Book('Learning PHP Design Patterns', 'William Sanders'));
            $bookList->addBook(new Book('Professional Php Design Patterns', 'Aaron Saray'));
            $bookList->addBook(new Book('Clean Code', 'Robert C. Martin'));

            $books = [];

            foreach ($bookList as $book) {
                $books[] = $book->getAuthorAndTitle();
            }

            $this->assertEquals(
                [
                    'Learning PHP Design Patterns by William Sanders',
                    'Professional Php Design Patterns by Aaron Saray',
                    'Clean Code by Robert C. Martin',
                ],
                $books
            );
        }

        public function testCanIterateOverBookListAfterRemovingBook()
        {
            $book = new Book('Clean Code', 'Robert C. Martin');
            $book2 = new Book('Professional Php Design Patterns', 'Aaron Saray');

            $bookList = new BookList();
            $bookList->addBook($book);
            $bookList->addBook($book2);
            $bookList->removeBook($book);

            $books = [];
            foreach ($bookList as $book) {
                $books[] = $book->getAuthorAndTitle();
            }

            $this->assertEquals(
                ['Professional Php Design Patterns by Aaron Saray'],
                $books
            );
        }

        public function testCanAddBookToList()
        {
            $book = new Book('Clean Code', 'Robert C. Martin');

            $bookList = new BookList();
            $bookList->addBook($book);

            $this->assertCount(1, $bookList);
        }

        public function testCanRemoveBookFromList()
        {
            $book = new Book('Clean Code', 'Robert C. Martin');

            $bookList = new BookList();
            $bookList->addBook($book);
            $bookList->removeBook($book);

            $this->assertCount(0, $bookList);
        }
    }
}



?>