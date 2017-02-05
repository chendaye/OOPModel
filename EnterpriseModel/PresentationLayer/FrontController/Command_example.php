<?php
/**
 * 实例化 Command 的时候不需要参数，是因为Command本身的特性
 *
 * 绝对不可以使用未经检查的用户数据，请求要经过严格的测试
 * 还可以只接受与配置文件值相匹配的命令字符串
 *
 * 创建命令的时候，尽可能让其中不包含逻辑
 * 一旦它们包含了具体的业务处理逻辑，就会变成混乱的事务脚本，代码重复也随之而来
 * 命令是一种中转站：它们解释请求，调用领域逻辑来改变对象，然后把对象传到表现层中
 * 一旦命令类中开始处理比这些更复杂的操作，就需要对代码进行重构，需要把具体的功能移到表现层或者领域类中
 */
namespace Angular\Command;
use Angular\Request\Request;

/**
 * 命令的基类
 * Class Command
 * @package Angular\Command
 */
abstract class Command{     //定义为final，任何子类都不能覆盖父类的构造方法,所有子类都不需要参数
    public final function __construct(){}
    public function execute(Request $request){
        $this->doExecute($request); //分发请求
    }
    abstract public function doExecute(Request $request);  //具体处理请求
}


?>