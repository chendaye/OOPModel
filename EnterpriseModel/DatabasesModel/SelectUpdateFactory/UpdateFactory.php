<?php
/**
 * 映射器的职责，如果使用了合适的模式，映射器类就不需要创建对象或者集合
 * 使用标识对象来处理搜索条件后，映射器类不再需要实现多种find方法
 * 下一个目标是将创建数据库查询语句的责任从映射器中剔除
 *
 * 问题：
 * 任何系统要和数据库打交道，就一定要使用数据库查询语句
 * 但是系统本身由领域对象和业务规则而不是数据库组成
 * 本章大多数模式是可以为树状的领域结构和表格是数据库搭建一座桥梁
 * 在将领域数据转化为数据库可以理解的格式时，就需要进行解耦
 *
 * 实现：
 * 在数据映射器模式中，已经见过此功能
 * 在本章中可以见识到标识对象带来的好处它可以更动态的生成查询语句，因为各种查询条件组合的可能性特别多
 * 选择工厂和更新工厂通常是与系统中领域对象平行组织的，这可能以标识对象为媒介
 * 鉴于此，它们也可能作为 PersistenceFactory
 * 抽象工厂作为领域对象执行持久化操作的场所
 */
namespace Angular\Mapper;
use Angular\Domain\DomainObject;

abstract class UpdateFactory{
    public abstract function newUpdate(DomainObject $obj);
    protected function buildStetement($table, array $fields, array $conditions = null){
        $terms = array();
        if(!is_null($conditions)){
            $query = "UPDATE {$table} SET ";
            $query .= implode(" = ?, ", array_keys($fields))." = ? ";
            $terms = array_values($fields);
            $cond = array();
            $query .= " WHERE ";
            foreach ($conditions as $key => $val){
                $cond[] = "$key = ?";
                $terms = $val;
            }
            $query .= implode(" AND ", $cond);
        }else{
            $query = "INSERT INTO {$table} (";
            $query .= implode(",", array_keys($fields));
            $query .= ") VALUES (";
            foreach ($fields as $name => $val){
                $terms[] = $val;
                $qs[] = "?";
            }
            $query .= implode(",", $qs);
            $query .= ")";
        }
        return array($query, $terms);
    }
}
/**
 * 从接口角度看，这个类做的唯一工作是定义newUpdate() 方法
 * 该方法会返回一个查询字符串数组以及要使用的值
 * buildStatement() 方法负责执行创建sql语句的功能
 * 在子类中可以具体实现满足不同标识对象的需要
 * buildStatement() 方法的参数是 表名 包含字段名以及查询条件相关数组
 * 该方法将三个条件组合起来构建一个sql  UPDATE 语句
 */
//todo:一个具体的更新工厂
class VenueUpdateFactory extends UpdateFactory{
    public function newUpdate(DomainObject $obj)
    {
        // TODO: Implement newUpdate() method.
        $id = $obj->getId();
        $cond = null;
        $values['name'] = $obj->getName();
        if($id > -1){
            $cond['id'] = $id;
        }
        return $this->buildStetement("venue", $values, $cond);
    }
}
/**
 * 在上面的代码中，可以直接使用DomainObject
 * 如果在系统中执行update时，需要操作多个对象
 * 可以使用标志对象来定义要使用哪些对象
 * 这些对象组成一个$cond数组
 * newUpdate() 提取生=成查询所需要的数据，在此过程中，数据被转化为数据库信息
 * 注意：newUpdate()的参数可以使任何DomainObject
 * 所以所以的UpdateFactory都可以共享一个接口，还可以在代码中加入更严格的类型检查
 * 以阻止错误类型的数据被传入
 */

?>