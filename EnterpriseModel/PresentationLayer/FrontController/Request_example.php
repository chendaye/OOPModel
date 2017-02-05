<?php
/**
 * php中，对系统的请求会被自动封装到一个全局有效的数组中
 * 之前的例子中Request 对象被传递给 CommandResolver 在被传递给具体的 Command
 *
 * 为什么不直接查询 $_GET $_POST $_REQUEST ?
 * 因为：把请求集中到一个地方，可以有更多的选择
 * 比如可以对所有请求使用过滤器
 * 或者可以从非 http请求中収集请求参数，允许应用程序在命令行或者测试脚本中运行
 *
 * Request 对象也可以用于存储需要和视图层交换的数据，从这个角度理解 Request 也可以提供响应请求的操作
 */
namespace Angular\Request;
use Angular\Registry\Scope\RequestRegistry;

/**
 * 此类的主要功能是设置和获取属性
 * init() 方法负责填充私有的$properties 数组
 * init() 同时支持 命令行参数和 http请求
 *
 * 有了一个 Request 对象后可以通过getProperty() 来获取http请求中的参数
 * Request 类同时也维护着 $feedback 数组；控制器可以通过$feedback 数组方便的传递消息给用户
 *
 * Class Request
 * @package Angular\Request
 */
class Request{
    private $properties;
    private $feedback = array();
    public function __construct()
    {
        $this->init();
        RequestRegistry::setRequest($this); //注册表
    }
    public function init(){ //初始化
        if(isset($_SERVER['REQUEST_METHOD'])){
            $this->properties = $_REQUEST;
            return null;
        }
        foreach ($_SERVER['argv'] as $arg){ //获取http请求信息
            if(strpos($arg, '=')){
                list($key, $val) = explode("=", $arg);
                $this->setProperty($key. $val); //提取信息保存在数组中
            }
        }
    }
    public function getProperty($key){
        if(isset($this->properties[$key])){
            return $this->properties[$key]; //获取储值
        }
    }
    public function setProperty($key, $val){
        $this->properties[$key] = $val; //存储值
    }
    public function addFeedback($msg){
        array_push($this->feedback, $msg);  //存
    }
    public function getFeedback(){
        return $this->feedback; //取
    }
    public function getFeedbackString($separator = "\n"){
        return implode($separator, $this->feedback);
    }
}
/**
 * 前端控制器模式：接受请求->分析请求->处理响应请求
 * 流程：Controller -> 委托 ApplicationHelper 初始化获取必要信息
 * -> 从 CommandResolver 获取一个 相应的 Command 对象
 * -> Command::execute() 处理业务逻辑
 *
 * 前端控制器使用时要慎重考虑，因为在使用之前要进行大量的前期工作
 * 若项目很小，前端控制器占得比重过大就没必要用此模式
 *
 * 可以把前端控制器的代码提取到共享代码库中，打造一个可重用的框架
 *
 * 缺点：每次请求都要加载所有的配置信息
 * 这些开销可以通过缓存来降低；最有效的办法是把配置写到代码中去，但是把配置单独放在一个配置文件里有利于其他人管理
 * 可以读取配置文件，把内容转换成数据写入缓存文件
 * 原生的缓存创建后系统会一直使用缓存中的数据，直到配置文件发生变化导致缓存需要重建
 * 可以把数据序列化后放入缓存，虽然效率不高但是方便
 *
 * 前端控制器：集中了系统的表现逻辑，这样就可以在一个地方（某个类集合中）同时处理请求和选择视图
 * 这样就能够降低代码重复和bug的发生率
 *
 * 前端控制器易于扩展，搭建好核心部分后，可以很方便的新增Command类和视图
 */
?>