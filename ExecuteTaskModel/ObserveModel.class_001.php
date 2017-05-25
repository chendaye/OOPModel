<?php
/**
 * 程序员的目标是创建在改动或移动时对其他组件影响最小的组件
 * 如果一个组件的改变会引起代码库其他地方一连串的改变，那么开放任务很快会变成产生bug和修复bug的恶性循环
 * 系统中的组件必然包含着对其他组件的引用，然而可以使用不同的策略让引用尽量减少
 */
namespace ObserveProblem{
    /**
     * 现在有一个客户登录类
     */
    class Login{
        const LOGINUSER = 1;
        const LOGINPASS = 2;
        const LOGINACCES = 3;
        private $status = array();
        //模拟登录
        function handelLogin($user, $pass, $ip){
            switch (rand(1,3)){
                case 1:
                    $this->setStatus(self::LOGINACCES, $user, $ip);
                    $ret = true;
                    break;
                case 2:
                    $this->setStatus(self::LOGINPASS, $user, $ip);
                    $ret = false;
                    break;
                case 3:
                    $this->setStatus(self::LOGINUSER, $user, $ip);
                    $ret = false;
                    break;
            }
            /**
             * 至此，此类的核心功能已经实现
             *
             * 但是：市场部门要求将用户登录的ip地址 记录进日志，这时就要调用 Logger 类
             * 类似的要求还有很多
             * 但是如果在代码中加入功能来满足要求，回破坏设计
             * Login 就会深深的嵌入到这个特殊的系统中，无法再实现重用
             * 如果想移植代码，我们就会走上  剪切粘贴的道路
             * 但是系统中不应该出现两个相似但有不同的类，不然修改了其中一个的代码，就要修改另一个
             */
            Logger::logIp($user, $pass, $ip);
            return $ret;
        }

        /**
         * 保存登录信息
         * @param $status
         * @param $user
         * @param $ip
         */
        private function setStatus($status, $user, $ip){
            $this->status = array($status, $user, $ip);
        }

        /**
         * 获取状态值
         * @return array
         */
        public function getStatus(){
            return $this->status;
        }
    }
    class Logger{
        static public function logIp($user, $pass, $ip){
        }
    }
}
namespace ObserveSolution{
    /**
     * 如何来拯救 Login 类： 观察者模式是一个好选择
     * 客户端元素（非系统设计时的核心功能，后来不断要求的）
     * 观察者模式是的核心 是把 观察者（客户端元素）从 主体（中心类中分离出来
     * 当主体知道事件发生时，观察者（客户端元素）被通知到
     * 同时不将主体与观察者的关系硬编码
     * 为实现这功能， 要允许观察者在主体上进行注册
     *
     * 现：Login 有三个新方法
     * attach()
     * detach()
     * notify()
     * 并强制其使用 Observabel 接口
     */
    //TODO:观察者支持接口，主体要强制实现
    interface Observabel{
        public function attach(Observe $observe);
        public function detach(Observe $observe);
        public function notify();
    }

    /**
     * 使用观察者的主体类
     * Class Login
     * @package ObserveSolution
     */
    class Login implements Observabel {
        const LOGINUSER = 1;
        const LOGINPASS = 2;
        const LOGINACCES = 3;
        private $status = array();
        private $observe;   //私有属性，用来注册观察者
        //TODO:注册观察者
        public function __construct()
        {
            $this->observe = array();
        }
        public function attach(Observe $observe)
        {
            // TODO: 添加观察者
            $this->observe[] = $observe;
        }
        public function detach(Observe $observe)
        {
            $newObserve = array();
            // TODO: 删除观察者
            foreach ($this->observe as $obs){
                if($obs !== $observe){
                    $newObserve[] = $obs;
                }
                $this->observe = $newObserve;
            }
        }
        public function notify()
        {
            // TODO: 告知观察者发生何事
            foreach ($this->observe as $obs){
                //todo:执行每一个观察者的 execute 方法
                $obs->execute($this);    //告知每一个观察者
            }
        }
        /**
         * 现在 Login 类管理着很多观察者对象
         * attach() 添加观察者
         * detach() 删除观察者
         * notify() 告诉观察者发生的事情， 会遍历观察者列表，并调用每一个观察者的 execute 方法
         */
        public function handelLogin($user, $pass, $ip){
            switch (rand(1,3)){
                case 1:
                    $this->setStatus(self::LOGINACCES, $user, $ip);
                    $ret = true;
                    break;
                case 2:
                    $this->setStatus(self::LOGINPASS, $user, $ip);
                    $ret = false;
                    break;
                case 3:
                    $this->setStatus(self::LOGINUSER, $user, $ip);
                    $ret = false;
                    break;
            }
            //TODO:通知观察者
            $this->notify();
            return $ret;
        }
        /**
         * 保存登录信息
         * @param $status
         * @param $user
         * @param $ip
         */
        private function setStatus($status, $user, $ip){
            $this->status = array($status, $user, $ip);
        }

        /**
         * 获取状态值
         * @return array
         */
        public function getStatus(){
            return $this->status;
        }

    }
    /**
     * 观察者接口
     * 定义观察者必须实现的方法
     * 这样任何实现这个接口的观察者 都可以 通过 Observabel $observabel 的 attach() 等 在主体注册删除
     */
    interface Observe{
        //TODO:持有一个主体的实例
        public function execute(Observabel $observabel);
    }

    /**
     * 一个观察者实例
     * Class Security
     * @package ObserveSolution
     */
    class Security implements Observe {
        //TODO:观察事件是否发生
        public function execute(Observabel $observabel)
        {
            // TODO: Implement execute() method.
            $status = $observabel->getStatus();
            if($status[0] == Login::LOGINPASS){
                print '发邮件';
            }
        }
    }
    //使用
    $login = new Login();   //主体
    /**
     * 将一个观察者注册进主体中
     * 随后 new Login() 中会执行 notify() 方法 遍历所有所有注册的观察者
     * 此方法中遍历调用 观察者的方法 execute(Observabel $observabel) ， 方法告知其发生的事件，
     * 由观察者自己在 execute 中  判断是否响应事件
     *
     * 实际上，简单的将  就是 在主体中 存储某些特定对象的实例
     * 每当主体进行什么操作的时候 就遍历 这些存好的实例  逐一调用 每一个实例的 特定方法 方法中判断 满足一定的条件
     * 就响应特定的操作
     * 注意此特定的方法 execute() 中 要传入主体的实例，以获取主体的操作状态、事件信息
     */
    //TODO:注册观察者
    $login->attach(new Security());
    //TODO:处理登录，之后执行notify()方法，通知观察者发生的事情
    $login->handelLogin(1,2,3);
}




namespace ObserveSolutionTwo{

    use ObserveSolution\Login;
    use ObserveSolution\Observe;
    use ObserveSolution\Observabel;

    /**
     * 上面的例子中： 观察者对对象 通过使用 Observabel $observabel 实例 来获取 事件信息
     * 主体类决定了是否给观察者查询状态的方法
     * getStatus()方法就是给观察者获取时间信息的方法
     *
     * 但是添加的这个方法，仍然存在问题
     * 观察者调用 Login::getStatus();方法是 必须知道 Observabel $observabel 中存在 此方法
     * 尽管 $observabel 一定是 Observabel 的扩展  但是 不一定是 Login ；有可能不支持getStatus() 方法
     * 解决方法有：
     * 可以在 Observabel 中添加 getStatus() 强制子类实现
     * 或者将其 命名为 ObservabelLogin 这样的名称来辨识
     * 还有一个方法，
     * 继续保持 Observabel 接口的通用性， 由观察者类 Observe 来保证他们的主体是正确类型
     * 它们也可以将自己添加到主体中
     * 因为会有多个 Observe 类型的对象 ，执行一些它们共有的任务
     * 所有先创建一个抽象类
     *
     * 如此 LoginObserve 的所有对象 都能判断使用的是 Login 对象 还是 任意的 Observabel 对象
     */
    // TODO: 本质上又是 添加 一级抽象级  处理 同类 扩展的 不同特性
    abstract class LoginObserve implements Observe {
        private $login;
        //TODO:使用一个具体的主体类   绑定一个特定的 主体
        public function __construct(Login $login)
        {
            $this->login = $login;
            $login->attach($this);  //在主体中注册
        }
        //TODO:观察者自己的功能实现
        public function execute(Observabel $observabel)
        {
            // TODO: 如果与此观察者 的 主体 匹配
            if($observabel === $this->login){
                $this->doExecute($observabel);
            }
        }

        /**
         * 抽象类会有许多不同的实现，所有具体的功能实现定义成抽象方法，而不是写成死的
         * @param Login $login
         * @return mixed
         */
        abstract function doExecute(Login $login);
    }
    //使用
    class Security extends LoginObserve {
        public function doExecute(Login $login)
        {
            // TODO: Implement doExecute() method.
            $status = $login->getStatus();
            if($status[0] == Login::LOGINPASS){
                print '发邮件';
            }
        }
    }
    $login = new Login();
    new Security($login);   //此观察者，已经在构造函数中 注册了
    echo '<br>';
    $login->handelLogin(1, 2, 3);
}
namespace NativeSPL{
    /**
     * PHP 通过内置的SPL 扩展了对观察者模式的原生支持
     * SPL 是一套可以帮助工程师处理很多面向对象问题的工具，堪称一把面向对象的瑞士军刀
     * 其中，观察者由 SplObserver  SplSubject  SplObjectStorage 三个元素组成
     * SplObserver  SplSubject 是接口
     * 与 Observe   Observabel 接口完全相同
     * SplObjectStorage 是一个工具类，用于储存对象和删除对象
     */
    //TODO:示例代码 一一对应
    class Login implements \SplSubject {
        private $storage;
        public function __construct()
        {
            $this->storage = new \SplObjectStorage();
        }
        public function attach(\SplObserver $observer)
        {
            // TODO: Implement attach() method.
            $this->storage->attach($observer);
        }
        public function detach(\SplObserver $observer)
        {
            // TODO: Implement detach() method.
            $this->storage->detach($observer);
        }
        public function notify()
        {
            // TODO: Implement notify() method.
            foreach ($this->storage as $obs){
                $obs->update($this);
            }
        }
        //TODO:
    }
    abstract class LoginObserve implements \SplObserver {
        private $login;
        public function __construct(Login $login)
        {
            $this->login = $login;
            $login->attach($this);
        }
        public function update(\SplSubject $subject)
        {
            // TODO: Implement update() method.
            if($subject === $this->login){
                $this->doUpdate($subject);
            }
        }
        abstract function doUpdate();
    }
    /**
     * 使用 SplObserver SplSubject 与自定义的 Observer 和 Subject 没区别
     *只是不愿再声明相关接口
     *
     * 又使用组合模式创建了一个灵活，可扩展的模型
     * Login类可以从上下文中提取出来，放入另一个项目中与其他观察者一起工作
     */
    //TODO:总结：观察者模式也采用组合原则，是把 客户端的要求代码 与 核心功能代码  分开
    //TODO:每一个客户端要求功能 就是一个观察者，核心代码会持有观察者实例，并且遍历观察者调用观察者的功能实现方法
}
?>