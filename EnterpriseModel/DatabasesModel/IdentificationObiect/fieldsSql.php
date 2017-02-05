<?php
/**
* 事实上有多种方法来得到数据并生成sql
* 例如：在基类中给字段名关联数组赋值，通过比较给自己添加索引：大于 小于 等于。
* 子类提供便捷的方法来添加数据带底层的数据结构中
* 然后sql构建器可以遍历数组来动态构建查询，实现这样一个系统有很多途径
* 希望客户端有一个灵活的途径来定义条件
*/
namespace Angular\Mapper;
/**
 * 这个类接受并且保存字段名
 * 可以对单个字段进行多个对比测试
 * Class Field
 * @package Angular\Mapper
 */
class Field{
    protected $name = null;
    protected $operator = null;
    protected $incomplete = false;
    //todo:设置字段名
    public function __construct($name)
    {
        $this->name = $name;
    }
    //todo：添加操作符和值
    public function addTest($operator, $value){
        $this->comps[] = array('name' => $this->name, 'operator' => $this->operator, 'value' => $value);
    }
    //todo:comps是一个数组，因此有多个方法来检查字段
    public function getComps(){
        return $this->comps;
    }
    //todo:如果$comps为空，则有比较数据并且字段不能用于数据库查询
    public function isIncomplete(){
        return empty($this->comps);
    }
}

//新的IdentityObject类
class newIdentityObject{
    protected $currentfield = null;
    protected $fields = array();
    private $and = null;
    private $enforce = array();
    public function __construct($field = null, array $enforce = null)
    {
        if(!is_null($enforce)){
            $this->enforce = $enforce;
        }
        if(!is_null($field)){
            $this->fields = $field;
        }
    }
    //todo:需要的字段名称
    public  function getObjectFields(){
        return $this->enforce;
    }

    /**
     * 使用一个新字段
     * 如果当前字段不完整，抛出错误
     * 本方法返回当前对象的引用；因此可以使用链式调用
     * @param $fieldname
     * @return $this
     * @throws \Exception
     */
    public function field($fieldname){
        if(!$this->isVoid() && $this->currentfield->isIncomplete()){
            throw new \Exception('no fields');
        }
        $this->enforceFied($fieldname);
        if(isset($this->fields[$fieldname])){
            $this->currentfield = new Field($fieldname);
        }else{
            $this->currentfield = new Field($fieldname);
            $this->fields[$fieldname] = $this->currentfield;
        }
        return $this;

    }
    //todo:标识对象是否已经设置了字段
    public function isVoid(){
        return empty($this->fields);
    }
    //todo:传入字段是否合法
    public function enforceField($fieldname){
        if(!in_array($fieldname, $this->enforce) && !empty($this->enforce)){
            $forcelist = implode(', ', $this->enforce);
            throw new \Exception('字段不合法');
        }
    }
    //todo:相等操作
    public function eq($value){
        return $this->operator("=", $value);
    }
    //todo:小于
    public function lt($value){
        return $this->operator("<", $value);
    }
    //todo:大于
    public function gt($value){
        return $this->operator(">", $value);
    }
    //todo;操作符方法是否得到当前字段变添加了操作符和测试值
    private function operator($symbol, $value){
        if($this->isVoid()){
            throw new \Exception('没有定义字段');
        }
        $this->currentfield->addTest($symbol, $value);
        return $this;   //返回对象的引用
    }
    //todo:以关联数组形式返回目前创建的所有对比
    public function getComps(){
        $ret = array();
        foreach ($this->fields as $key => $fie){
            $ret = array_merge($ret, $fie->getComps());
        }
        return $ret;
    }
}
//使用
$idobj = new newIdentityObject();
$idobj->field("name")->eq('a')->field('start')->gt(time())->lt(time());
/**
 * 先创建了一个标识对象，然后调用add()创建一个Field对象并将其复值给$currentfield属性
 * add()方法返回了对identity对象的引用，这样就可以在调用add()方法之后接着调用其它方法
 * 比如：eq() gt(); 同样这些方法会返回对象的引用
 * 这样就可以继续添加测试或者再次调用add()方法来操作一个新的字段
 *
 * 通过这种方式代码比较简洁，但是安全性也会降低
 * 这也是$enforce数组的设计目的；子类可以使用一定的限制条件调用基类的构造方法
 */
class EventIdebtityObject extends IdentityPbject {
    public function __construct($field = null)
    {
        parent::__construct($field, array('name', 'id', 'stat'));
    }
}
/**
 * EventIdebtityObject类现在限制了几个字段
 * 如果随便访问就会出现错误
 *
 * 效果：
 * 标识对象允许客户端程序员定义各种sql查询语句，而不需要直接使用数据库查询
 * 同时，使用标志对象也可以不用针对特定的数据库查询构建各种方法
 * 标识对象的目的在于对用户隐藏数据库细节，这很重要
 * 因此如果创建了一个自动化的解决方案，就像例子中使用流畅接口一样
 * ，你使用的标签应该明确指向你的领域对象，而不是列名
 * 当领域对象与列名不一致时，需要提供一个使用别名的机制
 *
 * 当使用特定的实体对象时，对每个领域对象来说，使用抽象工厂是很有用的，这可以为他们提供其他领域对象的相关对象
 */
?>