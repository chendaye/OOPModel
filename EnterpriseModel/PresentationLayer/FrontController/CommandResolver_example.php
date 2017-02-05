<?php
/**
 * 控制器需要通过某种策略来决定如何解释一个HTTTP请求，然后调用正确的代码来满足这个请求
 * 可以在 Controller 类中实现这个逻辑；但用一个特定的类来处理更容易重构和实现多态
 *
 * 前端控制器通常通过运行一个 Command 对象来调用应用逻辑
 * Command 通常根据请求中的参数或url结构来决定选择哪个命令
 * 可以通过Apache配置来确定url中的那个字段用于现在命令
 *
 * 有多种方法可以用来根据给定的参数选择命令
 * 逻辑方案：在一个配置文件或数据结构中测试参数
 * 物理方案：直接查找文件系统，看是否有对应的类文件
 */
namespace Angular\Command;
use Angular\Controller\Request;

/**
 * 这个类用于查找请求中包含的cmd参数
 * 如果参数被找到，且命令目录中有类文件相匹配，且正好包含cmd类，就返回相应类的实例
 * 如果其中任意条件未被满足，就返回默认的 Command类
 * Class CommandResolver
 * @package Angular\Command
 */
class CommandResolver{
    private static $base_cmd;
    private static $default_cmd;
    public function __construct()
    {
        if(!self::$base_cmd){
            self::$base_cmd = new \ReflectionClass('Command');  //利用反射初始化。提供安全性
            self::$default_cmd = new DefaultCommand();
        }
    }
    public function getCommand(Request $request){
        $cmd = $request->getProperty('cmd');
        $sep = '\\';
        if(!$cmd){
            return self::$default_cmd;  //返回默认命令
        }
        $cmd = str_replace(array('.',$sep), "", $cmd);
        $filepath = "Angular.$sep.Command.$sep.$cmd.php";   //物理实现
        $classname = "Angular\\Command\\$cmd";  //动态拼接类名
        if(file_exists($filepath)){ //通过命令判断文件存在否
            @require_once ($filepath);  //加载文件
            if(class_exists($classname)){   //判断类是否存在
                $cmd_class = new \ReflectionClass($classname);  //通过反射检查类型
                if($cmd_class->isSubclassOf(self::$base_cmd)){  //检查类型，反射
                    return $cmd_class->newInstance();   //满足要求返回一个实例
                }else{
                    $request->addFeedback("conmand $cmd is not a command"); //不满足给出提示
                }
            }
            $request->addFeedback("command is not found");  //若文件不存在给出提示
            return clone self::$default_cmd;    //返回默认的命令
        }
    }
}
?>