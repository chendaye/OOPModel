<?php
/**
 * 责任链模式
 *
 * 建立一个对象链以按顺序处理呼叫。如果一个对象不能处理一个调用，那么它会调用链中的下一个调用等等。

 * 日志框架，其中每个链元素自主决定如何处理日志消息
 * 一个垃圾邮件过滤器
 * 缓存：第一个对象是一个memcached接口的一个实例，如果“思念”，它代表调用数据库接口
 * Yii框架：cfilterchain是一连串的动作控制器过滤器。执行点是从一个过滤器传递到另一个沿链，只有当所有的过滤器说“是”，行动可以调用最后。
 */

namespace DesignPatterns\Behavioral\ChainOfResponsibilities{

    use Psr\Http\Message\RequestInterface;
    use Psr\Http\Message\ResponseInterface;

    abstract class Handler
    {
        /**
         * @var Handler|null
         */
        private $successor = null;

        public function __construct(Handler $handler = null)
        {
            $this->successor = $handler;
        }

        /**
         * This approach by using a template method pattern ensures you that
         * each subclass will not forget to call the successor
         *
         * @param RequestInterface $request
         *
         * @return string|null
         */
        final public function handle(RequestInterface $request)
        {
            $processed = $this->processing($request);

            if ($processed === null) {
                // the request has not been processed by this handler => see the next
                if ($this->successor !== null) {
                    $processed = $this->successor->handle($request);
                }
            }

            return $processed;
        }

        abstract protected function processing(RequestInterface $request);
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\ChainOfResponsibilities\Tests{
    use DesignPatterns\Behavioral\ChainOfResponsibilities\Handler;
    use DesignPatterns\Behavioral\ChainOfResponsibilities\Responsible\HttpInMemoryCacheHandler;
    use DesignPatterns\Behavioral\ChainOfResponsibilities\Responsible\SlowDatabaseHandler;
    use PHPUnit\Framework\TestCase;

    class ChainTest extends TestCase
    {
        /**
         * @var Handler
         */
        private $chain;

        protected function setUp()
        {
            $this->chain = new HttpInMemoryCacheHandler(
                ['/foo/bar?index=1' => 'Hello In Memory!'],
                new SlowDatabaseHandler()
            );
        }

        public function testCanRequestKeyInFastStorage()
        {
            $uri = $this->createMock('Psr\Http\Message\UriInterface');
            $uri->method('getPath')->willReturn('/foo/bar');
            $uri->method('getQuery')->willReturn('index=1');

            $request = $this->createMock('Psr\Http\Message\RequestInterface');
            $request->method('getMethod')
                ->willReturn('GET');
            $request->method('getUri')->willReturn($uri);

            $this->assertEquals('Hello In Memory!', $this->chain->handle($request));
        }

        public function testCanRequestKeyInSlowStorage()
        {
            $uri = $this->createMock('Psr\Http\Message\UriInterface');
            $uri->method('getPath')->willReturn('/foo/baz');
            $uri->method('getQuery')->willReturn('');

            $request = $this->createMock('Psr\Http\Message\RequestInterface');
            $request->method('getMethod')
                ->willReturn('GET');
            $request->method('getUri')->willReturn($uri);

            $this->assertEquals('Hello World!', $this->chain->handle($request));
        }
    }
}



?>