<?php
    /**
     * 组合模式
     * 组合由于继承
     * 组合对象的灵活性很高，单独的继承不可能达到
     * 继承是一堆变化的上下文设计的有效方式，但是限制了灵活性，特别当类菜单多重任务时
     * 总的来说：组合更灵活
     */
    namespace problem{
        /**
         * 问题：
         * Lesson抽象类是 大学课程的模型；它定义了 cost() chargeType() 方法
         * 有两个实现类：FixedPriceLesson 和 TimePriceLesson 为课程提供了不同的收费机制
         *
         * 现在要引入新特性：要处理 演讲 研讨会 ，因为两者以不同方式注册计费，因此需要独立的类
         * 故：现在需要从 Lesson  引出两个分支》》 Lecture +  Seminar，处理不同的定价策略， 区分开 演讲、研讨会
         *
         * 这样一来就  Lecture/Seminar   中会出现重复代码
         */
        abstract class Lesson{
            protected $duration;
            const FIXED = 1;
            const TIMED = 2;
            private $costtype;

            /**
             * 构造函数
             * Lesson constructor.
             * @param $duration
             * @param $costtype
             */
            public function __construct($duration, $costtype){
                $this->duration = $duration;
                $this->costtype = $costtype;
            }

            /**
             * 计价函数
             * @return int
             */
            public function cost(){
                //TODO:使用条件语句是为了能使方法，兼容两个子类 Lecture Seminar
                //TODO:但是在父类中使用条件语句，是不合理的；通常应该是用 多态 来代替条件语句
                switch ($this->costtype) {
                    case self::FIXED:
                        return (5*$this->duration);
                        break;
                    case self::TIMED:
                        return 30;
                        break;
                    default:
                        $this->costtype = self::TIMED;
                        return 30;
                }
            }

            /**
             * 计价模式
             * @return string
             */
            public function chargeType(){
                switch ($this->costtype) {
                    case self::FIXED:
                        return 'hour';
                        break;
                    case self::TIMED:
                        return 'second';
                        break;
                    default:
                        $this->costtype = self::TIMED;
                        return 'second';
                }
            }
            //TODO:更多函数。。。。
        }

        /**
         * 分支
         * 子类继承
         * Class Lecture
         * @package problem
         */
        class Lecture extends Lesson {
            //TODO:实现抽象类
        }

        /**
         * 分支
         * 子类继承
         * Class Seminar
         * @package problem
         */
        class Seminar extends Lesson {
            //TODO:实现抽象类
        }
        //TODO:使用;注意常量与静态成员样，不属于某个实例
        $lecture = new  Lecture(6, Lesson::FIXED);
        echo $lecture->cost().'--'.$lecture->chargeType().'<br>';
        $seminar = new Seminar(8, Lesson::TIMED);
        echo $seminar->cost().'--'.$seminar->chargeType();
    }


    namespace solution{
        /**
         * 使用组合，组合灵活性更高，但是没有纯粹的继承好理解
         * 可以用策略模式：适用于将一组算法移到一个独立的类型中
         * 此栗子中可以移走，费用计算的相关代码，简化Lesson类
         */
        //TODO:总结， 任何一个类都有一个抽象的基类（或抽象类、或接口），该基类定义了该类的功能
        //TODO:总结， 把原类中的，计费功能提取出来，单独抽象为一个类，然后该类的对象，以参数的形式传到原类中，以委托实现原有功能
        //TODO:总结， 这样扩展更加灵活，原类更加简洁，更好管理
        //TODO:总结， 尽量让类具有原子性，把影响独立性，简洁性的，分离，放在另一个接口后面
        abstract class Lesson{
            private $duration;
            private $costStrategy;

            /**
             * Lesson constructor.
             * @param $duration
             * @param CostStrategy $strategy    被单独移走的费用计算类
             */
            public function __construct($duration, CostStrategy $strategy){
                $this->duration;
                $this->costStrategy = $strategy;
            }

            /**
             * 费用计算交给，单独的类来完成
             * @return mixed
             */
            public function cost(){
                //TODO:显式调用另一个类的对象来完成功能，就叫委托
                //TODO: $this  代表自身这个对象
                return $this->costStrategy->cost($this);
            }

            /**
             * 费用计算交给，单独的类来完成
             * @return mixed
             */
            public function chargeType(){
                //TODO:显式调用另一个类的对象来完成功能，就叫委托
                return $this->costStrategy->chargeType();
            }

            /**
             * @return mixed
             */
            public function getDuration(){
                return $this->duration;
            }
            //TODO:其他方法
        }
        /**
         * 分支
         * 子类继承
         * Class Lecture
         * @package problem
         */
        class Lecture extends Lesson {
            //TODO:实现抽象类
        }

        /**
         * 分支
         * 子类继承
         * Class Seminar
         * @package problem
         */
        class Seminar extends Lesson {
            //TODO:实现抽象类
        }

        /**
         * 通过传递不同的  CostStrategy 对象来实现不同的计费方式
         * 如此，就消除了原先的条件语句，同时也更加灵活，用抽象的接口实现多态
         * 抽象费用计算类
         * Class CostStrategy
         * @package solution
         */
        abstract class CostStrategy{
            //TODO:抽象方法，计费方式
            abstract function cost(Lesson $lesson);
            //TODO:抽象方法，计费类型
            abstract function chargeType();
        }

        /**
         * 抽象费用计算类;的具体实现
         * Class TimeCostStrategy
         * @package solution
         */
        class TimeCostStrategy extends CostStrategy {
            //TODO:时间计费
            public function cost(Lesson $lesson){
                return $lesson->getDuration()*5;
            }
            public function chargeType(){
                return 'hour';
            }
        }

        /**
         * Class FixedCostStrategy
         * @package solution
         */
        class FixedCostStrategy extends CostStrategy{
            //TODO:其他计费方式
            public function cost(Lesson $lesson){
                return 30;
            }
            public function chargeType(){
                return 'second';
            }
        }
        $lesson = array();
        $lesson[] = new Lecture(6, new TimeCostStrategy());
        $lesson[] = new Seminar(6, new FixedCostStrategy());
        foreach ($lesson as $key => $val){
            echo '<br>'.$val->cost().'--'.$val->chargeType().'<br>';
        }
    }
    namespace coupling{

        use problem\Lecture;
        use problem\Lesson;
        use problem\Seminar;
        use solution\FixedCostStrategy;
        use solution\TimeCostStrategy;

        /**
         * 关于解耦
         * 重用性是面向对象编程的目标之一，但是紧耦合是其敌人
         * 当一个组件的改动，要引起系统其他地方的改动，就可以定性为紧耦合
         *
         * 上例中：继承方案，费用计算的逻辑 在 Lecture semair 中都存在，如果只改动其中一个类，系统就无法工作
         * 组合（策略模式）方案中：把费用计算提取为一个类，并且将具体的算法放在同一个公共接口后面
         *
         * 又比如一个系统中关于数据库的链接，不是大多数类的功能，也可以提取出来，放在一个公共接口后面
         * 这使类之间互相独立
         *
         * 总的来说：把具体的实现隐藏在一个干净的接口后面，就是所谓的  封装
         */
        class Register{
            //TODO:将 Lesson 类  的对象作为参数，传给类Register；如此两个类之间相对独立，降低耦合
            //TODO：任何一个类，起点一定是一个抽象类或者接口，通过此确定类的职责，类之间往往需要用对象来交流
            public function register(Lesson $lesson){
                //TODO:处理相关课程
                //TODO:通知某人
                $notifier = Notifier::getNotifier();
                $notifier->inform("new lesson: cost $lesson->cost()");
            }
        }

        /**
         * 通知类接口
         * Class Notifier
         * @package coupling
         */
        abstract class Notifier{
            //TODO:静态方法获取具体的Notifier的对象
            static public function getNotifier(){
                //TODO:根据配置或其他逻辑获得具体的类,这里用随机数代替rand(1,2)
                if(rand(1,2) == 1){
                    return new MailNotifier();
                }else{
                    return new TextNotifier();
                }
            }
            //TODO:
            abstract function inform($message);
        }

        /**
         * 邮件通知
         * Class MailNotifier
         * @package coupling
         */
        class MailNotifier extends Notifier {
            public function inform($message){
                // TODO: Implement inform() method.
                echo $message;
            }
        }

        /**
         * 书信通知
         * Class TextNotifier
         * @package coupling
         */
        class TextNotifier extends Notifier {
            public function inform($message){
                // TODO: Implement inform() method.
                echo $message;
            }
        }
        $lesson1 = new Lecture(4, new FixedCostStrategy());
        $lesson2 = new Seminar(5, new TimeCostStrategy());
        $msg = new Register();
        $msg->register($lesson1);
        $msg->register($lesson2);
    }
    //TODO:总结：针对接口（抽象类、接口）编程，而不是针对实现编程
    //TODO:一个实现表现为一个接口
    //TODO:把不同的实现隐藏在父类所定义的共同的接口后面。客户端只需要父类的对象，并不需要子类的对象
    //TODO:强调一遍：客户端只需要父类的对象，不关心子类，
    //TODO:一个类背后的组织逻辑，由父类来实现；父类就是一个插座

    //TODO:这样存在一个问题：创建抽象父类时，常会遇到如何实例化其子类的问题，选择哪个子类来对应相应条件？

    //TODO:极限编程原则：用最简单的方式解决问题

    /**
     * 变化的概念
     * 设计模式  把“变化”封装起来
     * 上例中 费用计算  就是 “变化
     * 积极寻找 “变化” 并评估是否有必要封装；根据一定条件把变化提取出来  形成子类  隐藏在一个抽象的父类后面；这个新父类能被其他类使用
     *优点：使类专注于职责；通过组合提高灵活性；使继承层级体系更加紧凑集中；减少重复
     * “变化”的特征：条件语句；误用继承
     */
?>