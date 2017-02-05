<?php
namespace Angular\Controller;
use \Angular\Registry\Scope\ApplicationRegistry;
/**
 * ApplicationHelper类并不是前端控制器的核心，但是前端控制器通常要通过应用助手类来获取基本的配置数据
 * ApplicationHelper类 就是用来给控制器获取配置信息的
 *
 * 这个类的作用就是读取配置文件中的数据，并使客户端代码能访问这些数据，用单例模式来实现
 * 单例模式能让它为系统中所有类工作
 *
 * 现在已经实现了 ApplicationRegistry （应用注册表），现在应该重构代码，把ApplicationHelper 改写为注册表
 * 而不是两个任务重叠的单例对象   将ApplicationRegistry的核心功能从领域对象的存取中分离出来
 *
 * 因此，init() 方法只负责加载配置数据
 * 它会检查 ApplicationRegistry 看数据是否被缓存；如果Registry对象中值已经存在，init()就什么也不做
 * 如果系统要做大量的初始化工作，这样的缓存机制很有用
 * 在将应用程序初始化和独立请求相分离的语言中，可以使用复杂的初始化操作
 * 在php中要尽量使用缓存来减少初始化操作
 * 缓存可以有效保证复杂且耗时的初始化过程只在第一次请求时发生，之后的所有请求都能从缓存中获益
 *
 * 第一次运行（或者缓存文件被删除--这是一种强制重新读取配置的有效方法），getOptions()将被调用，然后从配置文件中加载xml数据
 *
 * 类中使用了一个抛出异常的技巧，避免了类中导出出现抛出异常的代码
 * ensure($expr, $message) 就是把抛出异常封装在方法中
 */
class ApplicationHelper{
    private static $instance;
    private $config = "/tmp/data.xml";
    private function __contruct(){} //单例
    public static function instance(){
        if(!self::$instance){
            self::$instance = new self();   //获取单例
        }
        return self::$instance;
    }
    public function init(){
        $dsn = ApplicationRegistry::getDSN();   //通过注册表获取信息
        if(!is_null($dsn)){
            return null;
        }
        $this->getOptions();
    }
    //获取配置信息
    private function getOptions(){
        $this->ensure(file_exists($this->config), '没找到配置文件');
        $options = SimpleXml_load_file($this->config);
        $dsn = (string)$options->dsn;
        $this->ensure($dsn, '没有dsn');
        ApplicationRegistry::setDSN($dsn);  //设置值
    }
    //错误提示
    private function ensure($expr, $message){
        if(!$expr){
            throw new \Angular\AppException($message);
        }
    }
}
/**
 * 缓存对系统开发者和使用者都有好处
 * 系统可以很方便的维护一个xml配置文件
 * 同时使用缓存意味着可以以很快的速度访问配置文件中的数据
 * 不经常修改的配置可以写在xml文件中，或者以别的数据结构存储
 */
?>