<?php
/**
 * 问题：
 *一条SQL语句，在同一次请求中重复保存了同一条数据
 * 系统使用组合的命令来搭建的，结构良好；这意味着一条命令肯被多次调用，
 * 而且每次调用都要执行清理工作
 * 不仅保存了同一个对象两次，而且保存了原本没必要保存的对象
 *
 * 实现：
 * 要判断哪些数据库操作是必须的，需要跟踪与对象相关的各种事件，跟踪操作最好放在跟踪对象中
 * 现在需要一个对象列表，找出每个数据库操作需要的对象
 */
class ObjectWatch_{
    private $all = [];
    private $dirty = array();
    private $new = array();
    private $delete = array();
    private static $instance;
    public static function addDelete(\Angular\Domain\DomainObject $obj){
        $self = self::instance();
        $self->delete[$self->globalKey($obj)] = $obj;
    }
    public static function addDirty(\Angular\Domain\DomainObject $obj){
        $inst = self::instance();
        if(!in_array($obj, $inst->new, true)){
            $inst->dirty[$inst->globalKey($obj)] = $obj;
        }
    }
    public static function addNew(\Angular\Domain\DomainObject $obj ){
        $inst = self::instance();
        $inst->new[] = $obj;
    }
    public static function addClaen(\Angular\Domain\DomainObject $obj){
        $self = self::insatnce();
        unset($self->delete[$self->globalKey[$obj]]);
        unset($self->dirty[$self->globalKey[$obj]]);
        $self->new = array_filter($self->new, function($a) use ($obj){ return !($a === $obj);});
    }
    public function performPperations(){
        foreach ($this->dirty as $key=>$obj){
            $obj->finder()->update($obj);
        }
        foreach ($this->new as $key=>$obj){
            $obj->finder()->insert($obj);
        }
        $this->dirty = array();
        $this->new = array();
    }
}
/**
 * ObjectWatcher 类仍然是一个标识映射，但是增加了跟踪系统中所有对象的功能
 * 通常当对象从数据库中取出，然后被修改，我们就说对象脏了
 * 脏对象呗保存在$dirty 数组属性中，知道更新数据库
 * 客户端代码可以自行决定脏对象是否会触发数据库更新
 * 把脏对象识别为“干净”的，通过addClean()方法，这样数据库就不会被更新
 * 新创建的对象会被添加到new数组中，该数组的对象将会被插入到书库中
 *
 * addDirty()方法和addNew()方法都要添加一个对象到它们各自的数组属性中
 * addClean()方法则从$dirty数组中删除一个对象，表示该对象不需要被更新
 *
 * ObjectWatcher类提供了一个更新和添加对象到数据库的机制，但代码中并没有实现添加对象到ObjectWatcher中的功能
 *
 * 由于是在这些对象上操作，由这些对象来执行通知和合适
 * 以下是一些具体的工作方法
 */
//namespace Angular\Domain
//DomainObject
abstract class DomainObject_{
    private $id = -1;
    public function __construct($id = null)
    {
        if(is_null($id)){
            $this->markNew();
        }else{
            $this->id = $id;
        }
    }
    public function markNew(){
        ObjectWatcher::addNew($this);
    }
    public function markDelete(){
        ObjectWatcher::addDirty($this);
    }
    public function markClean(){
        ObjectWatcher::addClean($this);
    }
    public function setId($id){
        $this->id = $id;
    }
    public function getID(){
        return $this->id;
    }
    public function finder(){
        return self::getFinder(get_class($this));
    }
    public static function getFinder($type){
        return HelperFactory::getFinder($type);
    }
}
/**
 * Domain 类存在着 finder()方法和 getFinder() 方法
 * 它们的工作方式与collection() 及 getCollection() 一样
 * 查询一个简单的工厂类 HelperFactory 来获取需要的映射器对象
 *
 * 还需要在Mapper中增加一些代码
 */
//Maper
function createObject($array){
    $old = $this->getFormMap($array['id']);
    if($old){
        return $old;
    }
    $obj = $this->doCreateObject($array);
    $this->addToMap($obj);
    $obj->markClean();
    return $obj;
}

/**
 * 创建对象的工作包括通过在构造方法中调用ObjectWatcher::addNew()将其标记为新
 * 因此必须调用markClean(),否则从数据库中取出的每个对象在请求结束时都会被保存
 * 这不是我们想要的
 *
 * 确保被各个类方法修改过的对象标记为脏对象是很重要的
 * 高层次的控制器对象通常会调用performOperetions()
 * 所以只需要创建或修改一个对象，然后单元类（ObjectWatcher）只会在请求结束时执行一次
 */
//Spadce 对象中调用markDirty()的一些方法
class Space extends \Angular\Domain\DomainObject {
    public function setName($name_s){
        $this->name = $name_s;
        $this->markDirty();
    }
    public function setVenue(Venue $venue){
        $this->Venue = $venue;
        $this->markDirty();
    }
}

/**
 * 效果：
 * 这个模式很有用，但必须保证所有被修改过的对象都被标记为脏
 * 否则或导致一些难以发现和解决的bug
 */
?>