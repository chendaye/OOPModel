<?php
/**
 * 模板方法模式
 *
 * 模板法是一种行为设计模式。
 * 也许你已经遇到过很多次了。这个想法是让这个抽象模板的子类“完成”一个算法的行为。
 * 又名“好莱坞原则”：“不要给我们打电话，我们打电话给你。”这类不调用子类但逆。如何？抽象的过程。
 * 换句话说，这是一个算法的骨架，非常适合框架库。用户可以执行一个方法和父类做的工作。
 * 这是一个简单的方法来解耦具体类和减少复制粘贴，这就是为什么你会发现它无处不在。
 */

namespace DesignPatterns\Behavioral\TemplateMethod{
    abstract class Journey
    {
        /**
         * @var string[]
         */
        private $thingsToDo = [];

        /**
         * This is the public service provided by this class and its subclasses.
         * Notice it is final to "freeze" the global behavior of algorithm.
         * If you want to override this contract, make an interface with only takeATrip()
         * and subclass it.
         */
        final public function takeATrip()
        {
            $this->thingsToDo[] = $this->buyAFlight();
            $this->thingsToDo[] = $this->takePlane();
            $this->thingsToDo[] = $this->enjoyVacation();
            $buyGift = $this->buyGift();

            if ($buyGift !== null) {
                $this->thingsToDo[] = $buyGift;
            }

            $this->thingsToDo[] = $this->takePlane();
        }

        /**
         * This method must be implemented, this is the key-feature of this pattern.
         */
        abstract protected function enjoyVacation(): string;

        /**
         * This method is also part of the algorithm but it is optional.
         * You can override it only if you need to
         *
         * @return null|string
         */
        protected function buyGift()
        {
            return null;
        }

        private function buyAFlight(): string
        {
            return 'Buy a flight ticket';
        }

        private function takePlane(): string
        {
            return 'Taking the plane';
        }

        /**
         * @return string[]
         */
        public function getThingsToDo(): array
        {
            return $this->thingsToDo;
        }
    }

    class BeachJourney extends Journey
    {
        protected function enjoyVacation(): string
        {
            return "Swimming and sun-bathing";
        }
    }

    class CityJourney extends Journey
    {
        protected function enjoyVacation(): string
        {
            return "Eat, drink, take photos and sleep";
        }

        protected function buyGift(): string
        {
            return "Buy a gift";
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\TemplateMethod\Tests{
    use DesignPatterns\Behavioral\TemplateMethod;
    use PHPUnit\Framework\TestCase;

    class JourneyTest extends TestCase
    {
        public function testCanGetOnVacationOnTheBeach()
        {
            $beachJourney = new TemplateMethod\BeachJourney();
            $beachJourney->takeATrip();

            $this->assertEquals(
                ['Buy a flight ticket', 'Taking the plane', 'Swimming and sun-bathing', 'Taking the plane'],
                $beachJourney->getThingsToDo()
            );
        }

        public function testCanGetOnAJourneyToACity()
        {
            $beachJourney = new TemplateMethod\CityJourney();
            $beachJourney->takeATrip();

            $this->assertEquals(
                [
                    'Buy a flight ticket',
                    'Taking the plane',
                    'Eat, drink, take photos and sleep',
                    'Buy a gift',
                    'Taking the plane'
                ],
                $beachJourney->getThingsToDo()
            );
        }
    }

}



?>