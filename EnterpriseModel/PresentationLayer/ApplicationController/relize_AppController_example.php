<?php
/**
 * 应用控制器实现：
 * 与其他模式一样，应用控制器的关键是接口，
 * 应用控制器是一个类或者一组类，  前端控制器   可以用 应用控制器  来获取 命令
 * 并在执行命令后找到合适的视图来呈现给用户
 *
 * 本章模式的目标都是尽可能的简化客户端可操作的应用入口，也就是前端控制器
 * 虽然必须在接口背后部署一个实现（方案很多），
 * 但本模式的关键在于模式中的参与者（控制器， 命令， 视图），相互通信的方式，而非细节
 */
//TODO:前端控制器 FrontController  如何使用 应用控制器 AppController
function handleRequest(){
    $request = new \Angular\Request\Request();
    $app_c = \Angular\ApplicationRegistry::appController();
    while($cmd = $app_c->getCommand($request)){
        $cmd->execute($request);    //命令对象
    }
    $this->invokeView($app_c->getView($request));
}
function invokeView($targe){
    include ('Angular/view/'.$targe.'.php');
}
/**
 * 上面的例子和之前的前端控制器例子不同之处在于 Command 对象是再一个循环中被获取并被执行
 * 本例也使用 AppController 来获得它要包含的视图名称
 * 使用了注册表来获取AppController 对象
 */

/**
 * 实现概述：
 * 不同的操作阶段，Command 类可能需要加载不同的视图
 * AddVenue 命令的默认视图可以是一个数据输入表单。如果用户添加错误类型的数据，可能要重新显示表单
 * 或者显示一个错误页面
 * 如果一切顺利，场所将会被添加到系统中，然后会进入命令链的下一环节
 *
 * Command 对象通过设置状态标识，告诉系统他们当前的状态
 * 状态标识在 Command 父类中被定义为属性，可以被所有 Command 类识别
 *
 * 应用控制器通过Request 对象找到并实例化正确的 Command 类
 * 一旦 Command 对象开始运行，机会呗设置为某种状态
 * 然后  Command 和 状态 被用来与一个数据结构做比较，来决定接下来执行哪个命令
 * 如果不需要执行命令，就加载对应得视图
 */
abstract class Command{     //定义为final，任何子类都不能覆盖父类的构造方法,所有子类都不需要参数
    private static $STATUS_STRING = array(  //状态标识，私有的 静态的
        'CMD_DEFAULT' => 0,
        'CMD_OK' => 1,
        'CMD_ERROR' => 2,
        'CMD_INSUFFICIENT' => 3
    );
    public final function __construct(){}
    public function execute(Request $request){
        $this->doExecute($request); //分发请求
    }
    abstract public function doExecute(Request $request);  //具体处理请求
}
?>
//TODO:配置文件
/**
 * 系统管理员可以通过设置配置选项来决定命令和视图的工作方式
 */

<control>
    <view>main</view>
    <view status="CMD_OK">main</view>
    <view status="CMD_ERROR">error</view>

    <command name="ListVenue">
        <view>listvenues</view>
    </command>
    <command>
        <classroot neme="AddVenue" />
        <view>quickadd</view>
    </command>
    <command name="AddVenue">
        <view>addvenue</view>
        <status value="CMD_OK">
            <forward>AddSpace</forward>
        </status>
    </command>
    ......
</control>
<!--
这段简化的xml 展示了从 Command 类中抽取命令流及他们视图间关系的一种办法
配置选项都包含在一个 control 元素中，
这个的原理是基于查找：
最外面定义的是通用元素，它们可以被 command 元素内部等效的元素覆盖

第一个元素view定义了可用于所有命令的默认视图
如果没有指定的视图，默认视图就会被调用；同级view 元素声明了status属性，该属性 Command 类中的状态标识相对于
每种状态都用 Command 对象设置的一个标识来描述，以此来表明任务当前的执行阶段
同时，由于这些 view 元素比第一个view 元素指定了一个具体的内容，所以优先级更高
 如果一个命令设置了状态标识CMD_OK 则相应的menu视图会被加载，除非有一个更具体的元素覆盖它

 设置这些默认的值之后，文档接下来显示 command 元素，默认情况下这些元素直接映射到 Command 类
 ,以及它们在文件系统中的类文件；就如之前的 CommandResolver 那样
 因此，如果cmd 参数被设置成 AddVenue, 那么配置文件中的对于元素就会被选中
 字符串 AddVenue 就会被组合成指向 类文件  AddVenue.php 的路径
-->
<!--
配置文件还支持别名：
 如果 cmd 被设置成 QuickAddVenue ,那么下面的文件会被使用

 被命名为 QuickAddVenue 的command 元素并没有直接映射到一个类文件
 真正的映射由 classroot  元素定义，这样可以在不同的流 和视图中引用 AddVenue 类
-->
<command name="QuickAddVenue">
    <classroot name="AddVenue" />
    <view>quickadd</view>
</command>

<!--
command 元素从外到内生效， 内部元素优先级较高
通过在command元素内部设置一个 view 元素，可以把该 view 绑定到命令

addvenue 这个视图与 AddVenue 命令相关联
当AddVenue 命令被调用的时候 addvenue.php 视图总会被加载，除非status条件被匹配，此时会调用其他的视图
status元素可以只包含另一个视图来取代默认视图，下面的配置文件中， forward元素指定的不是视图，而是命令
通过转向另一个命令AddSpace ,把配置视图的任务交给命令来执行
-->
<command>
    <view>addvenue</view>
    <status value="CMD_OK">
        <forward>AddSpace</forward>
    </status>
</command>

<?php
//TODO:解析配置文件
/**
 * 上述的xml配置相当灵活，易于控制内容显示和命令的处理流程
 * 但是，不想每次请求都解析以此配置文件
 * 对此， ApplicationHelper 提供了将配置数据缓存起来的功能
 */
// ApplicationHelper 的部分代码
function getOptions(){
    $this->ensure(file_exists($this->config), "文件不存在");
    $options = @SimpleXml_load_file($this->config);
    //..........设置DSN...........
    $map = new ControllerMap();
    foreach ($options->control->view as $default_view){
        $stat_str = trim($default_view['status']);
        $status = \Angular\Command\Command::status($stat_str);
        $map->addView('default', $status, (string)$default_view);
    }
    //...........省略解析代码..............
    \Angular\ApplicationRegistry::setControllerMap($map);
}
?>