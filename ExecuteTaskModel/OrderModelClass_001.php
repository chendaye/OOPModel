<?php
/**
 * 命令模式最初源于图形化界面设计
 * 现在广泛应用于企业应用设计
 * 特别是控制器（请求和分发） 与 领域模型（应用逻辑）的分离
 * 总的来说：命令模式有助于系统更好的进行组织并已于扩展
 */
namespace Command{
    /**
     * 所有系统都必须决定如何相应用户的请求
     * PHP中决策过程通常可以由分散的各个php页面来处理
     * 但现在PHP开发越来越倾向于 单一入口 方式
     * 但无论单入口还是多入口，接受这都必然将用户请求委托给一个更加关注应用逻辑的层来处理
     * 因此，这个  委托  在用户请求不同页面时非常重要
     * 没有委托代码重复就会蔓延在这个系统中
     */
    /**
     * 案例：
     * 一个项目需要用户登录  和用户反馈
     * 可以分别创建 login.php  feedback.php 页面来处理 并且实例化专门的类来完成任务
     * 但是，系统中 用户界面 很难被精确定位  一一对应到 任务 系统  也就是，一个页面一般会处理很多功能  而不是单一的功能
     * 如果页面要处理很多不同的任务，就要考虑封装‘
     * 封装之后，面向系统增加新任务就会变得简单，并且可以将系统中的不同部分分离开来
     * 这时 就是命令模式 登场的时候
     *
     * 也就是说：命令模式  用来 处理，封装 一个页面 多个任务
     */
    //TODO:命令对象的接口很简单，只要实现一个 execute() 方法，Command可以被定义为接口也可以被定义为抽象类
    //TODO:定义成抽象类可以为它的扩展对象，提供有用的公共功能
    /**
     * Class Command
     * @package CommandProblem
     */
    abstract class Command{
        abstract public function execute(CommandContext $context);
    }
    class LoginCommnd extends Command {
        /**
         * @param CommandContext $context
         * @return bool
         * CommandContext 对象为参数， 可以将其描述为 RequestHelper
         * 通过 CommandContext 机制，请求数据可被传递给 Command 对象
         * 同时响应也可以被返回到视图层
         * 这种方法可以不破坏接口，就可以把不同的参数传递给命令对象
         * 从本质上说，CommandContext只是将关联数组变量包装成对象，  也会扩展它去执行其他任务
         */
        public function execute(CommandContext $context)
        {
            // TODO: 工厂方法
            $manager = Registry::getAccessManager();
            $user = $context->get('username');
            $pass = $context->get('pass');
            $user_obj = $manager->login($user, $pass);
            if(is_null($user_obj)){
                $context->setError($manager->getError());
                return false;
            }
            $context->addParam("user", $user_obj);
            return true;
        }
    }

    /**
     * 基本的请求帮助类
     * Class CommandContext
     * @package Command
     */
    class CommandContext{
        private $param = array();
        private $error = "";
        //初始化参数
        public function __construct()
        {
            $this->param = $_REQUEST;
        }
        //设置参数
        public function addParam($key, $val){
            $this->param[$key] = $val;
        }
        //获取参数
        public function get($key){
            return $this->param[$key];
        }
        //错误处理
        public function setError($error){
            $this->error = $error;
        }
        public function getError(){
            return $this->error;
        }
    }
    /**
     * CommandContext 参数数据中转站
     * LoginCommand  命令分发，调用具体逻辑
     * AccessManager 相应命令的具体逻辑类
     *
     * 通过 CommandContext 对象 LoginCommand 能够访问请求数据；提交用户名和密码
     * 上例中，使用 Registry 的一个静态方法  返回 LoginCommand 所需要的 AccessManager（处理登录的具体逻辑） 对象
     * 如果， AccessManager 中报告了一个错误， LoginCommand 把错误信息保存到 CommandContext 中 并在表现层中使用， 返回false
     *如果一切正常 返回 true
     *
     *Command 类不应该执行太多的逻辑，如果逻辑过多的出现在 Command 类中 就要考虑重构 Command 类
     *否则，这样的代码会导致重复，它不可避免的被不同的 Command 类赋值粘贴
     *至少要考虑这些逻辑功能，应该属于哪一部分代码，最好把这样的代码迁移到业务对象或者一个外观层中
     *
     *现在还缺少用于创建命令的类（客户端代码），
     *调用者类（使用命令的类）
     *在一个项目中，选择实例化那个命令对象最简单的方法，是根据请求本身的参数来决定
     */

    class CommandException extends \Exception {}

    /**
     * Class CommandFactory
     * @package Command
     *  CommandFactory 类再 目录 command 下面查找指定的文件
     * 文件中的类名  通过 数据中转类 CommandContext 的 $action 来构造  该参数是从 请求中传到系统的
     * 如果，文件和类都存在，就返回 一个对应的 命令对象（如：LoginCommnd） 给调用者
     *
     * 显然此方法可用于自建框架中，自建框架一定要做好顶层设计，想好用那些设计模式
     */
    //TODO:创建 命令者类  的类
    class CommandFactory{
        private static $dir = 'command';
        static public function getCommand($action = 'Default'){
            if(preg_match('/\w/', $action)){
                throw new \Exception('error');
            }
            //TODO:拼接类名. 不太高级的方法，，
            $class = UCfirst(strtolower($action))."Command";
            $file = self::$dir."/"."{$class}.php";  //按预先定义好的规则，拼接类文件名及其路径
            //文件不存在，抛出异常
            if(!file_exists($file)){
                throw new CommandException('文件不存在');
            }
            require_once ($file);   //加载类文件
            //拼接的类不存在。抛出异常
            if(!class_exists($class)){
                throw new CommandException('类不存在');
            }
            $cmd = new $class();
            return $cmd;

        }
    }
    //TODO:简单的调用者
    /**
     * Class Controller
     * @package Command
     * 就是一个控制器，不过里面实现的功能，是通过一定分发委托其他类完成
     */
    class Controller{
        private $context;

        /**
         * 初始化数据中转类
         * Controller constructor.
         */
        public function __construct()
        {
            //TODO:设计这些类是为了让分工更明确，降低耦合，提高内聚
            $this->context = new CommandContext();  //初始化 数据中转类
        }
        public function getContext(){
            return $this->context;
        }

        /**
         * 调用命令类处理请求
         */
        public function process(){
            //TODO:用请求参数构造一个  命令类实例
            $cmd = CommandFactory::getCommand($this->context->get('action'));
            //TODO:再用命令类的实例  分发处理 请求
            if(!$cmd->execute($this->context)){
                //处理失败
            }else{
                //处理成功
            }
        }
    }
    //TODO:使用
    $controller = new Controller();
    //伪造用户请求
    $context = $controller->getContext();
    $context->addParam('action', 'login');
    $context->addParam('username', 'daye');
    $context->addParam('password', '666');
    //TODO:请求处理
    $controller->process();
    /**
     * process() 方法将 实例化 命令类对象 的工作委托 给 CommandFactory::getCommand() 处理
     * 然后  在创建的 命令对象实例（LoginCommand）  中 调用 execute()  方法 响应具体的请求
     * 控制器类  Controller（调用者）  对 LoginCommand（命令对象） 内部实现 一无所知
     *命令响应的细节是独立的
     *因此，我们可以随时添加新的 命令者类  来响应不同的请求，并且对当前的结构影响很小
     */
    //TODO:这个类 一 类文件  FeedCommand.php 保存 在 目录 commands 下 ；它将被调用来处理 action = feed; 的请求
    //TODO：这样我们可以随意添加任意数量的 命令类 来响应 任意请求
    class FeedCommand extends Command {
        public function execute(CommandContext $context)
        {
            // TODO: Implement execute() method.
        }
    }
    /**
     * 总结：
     * 命令模式：
     * 把请求 映射 给 一个对应的  命令类
     * 控制器 通过 请求参数 来调用 相应的 命令类 来完成请求响应
     * CommandContext 作为 数据参数的中转存储中心
     * CommandFactory 根据 请求参数 构造对应的 命令类  的实例
     *
     * 很简单实用的模式，帮助建立具有扩展性的多层系统
     */
}
?>