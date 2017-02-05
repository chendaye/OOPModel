<?php
/**
 * 存储配置数据：
 * 上节需要缓存的是 ControllerMap ,它其实是将三个数组封装而成
 * 我们可以直接使用数组，但使用 ControllerMap 更保险，可以确保每个数组都遵循一定的格式
 */
namespace Angular\Controller;
class ControllerMap{
    private $viewMap =array();
    private $forwardMap = array();
    private $classrootMap = array();

    /**
     * 存取配置数据
     * @param $command
     * @param $classroot
     */
    public function addClassroot($command, $classroot){
        $this->classrootMap[$command] = $classroot;
    }
    public function getClassroot($command){
        if(isset($this->classrootMap[$command])){
            return $this->classrootMap[$command];
        }
        return $command;
    }
    public function addView($command = 'default', $status = 0, $view){
        $this->viewMap[$command][$status] = $view;
    }
    public function getView($command, $status){
        if(isset($this->viewMap[$command][$status])){
            return $this->viewMap[$command][$status];
        }
        return null;
    }
    public function addForward($command, $status = 0, $newCommand){
        $this->forwardMap[$command][$status] = $newCommand;
    }
    public function getForward($command, $status){
        if(isset($this->forwardMap[$command][$status])){
            return $this->forwardMap[$command][$status];
        }
        return null;
    }
}
/**
 * $classroot 属性是一个将命令句柄（配置文件中命令元素的名称）映射到Command 类名称的关联数组
 * 类名中去除了前缀，例如：Angular_Command_AddVenue 类在此的名称为 AddVenue
 * 这样可以坚持一个cmd参数是一个别名还是一个特定的类文件
 * 在解析配置文件的时候， addClassroot()会被用来为数组赋值
 *
 * $forwardMap 和 $viewMap 都是二维数组，用于将命令和状态都组合起来
 */
?>
<!--
例如一下片段
-->
<command name="AddVenue">
    <view>addVenue</view>
    <status value="CMD_OK">
        <forward>AddSpace</forward>
    </status>
</command>
<?php
//todo:解析时会添加正确的元素到 $viewMap 属性中
$map = new ControllerMap();
$map->addView('AddVenue', 0, 'addvenue');
//todo:同时也会为 $forwardMap() 属性赋值
$map->addForward('AddVenue', 0, 'AddSpace');
/**
 * 应用控制器会依次执行这些操作：
 * 例如， AddVenue 返回 CMD_OK 其值为 1；  返回 CMD_DEFAULT 值为 0
 * 应用控制器就会在 $forwardMap() 数组中查找相应的元素（先找特定的 Command和状态标识  然后找 一般的 Command和状态标识）
 * 找到的第一个匹配元素会被返回
 *
 * 事实上就是 一个 命令的组成要素 用 XML 来表现出来；
 * 因为 XML 比数组易于读，也容易解析
 */
/*
 $viewMap['AddVenue'][1];   //AddVenue CMD_OK
$viewMap['AddVenue'][0];    //AddVenue CMD_DEFAULT
*/
?>