<?php
namespace Angular\Mapper;
/**
 * 同样这个类也是一个抽象类，定义了公共接口
 * newSelect() 的参数是IdentityObject
 * 工具方法buildWhere()的参数也是IdentityObject()
 * buildWhere()方法通过IdentityObject::getComps()来获取所需的信息
 * 用于创建一个WHERE子句并构建一系列值
 * 不论是创建WHERE子句还是构建值，该方法都返回一个包含两个元素的数组
 * Class SelectFactory
 * @package Angular\Mapper
 */
abstract class SelectFactory{
    abstract public function newSelect(IdentityPbject $obj);
    public function buildWhere(IdentityPbject $obj){
        if($obj->isVoid()){
            return array("", array());
        }
        $compstrings = array();
        $values = array();
        foreach ($obj->getComps() as $comp){
            $compstrings[] = "{$comp['name']} {$comp['operator']} ?";
            $values[] = $comp['value'];
        }
        $where = "WHERE ".implode(" AND ", $compstrings);
        return array($where, $values);
    }
}
/**
 * 一个具体的SelectionFactory类
 */
class VenueSelectionFactory extends SelectFactory {
    public function newSelect(IdentityPbject $obj)
    {
        // TODO: Implement newSelect() method.
        $fields = implode(',', $obj->getObjectFields());
        $core = "SELECT $fields FROM venue";
        list($where, $values) = $this->buildWhere($obj);
        return array($core." ".$where, $values);
    }
}
/**
 * 这个类创建了核心的SQL语句，然后调用buildWhere()添加条件子句
 * 实际上两个SelectFactory 的区别就在于它们创建的SQL语句中数据表名称不同
 * 如果没有特定的需要也可以把两个类合并成一个 SelectionFactory
 * SelectionFactory会查询PersistenceFactory指定的数据表
 *
 * 效果：
 * 如果使用通用的标识对象，那么使用参数化的SelectionFactory类就更简单
 * 如果要硬编码标识对象，即包含一系列getter setter标识对象，就必须为每一个领域对象
 * 实现一个SelectionFactory类
 * 结合使用标识对象和查询语句工厂的最大好处之一就是可以生成各式各样的查询语句
 * 但这样会导致缓存问题
 * 这些方法计时生成查询，重复的劳动次数不计其数
 * 可以考虑设计一种比较不同的标识对象的机制，这样可以从缓存中方便的获取需要的字符串
 * 同时可以考虑从更高的层次使用数据库语句池
 *
 * 组合使用这些模式的 一个问题就是，这些模式是灵活的，但是不足够灵活
 * 它被设计为适用各种限制条件，没有设计任何特殊情况
 * 映射器的创建和维护有点麻烦，但是其在干净的API后面，却是最适合进行性能调节和数据处理的地方
 * 这些模式都致力于它们自己的职责，强调通过组合来使用
 * 这样就很难走捷径实现一些强大但是不优雅的功能
 */
?>