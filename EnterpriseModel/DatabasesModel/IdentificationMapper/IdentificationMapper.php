<?php
/**
 * 按值传递的问题：原以为两个对象指向同一个对象，而实际上是中性两个很相似的两个不同对象
 * 映射器中也有类似的问题
 * 添加到数据库的对象也可以通过映射器来得到，并且两个对象是完全等同的
 * 代码中新的Venue 对象赋值给旧的Venue,把旧对象覆盖
 * 有时候同一个请求中，可能多次引用同一个对象
 * 如果修改了该对象的某个版本并保存到数据库中，要确保修改不会被覆盖
 *
 * 实现：
 * 一个标识映射只是一个对象，它的任务是跟踪系统中的所有对象，并帮助系统避免将一个对象看成两个对象
 * 它赋值管理所有对象的信息
 */
$venue = new Venue();
$venue->setName("A");
$venue->setName("B");
?>

<?php

/**
 * 使用标识映射器的技巧在于如何标识是个对象，也就是如何给对象贴上标签
 * 可以用数据库保存一个全局键表，每当创建一个对象时就遍历该表，并将一个全局键
 * 这里采用的是更简单的方法：将数据表ID和类名连接起来
 * Class ObjectWatcher
 *
 * 效果：
 * 如果在在创建对象的时候使用了标识映射，系统中出现对象重复的可能性就为0
 * 当然，这只对当前进程有效，不同进程不可避免的会在同一时间访问同一个对象的不同版本
 * 有时候要考虑并发访问可能会导致的冲突和数据损坏
 * 如果问题严重可能需要采取一定的 锁 策略（类似数据库）
 * 可以考虑将对象保存到共享内存或者一个对外部对象缓存系统
 */
class ObjectWatcher{
    private $all = array(); //储存添加的对象
    private static $insatance;
    private function __construct(){}    //单例

    /**
     * 单例
     * @return ObjectWatcher
     */
    public static function instance(){
        if(!self::$insatance){
            self::$insatance = new ObjectWatcher();
        }
        return self::$insatance;
    }

    /**
     * 给每一个对象创造指定键值
     * @param \Angular\Domain\DomainObject $obj
     * @return string
     */
    public function globalKey(\Angular\Domain\DomainObject $obj){
        $key = get_class($obj).'.'.$obj->getId();
        return $key;
    }

    /**
     * 添加新的对象
     * @param \Angular\Domain\DomainObject $obj
     */
    public static function add(\Angular\Domain\DomainObject $obj){
        $inst = self::instance();
        $inst->all[$inst->globalKey($obj)] = $obj;
    }

    /**
     * 判断对象是否存在
     * @param $classname
     * @param $id
     * @return mixed|null
     */
    public static function exists($classname, $id)
    {
        $inst = self::instance();
        $key = $classname.$id;
        if(isset($inst->all[$key])){
            return $inst->all[$key];
        }
        return null;
    }
}

?>
