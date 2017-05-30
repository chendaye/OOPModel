<?php
/**
 * 对象池模式
 *
 *
 * 对象池设计模式 是创建型设计模式，它会对新创建的对象应用一系列的初始化操作，让对象保持立即可使用的状态 - 一个存放对象的 “池子” - 而不是对对象进行一次性的的使用(创建并使用，完成之后立即销毁)。
 * 对象池的使用者会对对象池发起请求，以期望获取一个对象，并使用获取到的对象进行一系列操作，
 * 当使用者对对象的使用完成之后，使用者会将由对象池的对象创建工厂创建的对象返回给对象池，而不是用完之后销毁获取到的对象。
 * 对象池在某些情况下会带来重要的性能提升，比如耗费资源的对象初始化操作，实例化类的代价很高，
 * 但每次实例化的数量较少的情况下。对象池中将被创建的对象会在真正被使用时被提前创建，
 * 避免在使用时让使用者浪费对象创建所需的大量时间(比如在对象某些操作需要访问网络资源的情况下)从池子中取得对象的时间是可预测的，但新建一个实例所需的时间是不确定。
 * 总之，对象池会为你节省宝贵的程序执行时间，比如像数据库连接，socket连接，大量耗费资源的代表数字资源的对象，像字体或者位图。
 * 不过，在特定情况下，简单的对象创建池(没有请求外部的资源，仅仅将自身保存在内存中)或许并不会提升效率和性能，这时候，就需要使用者酌情考虑了。
 */

namespace DesignPatterns\Creational\Pool{
    class WorkerPool implements \Countable
    {
        /**
         * @var StringReverseWorker[]
         */
        private $occupiedWorkers = [];

        /**
         * @var StringReverseWorker[]
         */
        private $freeWorkers = [];

        public function get(): StringReverseWorker
        {
            if (count($this->freeWorkers) == 0) {
                $worker = new StringReverseWorker();
            } else {
                $worker = array_pop($this->freeWorkers);
            }

            $this->occupiedWorkers[spl_object_hash($worker)] = $worker;

            return $worker;
        }

        public function dispose(StringReverseWorker $worker)
        {
            $key = spl_object_hash($worker);

            if (isset($this->occupiedWorkers[$key])) {
                unset($this->occupiedWorkers[$key]);
                $this->freeWorkers[$key] = $worker;
            }
        }

        public function count(): int
        {
            return count($this->occupiedWorkers) + count($this->freeWorkers);
        }
    }

    class StringReverseWorker
    {
        /**
         * @var \DateTime
         */
        private $createdAt;

        public function __construct()
        {
            $this->createdAt = new \DateTime();
        }

        public function run(string $text)
        {
            return strrev($text);
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Creational\Pool\Tests{
    use DesignPatterns\Creational\Pool\WorkerPool;
    use PHPUnit\Framework\TestCase;

    class PoolTest extends TestCase
    {
        public function testCanGetNewInstancesWithGet()
        {
            $pool = new WorkerPool();
            $worker1 = $pool->get();
            $worker2 = $pool->get();

            $this->assertCount(2, $pool);
            $this->assertNotSame($worker1, $worker2);
        }

        public function testCanGetSameInstanceTwiceWhenDisposingItFirst()
        {
            $pool = new WorkerPool();
            $worker1 = $pool->get();
            $pool->dispose($worker1);
            $worker2 = $pool->get();

            $this->assertCount(1, $pool);
            $this->assertSame($worker1, $worker2);
        }
    }
}




?>