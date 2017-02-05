<?php
namespace Angular\Registry\Primary;
/**
 * Class Registry_alert_1
 * @package chen\controller\ApplicationHelper
 * 采用键值方式的好处是不需要为希望储存和访问的每个对象都创建类方法
 * 坏处就是重新引入了全局变量（数组的值可以全局访问），有可能被覆盖
 * 这样类似映射的结构在开发时很有用
 */
class Registry{
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

?>