<?php
/**
 * 不论程序是否复杂，绝大多数Web应用都需要在一定程度上进行数据持久化操作
 *
 * 数据库模式包括以下内容：
 * 数据接口层：定义存储层和其他部分之间的接口
 * 对象监听：跟踪对象避免重复，自动保存和插入数据
 * 灵活查询：允许客户端程序员在构建查询时不考虑底层的数据库
 * 生成结果对象列表：创建可迭代的数据集合
 * 管理数据库组件：使用抽象工厂模式
 *
 * 数据层：
 * 和客户交流时，主要围绕表现层；问题在于如何将数据库中的行和列转换成项目中的数据结构
 * 通常使用多个模式来解决数据的持久化问题
 *
 * 数据映射器（Mapping）:
 * 数据映射器是一个负责将数据库数据映射到对象的类
 *
 * 问题：
 * 对象间的组织关系和关系数据表中的表是不同的，
 * 数据库表可以看成是行和列组成的格子，表中的一行可以通过外键和另一个表（甚至同一个表）中的一行关联
 *
 * 对象的组织关系更为复杂，一个对象可能包含其他对象；不同的数据结构根据不同的方式组织相同的对象
 *
 * 关系数据库是一个管理大量表格式数据的优秀解决方案，但类和对象通常封装更小的集中式的信息块
 * 类和关系数据库之间的这种分称为 对象关系阻抗不匹配
 */
namespace Angular\Mapper;
use Angular\Domain\DomainObject;
use Angular\Registry\Scope\ApplicationRegistry;

/**
 * 构造方法通过ApplicationRegistry 注册表获取DSN 连接数据库
 * 从控制层传递数据到映射器并非总是明智的
 * Class Mapper
 * @package Angular\Mapper
 */
abstract class Mapper{
    protected static $pdo;
    public function __construct()
    {
        if(!isset(self::$pdo)){
            $dsn = ApplicationRegistry::getDSN();   //数据库连接信息
        }
        self::$pdo = new \PDO($dsn);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * 通过ID查询
     * 返回一个对象，这样可以实现链式调用
     * @param $id
     * @return null
     */
    public function find($id){
        $this->selectStmt()->execute(array($id));
        $array = $this->selectStmt()->fetch();
        $this->selectStmt()->closeCursor();
        if(!is_array($array)){
            return null;
        }
        if(!isset($array['id'])){
            return null;
        }
        $object = $this->createObject($array);
        return $object;
    }

    /**
     * 创建对象
     * @param $array
     * @return mixed
     */
    public function createObject($array){
        $obj = $this->doCreateObject($array);
        return $obj;
    }

    /**
     * 插入记录
     * @param DomainObject $obj
     */
    public function insert(DomainObject $obj){
        $this->doInsert($obj);
    }

    /**
     * 更新
     * @param DomainObject $obj
     * @return mixed
     */
    public abstract function update(DomainObject $obj);

    /**
     * 创建对象
     * @param array $array
     * @return mixed
     */
    protected abstract function doCreateObject(array $array);

    /**
     * 插入
     * @param DomainObject $obj
     * @return mixed
     */
    protected abstract function doInsert(DomainObject $obj);

    /**
     * PDOStmt 对象
     * @return mixed
     */
    protected abstract function selectStmt();
}
/**
 * 另一种创建映射器的方法是由Registry 类来处理
 * 映射器不是在类的内部实例化 PDO 对象
 * 而是将PDO对象作为构造方法的参数传入类中
 */
abstract class Mapper_Two{
    protected $PDO;

    /**
     * PDO 作为构造参数
     * Mapper_Two constructor.
     * @param \PDO $PDO
     */
    public function __construct(\PDO $PDO)
    {
        $this->PDO = $PDO;
    }
}
/**
 * 客户端代码可以通过，Request 类中的 Request::getVenueMapper() 方法从 Registry 对象中获得一个新的 VenueMapper
 * getVenueMapper() 方法将会实例化一个Mapper并生成一个 PDO 对象
 * 对于以后的请求，该方法会返回缓存着的 Mapper
 * 不过，这样一来 Registry 必须获得系统更多的信息  而 Mapper 不需要和全局配置数据打交道
 *
 * insert() 方法除了把真正的操作分配给doInsert()之外，什么都不做
 * doInsert() 方法声明为抽象方法，由子类的方法来负责具体工作
 *
 * find() 方法负责调用预编译过的 sql语句（由具体的子类提供）；并获得行数据
 * 然后调用createObject() 方法将数据从数组转化为对象
 * 将数组转化为对象可以有多种方式，因此细节可以有抽象方法 doCreateObject() 来处理
 * 显然，createObject() 也是一个方法定义（子类必须要实现的功能）
 * 真正要执行的操作委托给具体子类来完成
 *
 * 根据不同的条件，子类中也将定制各种方法来查找数据
 */
?>