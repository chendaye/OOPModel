<?php
/**
 * 之前已经从数据映射器中去除了创建对象、查询和数据集合的功能
 * 不再需要管理任何数据库查询条件；现在数据映射器还剩什么？
 * 之前创建了很多对象，并让他们进行交互
 * 现在需要在这些对象之上添加一个对象，用于缓存和处理数据库连接（也可以把连接工作委托给其他对象）
 * 这些数据层控制器成为领域对象组装器
 */
namespace Angular\Mapper;
use Angular\Domain\DomainObject;
use Angular\Registry\Scope\ApplicationRegistry;
use Think\Exception;

/**
 * 这个类不是一个抽象类，没有将自己分成多个子类
 * 而是使用PersistenceFactory来确保自己为当前领域对象得到正确的组件
 * Class DomainObjectAssembler
 * @package Angular\Mapper
 */
class DomainObjectAssembler{
    protected static $PDO;
    public function __construct(PersistenceFactory $factory)
    {
        $this->factory = $factory;
        if(!isset(self::$PDO)){
            $dsn = ApplicationRegistry::getDSN();
            if(is_null($dsn)){
                throw new Exception("no dsn");
            }
            self::$PDO = new \PDO($dsn);
            self::$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }
    public function getStatement($str){
        if(!isset($this->getStatement[$str])){
            $this->getStatement[$str] = self::$PDO->prepare($str);
        }
        return $this->statment($str);
    }
    public function findOne(IdentityObject $idobj){
        $collection = $this->find($idobj);
        return $collection->next();
    }
    public function find(IdentityPbject $idobj){
        $selfact = $this->factory->getSelectionFactory();
        list($selection, $values) = $selfact->newSelection($idobj);
        $stmt = $this->getStatement($selection);
        $stmt->execute($values);
        $raw = $stmt->fetachAll();
        return $this->factory->getCollection($raw);
    }
    public function insert(DomainObject $obj){
        $upfact = $this->factory->getUpdateFactory();
        list($update, $values) = $upfact->newUpdate($obj);
        $stmt = $this->getStatement($update);
        $stmt->execute($values);
        if($obj->getId() < 0){
            $obj->setId(self::$PDO->lastInsertId());
        }
        $obj->markClean();
    }
}
/**
 * 小结：
 * 选择使用那些模式总是由你面临的问题决定
 * 实际开发中可以采用数据映射器和标识对象
 * 可以使用灵活的解决方案，但也需要自己能够分解整个系统
 * 并且在必要的时候手动完成一些工作，比如:维护一个干净的接口和分离的数据层
 *
 * 例如：要优化一条SQL查询语句，或者收银连接从多个表中获取数据
 * 即使使用的是一个基于复杂模式的第三方框架，你也会发现ORM(Object-Relational-Mapping 对象关系映射)，并不总能满足要求
 *
 * 优秀框架的一个特点是：
 * 在不破坏原有完整性的前提下，可以轻松的加入新功能
 *
 * 本章的模式主要有:
 * 数据映射器：创建用于将领域模型对象映射到关系数据库的类
 * 标识映射器：跟踪系统中的所有对象，以避免重复实例化或者不必要的数据库操作
 * 工作单元：自动保存对象到数据库，确保只将修改过的对象和新创建的对象插入数据库
 * 延迟加载：延迟创建对象或数据库查询，直到需要
 * 领域对象工厂：封装创建对象的功能
 * 标识对象：允许客户端自行组装数据库查询条件，而与底层数据库无关
 * 查询语句工厂：包括选择工厂和更新工厂，封装创建SQL查询的逻辑
 * 领域对象组装器：创建一个在较高层次管理数据存储的控制器
 */
?>