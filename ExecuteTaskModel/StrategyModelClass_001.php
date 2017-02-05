<?php
//TODO:面向对象编程，就是面向接口编程； 一个原则  接口只定义 其 必须的功能（所有子类必须实现的核心功能），同时不能有多余的功能在接口中
//TODO:子类 则专注于完成自己特有的功能 同时 他的父类 或者接口 已经实现了 其 公共功能
//TODO:可以用多级抽象来，处理不同子类的不同功能需求
//TODO:终极目标： 接口（父类）中只含有，该类定义的核心功能（所有子类必须支持）， 子类中只完成 自己特定的功能
/**
 * 策略模式：
 * 有些程序会赋予类太多的功能，为了能让类执行写相关的操作，如果操作环境变化，类需要分解为子类
 */
namespace Strategy{
    /**
     * 问题需求，解决：
     *现在有一个问题类；但是问题有很多种：LogQuestion MatQuestion RegQuestion
     * 首先想到的是 采用继承 Question   扩展  LogQuestion MatQuestion RegQuestion
     * 但现在 Question 有要 分为A B 两大子类 且都 拥有 子类  LogQuestion MatQuestion RegQuestion
     * 这样就有在继承体系中创建大量的类，而且会造成代码重复
     *
     * 只要不断在继承树各个分支中出现同样的算法  就应该将这些方法抽象成独立的类型
     *
     * 解决：
     * 策略模式简单强大
     * 当类必须支持同一个接口的多个实现 例如 A B  要支持 LogQuestion MatQuestion RegQuestion
     * 最好的方法是把这些要实现的功能 LogQuestion MatQuestion RegQuestion 单独提取出来
     * 作为单独的类 去实现
     *
     * 这就是组合优于继承
     * 将 LogQuestion MatQuestion RegQuestion 定义封装成类
     * 减少了子类 增加了灵活性 而且随时可以加入新的方法  而不必 修改 A  B
     */
    //TODO:这里有一个大发现：在面向过程编程中；
    //TODO:我们把一个个独立的功能封装成函数，以此来减少代码重复，实现复用，降低耦合
    //TODO:更进一步，我们有了面向对象编程，把一个功能模块放在一个类中
    //TODO:而事实上，在类的内部也可能出现代码重复的问题，比如继承的时候，这样就与面向过程封装函数遇到的本质是一个问题
    //TODO:因此，在类的内部，我们也需要要把独立的功能模块封装成类，再杜绝代码重复的同时，还可以大大提高代码的灵活性
    /**
     * 父类
     * Class Question
     * @package Strategy
     */
    abstract class Question{
        protected $prompt;
        protected $maker;

        /**
         * 构造函数初始化一个 Mark 对象 ，然后用Mark 来委托任务； 与调用自身方法效果并无不同
         *唯一的一点就是， Question 类 现在要 持有 Mark 类的实例  来实现委托
         * Question constructor.
         * @param $prompt
         * @param Maker $maker
         */
        public function __construct($prompt, Maker $maker)
        {
            $this->prompt = $prompt;
            $this->maker = $maker;
        }

        /**
         * 委托的具体实现
         * 委托一定要持有相应功能对象的实例
         * 实例的持有可以在构造函数中初始化；也可以在具体的方法中用参数传递
         * 不过在类的构造函数中初始化，就相当一 委托对象是类自己的一部分，联系更紧密
         * @param $reponse
         * @return mixed
         */
        public function mark($reponse){
            return $this->maker->mark($reponse);
        }
    }

    /**
     * 两个子类
     * Class AQuestion
     * @package Strategy
     */
    class AQuestion extends Question {
        //TODO:问题A
    }
    class BQuestion extends Question {
        //TODO:问题B
    }

    /**
     * 从原有Question 类中 提取出来的； 同样只要是类 必定先要定义好接口 功能 在有子类具体实现
     * Class Maker
     * @package Strategy
     */
    //TODO:给其主类委托（从主类中分离出来的）
    abstract class Maker {
        protected $test;
        public function __construct($test)
        {
            $this->test = $test;
        }
        abstract function mark($response);
    }
    class LogMaker extends Maker {
        private $engine;
        public function __construct($test)
        {
            parent::__construct($test);
        }
        public function mark($response)
        {
            // TODO: Implement mark() method.
            return true;
        }
    }
    class MatMaker extends Maker {
        public function mark($response)
        {
            // TODO: Implement mark() method.
            return false;
        }
    }
    class RegMaker extends Maker {
        public function mark($response)
        {
            // TODO: Implement mark() method.
            print_r($response);
        }
    }
}
?>