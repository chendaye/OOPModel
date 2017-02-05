<?php
/**
 * AppController 类需要使用 Request 对象中存储的之前执行过的命令，此工作由Command 基类来完成
 */
namespace Angular\Command;
use Angular\Request\Request;

/**
 * Command 类定义了一个状态字符串数组
 * statuses() 方法将一个字符串转换为相应的数字
 * getstatus() 方法返回当前 Command 对象的状态标识
 *
 * 更严格一点： 可以让statuses() 方法在失败时抛出异常，实际上如果元素未定义
 * statuses() 将默认返回 null
 * execute() 方法使用抽象方法 doExecute() 返回的值来设置状态标识，并将它它缓存到 Request 对象中
 *
 * Class Command
 * @package Angular\Command
 */
abstract class Command{
    private static $STATUS_STRINGS = array(
        'CMD_DEFAULT' => 0,
        'CMD_OK' => 1,
        'CMD_ERROR' => 2,
        'CMD_INSUFFICIENT_DATA' =>3
    );
    private $status = 0;
    final public function __construct(){}   //不能被子类重写

    /**
     * 执行命令
     * @param Request $request
     */
    public function execute(Request $request){
        $this->status = $this->doExecute($request);
        $request->setCommand($this);
    }

    /**
     * 状态
     * @return int
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * @param string $str
     * @return mixed
     */
    static public function statuses($str = 'CMD_DEFAULT'){
        if(empty($str)){
            $str = 'CMD_DEFAULT';
            //将字符串转换为状态数
            return self::$STATUS_STRINGS[$str];
        }
    }

    /**
     * 命令的具体执行
     * @param Request $request
     * @return mixed
     */
    abstract public function doExecute(Request $request);
}

/**
 * 一个具体的Command 类
 */
class  AddVenue extends Command {
    public function doExecute(Request $request){
        $name = $request->getProperty("venue_name");
        if(!$name){
            $request->getFeedback("no name provided");
            return self::statuses('CMD_INSUFFICIENT_DATA');
        }else{
            $venue_obj = new \Angular\Domian\Venue(null, $name);
            $request->setObject('venue', $venue_obj);
            $request->addFeedback("{$name} added {$venue_obj->getId()}");
            return self::statuses('CMD_OK');
        }
    }
}
?>
<?php
namespace \Angular\Domain;
class Venue{
    private $id;
    private $name;
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }
    public function getId(){
        return $this->id;
    }
}

/**
 * doExecute()方法返回一个状态标识，而抽象基类将状态标识保存在属性中
 * 命令对象在被调用并设置了状态标识后，系统如何响应，完全取决于配置文件（XML）
 * 例如：如果返回 CMD_OK 转向机制会促使 AddSpace 类被实例化
 * 当请求包含 cmd=AddVenue 时，事件链就会被触发
 * 如果请求包含 cmd=QuickAddVenue 则转向不会发生，而直接显示 quickaddvenue 视图
 *
 * 应用控制器的效果：
 * 要实现一个完整的应用控制器例子很困难，因为要做很多工作
 * 包括得到 应用元数据来描述命令与请求、命令与命令、命令与视图之间的关系
 *
 * 我们只有在项目需要的时候才这么做
 * 当在命令中添加条件语句来加载不同的视图或者调用不同的命令时，如果觉得命令和视图的逻辑不太容易控制
 * 才会想到使用应用控制器
 *
 * 注意：不要为了使用而使用， 使用是来解决问题的，不是创造问题的，当然练习除外
 *
 * 应用控制器也可以使用各种机制来创建命令与视图的关系，不仅限于例子中的机制‘
 * 即使请求的的字符串，命令，和视图的关系是固定的，仍然可以从构建应用控制器中受益
 * 这样会给程序带来更大的灵活性，便于在将来需求复杂的时候重构代码
 *
 * 总结：
 * 应用控制器的关键在于  命令和视图  命令和命令 的处理机制
 */
?>
