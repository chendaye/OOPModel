<?php
/**
 * find() 方法很简单，只用返回一个对象。如果要从数据库中获得大量数据
 * 一个办法是返回一个数组
 * 如果返回一个对象数组，那么数组中的每个对象都要被实例化，可能会导致不必要的资源浪费
 * 另一种方法是返回一个数组，然后让调用代码来实例化；但是这样偏离了Mapper类的核心目的
 *
 * 还有一种方法是使用PHP内置的 Iterator接口：
 * 实现Iterator 接口必须定义一些用于查询数据集的方法
 * 这样类就能通过foreach() 循环遍历
 *
 * Iterator接口定义的方法:
 * rewind() 指向列表开头
 * current() 返回当前指针所指向的元素
 * key()  返回当前的键（例如，指针的值）
 * next() 返回当前指向的元素并且将指针前移一步
 *valid()  确定当前指针处有一个元素
 */
namespace Angular\Mapper;
use Angular\Domain\DomainObject;
use League\Flysystem\Exception;

/**
 * 调用构造方法的时候可以不使用参数，也可以使用参数（最终被转化为一组对象的原始数据，和一个映射器的引用）
 * 如果提供了$raw 参数，那么$raw 参数就会和$mapper 参数一起放到类属性中，提供$raw 参数的同时也需要Mapper 的实例
 * 因为需要使用Mapper 对象吧每一行记录转化为对象
 *
 * 如果没提供参数，则类中的数据为空，可以通过方法add()添加数据到类中
 * 类有两个数组：$object $raw ；如果要查找一个特定的元素。getRow() 方法现在 $objects 对象中查找元素是否被实例化
 * 如果已经被实例化，就返回；否则，在$raw中查找原始数据
 *因此相应的行数据可以传递给 Mapper::createObject() 方法；返回一个缓存在$object 数组中具有相应索引的DomainObject对象
 *最新创建的Domianobject对象返回给客户端代码
 * Class Collection
 * @package Angular\Mapper
 */
abstract class Collection implements \Iterator {
    protected $mapper;
    protected $total = 0;
    protected $raw = array();
    private $result;
    private $pointer = 0;
    private $objects = array();

    /**
     * Collection constructor.
     * @param array|null $raw
     * @param Mapper|null $mapper
     */
    public function __construct(array $raw = null, Mapper $mapper = null)
    {
        if(!is_null($raw) && !is_null($mapper)){
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
    }

    /**
     * @param DomainObject $object
     * @throws Exception
     */
    public function add(DomainObject $object){
        $class = $this->targetClass();
        if(!($object instanceof $class)){
            throw new Exception("error");
        }
        $this->notifyAccess();
        $this->objects[$this->total] = $this->objects;
        $this->total++;
    }

    /**
     * 抽象方法
     * @return mixed
     */
    public abstract function targetClass();

    /**
     * 暂时留空
     */
    protected function notifyAccess(){
        //todo:....
    }

    /**
     * @param $num
     * @return mixed|null
     */
    private function getRow($num){
        $this->notifyAccess();
        if($num >= $this->total || $num < 0){
            return null;
        }
        if(isset($this->objects[$num])){
            return $this->objects[$num];
        }
        if(isset($this->raw[$num])){
            $this->objects[$num] = $this->mapper->createObject($this->raw[$num]);
            return $this->objects[$num];
        }
    }

    /**
     * 指向列表开头
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
        $this->pointer = 0;
    }

    /**
     * 返回指针所指的元素
     * @return mixed|null
     */
    public function current()
    {
        // TODO: Implement current() method.
        return $this->getRow($this->pointer);
    }

    /**
     * 返回当前键值
     * @return int
     */
    public function key(){
        return $this->pointer;
    }

    /**
     * 指向下一个元素
     * @return mixed|null
     */
    public function next(){
        $row = $this->getRow($this->pointer);
        if($row){
            $this->pointer++;
        }
        return $row;
    }

    /**
     * 确定指针处有一个元素
     * @return bool
     */
    public function valid(){
        return (!is_null($this->current()));
    }
}

/**
 * Collection 方法是一个抽象类
 * 每个领域类都要提供一个特定的实现
 * VenueCollection 继承自 Collection，并且实现了 targetClass 方法；它和父类中的add方法一起检查对象类型
 * 确保只有Venue 对象才可以添加到数据集合中
 *
 * 显然这个类只能和VenueMapper 一起工作
 *
 * VenueCollection 实现了一个接口 VenueColletion.是独立接口（Separated Interface）的技术的一部分
 * 利用VenueClollection, domain包可以定义相应的Collection类
 */
class VenueCollection extends Collection {
    public function targetClass()
    {
        // TODO: Implement targetClass() method.
    }
}
?>

<?php
namespace Angular\Domain;
interface VenueCollection extends \Iterator {
   public function add(DomainObject $venue);
}
interface SpaceCollection extends \Iterator {
    public function add(DomainObject $space);
}
interface EventCollection extends \Iterator {
    public function add(DomainObject $event);
}
$collection = HelperFactory::getCollection("Angular\\Domain\\Venue");
$collection->add(new Venue(null, "A"));
$collection->add(new Venue(null, "B"));
$collection->add(new Venue(null, "C"));

//命名空间 Angular\Domain
//DomainObject
class DomainObject_{
    static public function getCollection($type){
        return HelperFactory::getCollection($type);
    }
    public function collection(){
        return self::getCollection(get_class($this));
    }
}

/**
 * 这个类有两种获得 Collection 对象的途径：静态方法和实例化方法
 * 无论静态方法和实例化方法都只是使用类名调用HelperFactory::getCollection
 *
 *在Domain 包中 为 Mapper 和 Collection 创建接口
 *这样领域对象可以与Mapper 完全隔离开来，这叫做分离接口
 *这个模式很有用，有时用户需要放弃真整个映射器转而使用另一个
 *如果实现了分离接口模式，就可以使用getFinder()方法来返回一个 Finder 接口。
 *Mapper会实现这个接口
 *
 *鉴于此，可以对Venue 类进行扩展，用于管理Space 对象的持久化
 *Venue 提供 方法来添加 Space 到 SpaceCollection 或者设置一个全新的 SpaceCollection
 */
//命名空间 Angular\Domain
//Venue
/**
 * setSpace() 被 VenueMapper 类用于构建Venue 对象
 * 该方法建立在集合中所有Space 对象都指向当前的 Veneu 基础上
 * Class Venue_
 * @package Angular\Domain
 */
class Venue_{
    public function setSpace(SpaceCollection $space){
        $this->space = $space;
    }
    public function getSpace(){
        if(!isset($this->space)){
            $this->space = self::getCollection("\\Agular\\Domain\\Space");
        }
        return $this->space;
    }
    public function addSpace(Space $space){
        $this->getSpace()->add($space);
        $space->setVenue($this);
    }
}
?>

<?php
/**
 * VenueMapper 需要为它所创建的每一个Venue对象建立一个SpaceCollection
 */
//命名空间 Angular\Mapper
//VenueMapper
/**
 * VenueMapper::doCreateObject()方法得到一个SpaceMapper 并从中获得一个Collection
 * SpaceMapper 类实现了findByVenue方法。可以通过该方法查询数据库，得到包含多个对象的结果集
 * Class VenueMapper_
 * @package Angular\Domain
 */
class VenueMapper_{
    protected function doCreateObject(array $array){
        $obj = new Venue($array['id']);
        $obj->setName($array['id']);
        $space_mapper = new SpaceMapper();
        $space_collection = $space_mapper->findByVenue($array['id']);
    }
}
?>

<?php
//namespace Angular\Mapper;
//Mapper
/**
 * findAll()方法调用子方法 selectAllStmt();该方法需要一个预编译的sql语句
 * Class Mapper_
 * @package Angular\Domain
 */
class Mapper_ {
    public function findAll(){
        $this->selectStmt()->execute(array());
        return $this->getCollection($this->selectAllStmt()->fetchAll(\PDO::FETCH_ASSOC));
    }
}

/**
 * 在一个完整的Mapper类中，应该将getCollection() 和 selectAllCollection() 声明为抽象方法
 * 这样所有映射器都能返回一个包含持久领域对象的集合
 */

/**
 * 效果：
 * 使用映射器添加Space对象到Venue的缺少是不得不两次访问数据库；
 * 在大多数情况下，这个代价是你值得的
 * Venue::doCreateObject() 获取 SpaceCollection的工作也可以放到 Venue::getSpace()中完成
 * 这样第二次数据库连接只有在需要的时候才发生
 *
 * 性能问题比较显著的时候可以不使用SpaceMapper 直接使用一条 SQL语句，一次性取回所有的数据
 * 这样会导致代码的移植性降低，但是性能优化总要有代价
 *
 * 数据映射器最强大的地方在于消除了 领域层 和数据操作之间的联系
 * Mapper在幕后运作，可以用于各种对象关系映射
 *
 * 缺点：
 * 需要创建大量具体的映射器类
 * 有很多样板代码可以由程序自动生成，利用反射是一个好办法
 *
 * 使用映射器时注意不要一次加载过多的对象，尽管Iterator 可以帮助我们
 * 因为Collection 对象只持有原始数据；第二次请求（Space对象）只有当访问特定的Venue并把数组转化为对象时才产生
 * 要小心波浪式加载，即创建映射器时，如果该映射器使用了另一个映射器来获取一个对象属性，那么可能导致性能上的雪崩
 * 第二个映射器可能使用了更多的映射器来获取想要的对象
 *
 * 同时要注意数据库的查询效率，对SQL语句的编译和优化
 */
?>
