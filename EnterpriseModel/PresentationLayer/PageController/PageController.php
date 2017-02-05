<?php
namespace Angular\Controller;
use Angular\Registry\Scope\RequestRegistry;
use Angular\Request\Request;

/**
 * 页面控制器基类
 * Class PageController
 * @package Angular\Controller
 *
 * 本类中使用了之前的一些工具类， RequestRegistry、Request
 * Controller 类主要负责访问一个 Request 对象；管理视图加载
 * 实际项目中需求增长很快，就可以相应设计越来越多的子类来处理不同的需求
 *
 * 子类可以放在视图的内部，也可以分离出来；
 * 一般分离出来会使代码更简洁
 */
abstract class PageController{
    private $request;
    public function __construct()
    {
        $request = RequestRegistry::getRequest();
        if(is_null($request)){
            $request = new Request();
            $this->request = $request;  //获取命令
        }
    }
    abstract public function process(); //抽象方法
    public function forward($resource)
    {
        include ($resource);//转向
        exit();
    }
    public function getRequest(){
        return $this->request;
    }
}
?>