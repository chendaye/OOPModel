<?php
/**
 * 轻量模式
 *
 * 为了减少内存的使用，一个轻量级的股份作为相似的对象可能的记忆一样。
 * 这是需要时，大量的对象使用，不太多的状态。一个常见的做法是保持状态的外部数据结构传递给flyweight对象在需要的时候。
 */

namespace DesignPatterns\Structural\Flyweight{
    interface FlyweightInterface
    {
        public function render(string $extrinsicState): string;
    }

    /**
     * Implements the flyweight interface and adds storage for intrinsic state, if any.
     * Instances of concrete flyweights are shared by means of a factory.
     */
    class CharacterFlyweight implements FlyweightInterface
    {
        /**
         * Any state stored by the concrete flyweight must be independent of its context.
         * For flyweights representing characters, this is usually the corresponding character code.
         *
         * @var string
         */
        private $name;

        public function __construct(string $name)
        {
            $this->name = $name;
        }

        public function render(string $font): string
        {
            // Clients supply the context-dependent information that the flyweight needs to draw itself
            // For flyweights representing characters, extrinsic state usually contains e.g. the font.

            return sprintf('Character %s with font %s', $this->name, $font);
        }
    }

    /**
     * A factory manages shared flyweights. Clients should not instantiate them directly,
     * but let the factory take care of returning existing objects or creating new ones.
     */
    class FlyweightFactory implements \Countable
    {
        /**
         * @var CharacterFlyweight[]
         */
        private $pool = [];

        public function get(string $name): CharacterFlyweight
        {
            if (!isset($this->pool[$name])) {
                $this->pool[$name] = new CharacterFlyweight($name);
            }

            return $this->pool[$name];
        }

        public function count(): int
        {
            return count($this->pool);
        }
    }
}


namespace DesignPatterns\Structural\Flyweight\Tests{

    use DesignPatterns\Structural\Flyweight\FlyweightFactory;
    use PHPUnit\Framework\TestCase;

    class FlyweightTest extends TestCase
    {
        private $characters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        private $fonts = ['Arial', 'Times New Roman', 'Verdana', 'Helvetica'];

        public function testFlyweight()
        {
            $factory = new FlyweightFactory();

            foreach ($this->characters as $char) {
                foreach ($this->fonts as $font) {
                    $flyweight = $factory->get($char);
                    $rendered = $flyweight->render($font);

                    $this->assertEquals(sprintf('Character %s with font %s', $char, $font), $rendered);
                }
            }

            // Flyweight pattern ensures that instances are shared
            // instead of having hundreds of thousands of individual objects
            // there must be one instance for every char that has been reused for displaying in different fonts
            $this->assertCount(count($this->characters), $factory);
        }
    }
}

?>