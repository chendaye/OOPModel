<?php
/**
 * 在查找领域对象时，之前的映射器实现方案有点不太灵活
 * 查找一个对象没问题，查找相关的所有领域对象也简单
 * 但是如果要查找符合特定条件的对象，就要添加一个类方法来构造查询语句
 * 标识对象封装了查询条件，从而解除了系统与数据库语法之间的耦合
 *
 * 问题：
 * 通常很难知道需要查询数据库中的那些内容；领域对象越复杂，数据库查询时使用的限定条件就越多
 * 这就需要根据要求往映射器中添加多个类方法，这可以在一定程度上解决这个问题
 * 但是这个不太灵活，而且会导致代码重复
 * 标识对象封装了数据库查询中的条件，这样不同的组合可以在运行时构成不同的数据库查询
 * 通过条件的自由组合可以让数据库查询有一定的灵活性
 *
 * 实现：
 * 一般，标识对象包含一系列的类方法，可以调用它们构建各种查询条件
 * 设置标识对象的状态后，可以将它传递到一个负责构建SQL语句的类方法中
 * 可以使用基类来管理日常的操作，并保证多个标志对象都属于同一个类型
 */
namespace Angular\Mapper;
class IdentityPbject{
    private $name = null;
    public function setName($name){
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }
}

/**
 * 类的作用就是将数据储存起来然后在请求的时候在取出
 * Class EventIdentityObject
 * @package Angular\Mapper
 */
class EventIdentityObject extends IdentityPbject {
    private  $start = null;
    private $minstart = null;
    public function setMinimumStart($minstart){
        $this->minstart = $minstart;
    }
    public function getMinimumStart(){
        return $this->minstart;
    }
    public function setStart($start){
        $this->start = $start;
    }
    public function getstart(){
        return $this->start;
    }
}

//使用SpaceIdentyObject来构建WHERE句子
$idobj = new EventIdentityObject();
$idobj->setMinimumStart(time());
$idobj->setName('A');
$comps = array();
$name = $idobj->getName();
if(!is_null($name)){
    $comps[] = "name = '{$name}'";
}
$minstart = $idobj->setMinimumStart();
if(!is_null($minstart)){
    $comps = "start > {$minstart}";
}
$start = $idobj->getstart();
if(!is_null($start)){
    $comps[] = "start = '{$start}'";
}
$clause = "WHERE ".implode(" and ", $comps);

/**
 * 这个模型可以很好的工作，但那时不是很符合延迟的精神
 * 对于一个较大的领域对象来说，编写大量的getter setter 方法很麻烦
 * 在此模型中，你需要编写代码将WHERE语句中的每种条件都输出
 */
?>