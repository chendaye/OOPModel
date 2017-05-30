<?php
/**
 * 规范模式
 *
 * 建立一个明确的业务规则的规范，对象可以检查。复合规范类有一个方法叫做issatisfiedby返回true或false取决于给定的对象满足规范要求。
 */

namespace DesignPatterns\Behavioral\Specification{
    class Item
    {
        /**
         * @var float
         */
        private $price;

        public function __construct(float $price)
        {
            $this->price = $price;
        }

        public function getPrice(): float
        {
            return $this->price;
        }
    }

    interface SpecificationInterface
    {
        public function isSatisfiedBy(Item $item): bool;
    }

    class OrSpecification implements SpecificationInterface
    {
        /**
         * @var SpecificationInterface[]
         */
        private $specifications;

        /**
         * @param SpecificationInterface[] ...$specifications
         */
        public function __construct(SpecificationInterface ...$specifications)
        {
            $this->specifications = $specifications;
        }

        /**
         * if at least one specification is true, return true, else return false
         */
        public function isSatisfiedBy(Item $item): bool
        {
            foreach ($this->specifications as $specification) {
                if ($specification->isSatisfiedBy($item)) {
                    return true;
                }
            }
            return false;
        }
    }

    class PriceSpecification implements SpecificationInterface
    {
        /**
         * @var float|null
         */
        private $maxPrice;

        /**
         * @var float|null
         */
        private $minPrice;

        /**
         * @param float $minPrice
         * @param float $maxPrice
         */
        public function __construct($minPrice, $maxPrice)
        {
            $this->minPrice = $minPrice;
            $this->maxPrice = $maxPrice;
        }

        public function isSatisfiedBy(Item $item): bool
        {
            if ($this->maxPrice !== null && $item->getPrice() > $this->maxPrice) {
                return false;
            }

            if ($this->minPrice !== null && $item->getPrice() < $this->minPrice) {
                return false;
            }

            return true;
        }
    }

    class AndSpecification implements SpecificationInterface
    {
        /**
         * @var SpecificationInterface[]
         */
        private $specifications;

        /**
         * @param SpecificationInterface[] ...$specifications
         */
        public function __construct(SpecificationInterface ...$specifications)
        {
            $this->specifications = $specifications;
        }

        /**
         * if at least one specification is false, return false, else return true.
         */
        public function isSatisfiedBy(Item $item): bool
        {
            foreach ($this->specifications as $specification) {
                if (!$specification->isSatisfiedBy($item)) {
                    return false;
                }
            }

            return true;
        }
    }

    class NotSpecification implements SpecificationInterface
    {
        /**
         * @var SpecificationInterface
         */
        private $specification;

        public function __construct(SpecificationInterface $specification)
        {
            $this->specification = $specification;
        }

        public function isSatisfiedBy(Item $item): bool
        {
            return !$this->specification->isSatisfiedBy($item);
        }
    }
}


/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Specification\Tests{
    use DesignPatterns\Behavioral\Specification\Item;
    use DesignPatterns\Behavioral\Specification\NotSpecification;
    use DesignPatterns\Behavioral\Specification\OrSpecification;
    use DesignPatterns\Behavioral\Specification\AndSpecification;
    use DesignPatterns\Behavioral\Specification\PriceSpecification;
    use PHPUnit\Framework\TestCase;

    class SpecificationTest extends TestCase
    {
        public function testCanOr()
        {
            $spec1 = new PriceSpecification(50, 99);
            $spec2 = new PriceSpecification(101, 200);

            $orSpec = new OrSpecification($spec1, $spec2);

            $this->assertFalse($orSpec->isSatisfiedBy(new Item(100)));
            $this->assertTrue($orSpec->isSatisfiedBy(new Item(51)));
            $this->assertTrue($orSpec->isSatisfiedBy(new Item(150)));
        }

        public function testCanAnd()
        {
            $spec1 = new PriceSpecification(50, 100);
            $spec2 = new PriceSpecification(80, 200);

            $andSpec = new AndSpecification($spec1, $spec2);

            $this->assertFalse($andSpec->isSatisfiedBy(new Item(150)));
            $this->assertFalse($andSpec->isSatisfiedBy(new Item(1)));
            $this->assertFalse($andSpec->isSatisfiedBy(new Item(51)));
            $this->assertTrue($andSpec->isSatisfiedBy(new Item(100)));
        }

        public function testCanNot()
        {
            $spec1 = new PriceSpecification(50, 100);
            $notSpec = new NotSpecification($spec1);

            $this->assertTrue($notSpec->isSatisfiedBy(new Item(150)));
            $this->assertFalse($notSpec->isSatisfiedBy(new Item(50)));
        }
    }
}




?>