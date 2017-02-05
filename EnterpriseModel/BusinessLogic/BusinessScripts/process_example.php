<?php
namespace Angular\Process;
class VenueManger extends Base {
    //todo:事物脚本要执行的sql语句，被构造成prepare接受的格式，“？”代表占位符，最终被传递给execute方法
    static $add_venue = "INSERT INTO `venue` (name) VALUES (?)";
    static $add_space = "INSERT INTO `space` (`name`, `venue`) VALUES (?,?)";
    //..........
    //todo:某个特定的业务需求例子
    /**
     * addVenue 方法中省去了很多数据库操作，交由基类实现
     * 把场所名传递给doStatement 方法，如果发生错误，将抛出一个异常
     * 改方法中并未抛出任何异常，所以doStatement 或 prepareStatement 方法抛出的异常将会被 addVenue 方法抛出
     *
     *
     * @param $name
     * @param $space_array
     * @return array
     */
    public function addVenue($name, $space_array){
        $ret = array();
        $ret['venue'] = array($name);
        $this->doStatement(self::$add_space, $ret['venue']);    //预处理sql
        $v_id = self::$db->lastInsertId();
        $ret['soace'] = array();
        foreach ($space_array as $space_name){
            $value = array($space_name, $v_id);
            $this->doStatement(self::$add_space, $value);
            $s_id = self::$db->lastInsertId();
            array_unshift($value, $s_id);
            $ret['space'][] = $value;
        }
        return $ret;
    }
}
/**
 * 效果：
 * 事物脚本模式是快速获得结果的有效途径，也是经常被用但是没命名的模式之一
 * 通过添加到基类中的几个助手方法，可以关注应用逻辑而不需要花太多的时间在数据的存取上
 *
 * 大多数情况下，在开发小型项目时使用事物脚本，特别是当你确定它不会成长为一个大项目的情况下
 *
 * 这种方式不容易扩展，因为事物脚本总是不肯避免的相互渗透，从而导致代码重复
 *
 * 另外，应该把数据库的操作和应用逻辑分离开；
 * 奖数据库操作从事物脚本中提取出来，然后创建一个入口类让事物脚本与数据库进行交互
 */
?>