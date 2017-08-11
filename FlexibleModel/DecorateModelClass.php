<?php
/**
 * 装饰模式：
 * 组合模式用来聚合组件
 * 装饰模式用来改变组件的功能，该模式同样体现了组合模式的重要性，组合是在代码运行时实现的
 * 继承是共享父类特性的简单方法，当时会需要 将要改变的特性硬编码到继承体系中；这降低了系统的灵活性
 *
 * 问题：
 * 将所有功能建立在继承体系之上会导致系统中的类“爆炸式”增长
 * 并且当对继承树不同部分做相似修改时，代码会重复
 */
namespace DecorateProblem{

    use PrototypeModel\Plains;

    /**
     * 游戏区块类
     * Class Title
     * @package DecorateProblem
     */
    abstract class Title{
        abstract public function wealth();
    }
    class Plain extends Title {
        private $wealth = 2;
        public function wealth()
        {
            // TODO: Implement wealth() method.
            return $this->wealth;
        }
    }
    /**
     * 现在需要Plain做更多的事情； 处理一下自然资源 和 污染
     * 一个办法是从 Plain 派生
     *
     * 被污染的区块的财富系数
     */
    class PollutePlain extends Plain{
        public function wealth(){
            return parent::wealth() + 6;
        }
        public function pollute(){
            return parent::wealth() - 4;
        }
    }
}
namespace DecorateSolution{

    use DecorateProblem\Title;

    /**
     * 上一个方案，可以获得 一个污染区块的 财富  也可以获得 一个钻石区块的 财富 但是无法既有污染又有钻石的对象
     * 由此可以看出:功能定义完全依赖于继承体系，会导致类的数量过多，而且代码会重复
     * 因为：继承树只有一个根，再怎么改变 还是要依赖根定义的功能， 不灵活，且如果强行加功能情况会更糟糕
     *
     * 再举一个栗子：
     * web要在相应用户之前做一系列操作
     * 验证用户、记录请求、将原始输入转化为特定格式等等
     * 现在遇到同样的问题；用单纯的继承也可以实现，但是当需求复杂，弊端就显现出来了 不灵活 代码重复 不好扩展
     *
     * 装饰模式使用  组合  和  委托 ，而非单纯的继承来解决问题
     * 实际上  Decorator 对象会持有 另外的对象的实例
     * Decorator 对象会实现 与 被调用对象 的方法 相对应的 方法
     * 这样可以在运行时 创建 一系列 的 Decorator 方法
     */
    abstract class Tile{
        abstract public function wealth();
    }

    /**
     * 基础自抽象父类
     * Class Plain
     * @package DecorateSolution
     */
    class Plain extends Tile{
        private $wealth = 6;
        public function wealth()
        {
            // TODO: Implement wealth() method.
            return $this->wealth;
        }
    }

    /**
     * 引入TileDecorator 继承自 Tile 但不实现wealth 类 所以声明为抽象类
     * 传入对象 保存在 $tile 属性中，声明为 protected 以便子类访问
     *
     * Class TileDecorator
     * @package DecorateSolution
     */
    //TODO:装饰器抽象接口，持有一个特定的  Tile对象（实现Tile接口的子类实例）， 用来委托  本身是  Tile 类  同时持有 Tile 类实例
    abstract class TileDecorator extends Tile {
        protected $tile;
        public function __construct(Tile $title)
        {
            $this->tile = $title;
        }
    }

    /**
     * 以下类扩展自 DecorateSolution ，则都拥有 指向 Tile 对象的引用
     * 当 wealth 函数被调用时  先调用所拥有的 Tile 对象的 wealth 方法；然后指执行自己特有的方法
     * 这便是组合和委托， 可以在运行时轻松合并对象
     * 因为模式中的对象都扩展自 Tile ;wealth() 方法在任何对象中都支持 不论是 一个 装饰器 还是一个真正的Tile对象，以客户端代码不需要知道内部是如何合并的
     *
     * Class Diamon
     * @package DecorateSolution
     */
    //TODO:装饰器的实例
    class Diamon extends TileDecorator {
        //TODO:实现抽象方法；也就是委托方法
        public function wealth()
        {
            // TODO: Implement wealth() method.
            return $this->tile->wealth()+8;
        }
    }
    class Pollution extends TileDecorator {
        public function wealth()
        {
            // TODO: Implement wealth() method.
            return $this->tile->wealth() - 8;
        }
    }
    $tile = new Plain();
    echo $tile->wealth();
    //TODO:委托$tile的wealth()方法来实现 自身的wealth()方法
    $decorator = new Diamon($tile);
    echo $decorator->wealth();
    //更进一步的
    $tile = new Pollution(new Diamon(new Plain()));
    echo '<br>'.$tile->wealth();
    /**
     * 这样的模式具有很大的扩展性；可以非常轻松的添加新的装饰器，或新组件
     * 通过大量使用装饰器。可以在运行时拥有很大的灵活性
     *
     * 和之前的组合模式有一点很相似，也是极其重要的
     * 就是在 父抽象 接口之后 再建 一个 抽象级 并且定义新的抽象方法以实现父类基础上另外的功能，然后子类可以在此基础上扩展
     * 加了一级抽象接口之后，所有子类仍然继承自 一级父类，实现一级父类的所有定义功能，本质上仍然是同类型的对象
     * 但另一方面，扩展自二级抽象接口的子类，有会支持新的功能
     */
}
namespace DecoretorRelize{
    /**
     * 装饰器模式 对于创建过滤器很有用
     * 客户端程序可以将核心组件与装饰对象合并；从而对核心方法进行过滤、压缩、缓冲等操作
     *
     * 以web请求为例
     */
    class Request{}
    //TODO:组件接口
    abstract class Process{
        abstract public function process(Request $request);
    }
    //TODO:组件实例
    class MainProcess extends Process {
        public function process(Request $request)
        {
            // TODO: Implement process() method.
            print __CLASS__;
        }
    }
    //TODO:装饰器
    abstract class DecorateProcess extends Process {
        protected $process;
        //TODO:装饰器构造方法拥有一个Process对象
        public function __construct(Process $process)
        {
            $this->process = $process;
        }
    }
    //TODO:使用装饰器
    class LogRequest extends DecorateProcess {
        public function process(Request $request)
        {
            // TODO: Implement process() method.
            print __CLASS__.'<br>';
            $this->process->process($request);
        }
    }
    class OutRequest extends DecorateProcess {
        public function process(Request $request)
        {
            // TODO: Implement process() method.
            print __CLASS__.'<br>';
            $this->process->process($request);
        }
    }
    class CheckRequest extends DecorateProcess {
        public function process(Request $request)
        {
            // TODO: Implement process() method.
            print __CLASS__.'<br>';
            $this->process->process($request);
        }
    }
    //TODO:装饰模式，过滤器
    //TODO:一层一层，最终调用new MainProcess() 的 process 方法，每一次调用方法前输出一条类信息
    $process = new CheckRequest(new LogRequest(new OutRequest(new MainProcess())));
    echo $process->process(new Request());

    /**
     * 这就实现了 在运行时合并这些类的初始对象  创建过滤器来对每一个请求，按不同孙旭执行不同的操作
     *
     * 组合和继承通常是同时使用的
     * LogRequest 继承自Process 却表现为对另一个 Process 对象的封装
     * 因为装饰对象作为子对象的包装，所以保持基类中的方法尽可能少很重要
     * 如果基类有 大量 public 方法，那么装饰类 必须给所有 public方法  加上委托
     * 可以用抽象的装饰类来实现，但仍会带来耦合，并带来bug
     */
}
?>