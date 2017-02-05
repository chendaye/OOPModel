<?php
/**
 * 书中大部分模式都可以在 层 中找到自己的位置 但是有一些模式 非常基础
 * 所以被放到了结构之外。 注册表模式  就是
 *
 * 注册表是跳出层的约束的有效途径；大多数模式都只能在某一个层，但是注册表是一个例外
 * 它可以不受层的约束
 */

/**
 * 注册表的作用是提供系统级别的对象访问功能
 * 一般说全局变量是不好的，不过凡事都有两面性，全局性的数据访问非常有吸引力
 *
 * 单例对象不能被覆盖，因此不想普通全局变量缺陷明显，因为全局变量可以在任何地方任何时间被修改
 */

namespace chen\controller\ApplicationHelper{

    use coupling\Register;

    /**
     * 问题：
     * 很多企业系统被分为几层，每个层只通过事先定义好的通道和相邻的层通信
     * 对层的分离使程序灵活，替换或者修改时可以最小化对其他地方的修改
     *
     * 现在 ApplicationHelper 类中读取配置信息
     */
    class ApplicationHelper{
        public function getOption(){
            if(!file_exists("data.xml")){
                throw new \Exception('文件不存在！');
            }
            $option = simplexml_load_file("data.xml");
            $dsn = (string)$option->dsn;
            //已经获取了数据，问题是如何让数据全局可用
            //TODO:......
        }
    }
    /**
     * 要让各个数据层都能读取这些信息
     * 解决办法：在系统中把信息从一个对象传递到另一个对象
     * 从一个负责处理请求控制的对象传递到业务逻辑层的对象
     * 再传递到负责和数据库对话的对象
     *
     * 事实上可以传递ApplicationHelper对象 或者是一个特定的 Context对象
     * 无论哪种方式都通过系统的层之间 的上下文信息从一个对象传递给另一个需要的对象
     *
     * 注册表类提供静态方法（或者单例对象的实例化方法），来让其他对象来访问其中的数据，整个系统中每一个对象都可以访问这些数据对象
     * 可以理解：注册表就是系统中的信息公示栏
     */
    class Registry{
        private static $instance;
        private $request;
        //单例
        private function __construct(){}
        public static function instance(){
            if(!isset(self::$instance)){
                self::$instance = new self();   //单例
            }
            return self::$instance;
        }
        public function getRequest(){
            return $this->request;  //获取请求
        }
        public function setRequest(Request $request){
            $this->request = $request;
        }
    }
    $ret = Registry::instance();    //获取单例,之后所有对象都使用同一个单例,在系统任何地方都能用这个方式获取
    //var_dump($ret);
    $request = $ret->getRequest();
    //var_dump($request);
    //TODO:还可以修改注册表类，基于键值储存
    /**
     * Class Registry_alert_1
     * @package chen\controller\ApplicationHelper
     * 采用键值方式的好处是不需要为希望储存和访问的每个对象都创建类方法
     * 坏处就是重新引入了全局变量（数组的值可以全局访问），有可能被覆盖
     * 这样类似映射的结构在开发时很有用
     */
    class Registry_alert_1{
        private static $instance;
        private $values = array();
        private $treeBuilder;
        private $conf;
        //单例
        private function __construct(){}
        public static function instance(){
            if(!isset(self::$instance)){
                self::$instance = new self();   //单例
            }
            return self::$instance;
        }
        public function getRequest($key){
            if(isset($this->values[$key])){
                return $this->values[$key];
            }
        }
        public function setRequest($key, $value){
            $this->values[$key] = $value;
        }
        /**
         * 可以在系统中使用注册表对象作为普通对象的工厂，注册表类不储存一个一个要提供的对象
         * 而是先创建一个对象实例，然后储存该对象的一个引用；因为注册表采用单例 始终如一
         * 这样可以做一些幕后的工作
         *
         * TreeBuilder Conf 都是虚构的类，功能也是虚构的
         * 要使用TreeBuilder 的对象，只需要简单的调用 Registry::treeBuilder() 的方法，而不会增加自己的复杂性
         * 复杂性主要来源于应用级别的数据， 比如Conf对象，系统中绝大多数类不应该与 Conf 打交道
         */
        public function treeBuilder(){
            if(!isset($this->treeBuilder)){
                $this->treeBuilder = new TreeBuilder($this->conf()->get('treedir'));    //TreeBuilder 是虚构的类
            }
            return $this->treeBuilder;
        }
        public function conf(){
            if(!isset($this->conf)){
                $this->conf = new Conf();   //Conf 是虚构的类
            }
            return $this->conf;
        }
    }

    /**
     * Class Registry_alert_2
     * @package chen\controller\ApplicationHelper
     * 注册表对测试也很有用
     */
    class Registry_alert_2{
        private static $instance;
        private static $testMode;
        //......
        public static function testMode($mode = true){
            self::$instance = null;
            self::$testMode = $mode;
        }

        /**
         * 静态方法可以提供模拟的Registry 对象 通过 self::$testMode 来判断
         * 可以使用测试模式在模拟的注册表中切换
         * @return MockRegistry|Registry_alert_2
         */
        public static function instance(){
            if(self::$testMode){
                return new MockRegistry();
            }
            if(!isset(self::$instance)){
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

?>