<?php
/**
 * 前端控制器模式用一个中心来处理所有发来的请求；最后调用视图来呈现给用户
 * 前端控制器模式是企业应用的核心模式之一，也是最有影响的企业模式之一
 *
 * 问题：当请求可以发送到系统中的多个地方时很难避免代码重复
 * 这就意味着系统中多个地方可能要执行同一个操作；我们可以复制代码从一个地方到另一个地方
 * 但这样的话，当修改系统中某个地方时，其他地方也要跟着改变，这回让代码难以维护
 * 要避免这种情况，首先要做的是把公共操作集中到类库代码中
 * 但是，即便是这样，对函数和方法的调用仍然会分布到系统的各个部分
 *
 * 总结来说：当请求可以发送到系统的多个地方，意味着系统多个地方可能会出现重复代码
 *
 * 当系统控制器和视图混在一起的时候，管理视图的选择和切换是另一个难点
 * 在一个复杂的系统中，随着输入和逻辑层中操作的成功执行，一个视图的提交动作可能会产生任意数目的结果页面
 * 从一个视图跳到另一个视图时会产生混乱，特别当某个视图被用在多个地方时
 *
 * 前端控制器模式定义了一个中心入口，每个请求都从这个入口进入系统
 * 前端控制器处理请求并选择要执行的操作
 * 操作通常都定义在 Command 对象中
 * Command 对象通过命令模式来组织
 *
 * 自建框架是先把最基础的跑通；在根据具体情况，由设计模式来优化
 * 各种模式的配合使用共同搭建一个系统
 */
namespace Angular\Controller;
/**
 * Controller 类非常简单，没有考虑错误处理；系统中的控制器负责分配任务给其他类，其他类完成实际处理
 * Controller 类的构造方法被声明为 private 因此客户端只能通过 run() 方法来实例化 Controller类
 * run() 是静态方法  Controller：：run() 即可调用
 *
 * init() 和 handleRequest() 方法体现了php的特性
 * 在某些语言中init()只在应用第一次启动时运行， handleRequest() 在用户请求到来时运行
 * PHP是解释型语言，每次请求中产生的数据在请求结束时被销毁，无法提供给下一次请求使用，所有下次请求还要执行init()方法
 *
 * 工作流程： init()中获得 ApplicationHelper （助手类）的一个实例，这个类用来管理应用程序的配置信息
 * 控制器的 init()方法调用  ApplicationHelper 类中同名的 init() 方法用于初始化应用程序要使用的数据
 *
 * handleRequest() 方法通过 CommandResolver 来获取 一个 Command对象，然后调用 Command对象的 execute()方法来执行实际操作
 *
 *
 * Class Controller
 * @package Angular\Controller
 */
class Controller{
    private $applicationHelper;
    private function __contruct(){} //单例
    public static function run(){
        $instance = new Controller();   //单例实现
        $instance->init();
        $instance->handleRequst();
    }

    /**
     * 初始化操作
     * 初始化的工作也委托给别的类
     */
    public function init(){
        $applicationHelper = ApplicationHelper::instance();
        $applicationHelper->init();
    }

    /**
     * 处理请求
     * 分发请求给业务类
     */
    public function handleRequest(){
        $request = new Request();
        $cmd_r = new \Angular\Command\CommandResolver();
        $cmd = $cmd_r->getCommand($request);
        $cmd->execute($request);
    }
}

?>