<?php
/**
 * 策略模式
 *
 * 分离策略，使它们之间快速切换。此外，这种模式是一个很好的替代继承（而不是有一个抽象类扩展）。
 */

namespace DesignPatterns\Behavioral\Strategy{
    class ObjectCollection
    {
        /**
         * @var array
         */
        private $elements;

        /**
         * @var ComparatorInterface
         */
        private $comparator;

        /**
         * @param array $elements
         */
        public function __construct(array $elements = [])
        {
            $this->elements = $elements;
        }

        public function sort(): array
        {
            if (!$this->comparator) {
                throw new \LogicException('Comparator is not set');
            }

            uasort($this->elements, [$this->comparator, 'compare']);

            return $this->elements;
        }

        /**
         * @param ComparatorInterface $comparator
         */
        public function setComparator(ComparatorInterface $comparator)
        {
            $this->comparator = $comparator;
        }
    }

    interface ComparatorInterface
    {
        /**
         * @param mixed $a
         * @param mixed $b
         *
         * @return int
         */
        public function compare($a, $b): int;
    }

    class DateComparator implements ComparatorInterface
    {
        /**
         * @param mixed $a
         * @param mixed $b
         *
         * @return int
         */
        public function compare($a, $b): int
        {
            $aDate = new \DateTime($a['date']);
            $bDate = new \DateTime($b['date']);

            return $aDate <=> $bDate;
        }
    }

    class IdComparator implements ComparatorInterface
    {
        /**
         * @param mixed $a
         * @param mixed $b
         *
         * @return int
         */
        public function compare($a, $b): int
        {
            return $a['id'] <=> $b['id'];
        }
    }
}


/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Strategy\Tests{
    use DesignPatterns\Behavioral\Strategy\DateComparator;
    use DesignPatterns\Behavioral\Strategy\IdComparator;
    use DesignPatterns\Behavioral\Strategy\ObjectCollection;
    use PHPUnit\Framework\TestCase;

    class StrategyTest extends TestCase
    {
        public function provideIntegers()
        {
            return [
                [
                    [['id' => 2], ['id' => 1], ['id' => 3]],
                    ['id' => 1],
                ],
                [
                    [['id' => 3], ['id' => 2], ['id' => 1]],
                    ['id' => 1],
                ],
            ];
        }

        public function provideDates()
        {
            return [
                [
                    [['date' => '2014-03-03'], ['date' => '2015-03-02'], ['date' => '2013-03-01']],
                    ['date' => '2013-03-01'],
                ],
                [
                    [['date' => '2014-02-03'], ['date' => '2013-02-01'], ['date' => '2015-02-02']],
                    ['date' => '2013-02-01'],
                ],
            ];
        }

        /**
         * @dataProvider provideIntegers
         *
         * @param array $collection
         * @param array $expected
         */
        public function testIdComparator($collection, $expected)
        {
            $obj = new ObjectCollection($collection);
            $obj->setComparator(new IdComparator());
            $elements = $obj->sort();

            $firstElement = array_shift($elements);
            $this->assertEquals($expected, $firstElement);
        }

        /**
         * @dataProvider provideDates
         *
         * @param array $collection
         * @param array $expected
         */
        public function testDateComparator($collection, $expected)
        {
            $obj = new ObjectCollection($collection);
            $obj->setComparator(new DateComparator());
            $elements = $obj->sort();

            $firstElement = array_shift($elements);
            $this->assertEquals($expected, $firstElement);
        }
    }
}




?>