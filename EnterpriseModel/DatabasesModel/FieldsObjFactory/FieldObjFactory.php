<?php
/**
 * 数据映射器模式很灵活，但是也有缺陷
 * 映射器中要包含很多功能，如：组装sql，将数组转换为对象等
 * 这么多功能会是数据映射器很强大，但是也会降低灵活性
 * 当映射器要处理多种查询或者其他类要共享映射器中的功能时，映射器的缺陷更突出
 *
 * 可以分解数据映射器分为多个更为具体的模式，这些组合起来构成完整的数据映射器
 *
 * 问题：
 * 数据映射器是一个天然的隔离层；映射器可以在内部使用CreateObject()来创建领域对象
 * 但集合 Collection对象也需要它创建领域对象
 * 这就要在创建 Collections时将一个映射器的引用传递给集合
 * 如果允许回调，这样做没问题（就像，访问者模式和观察者模式中一样）
 * 但是，如果能把创建对象的功能从映射器中移出来，会更灵活
 *
 * 实现：
 * 设有多个映射器类，每一个都有自己的领域对象，只要将createObject()方法从映射器类中移出来
 * 放到一个独立的类中（按平行的对象层级（排行）就构成一个领域工厂模式
 */
namespace Angular\Mapper;
/**
 * 领域工厂只有一个核心功能很简单
 * Class DomainObjectFactory
 * @package Angular\Mapper
 */
abstract class  DomainObjectFactory{
    abstract public function createObject(array $array);
}
?>
<?php
namespace Angular\Mapper;

/**
 * 领域工厂的一个具体实现
 *
 * 可能会需要将对象缓存起来避免重复实例化或者不必要的数据交互
 * 可以把映射器中的 addToMap() 和 getFromMap()方法移到这里
 * 或者在ObjectWatcher 和 createObject 间建立观察者关系
 * Class VenueObjectFactory
 * @package Angular\Mapper
 */
class VenueObjectFactory extends DomainObjectFactory {
    public function createObject(array $array)
    {
        // TODO: Implement createObject() method.
        $obj = new \Angular\Domain\Venue($array['id']);
        $obj->setName($array['name']);
        return $obj;
    }
}

/**
 * 效果：
 * 领域对象工厂消除了数据库原始数据与对象字段之间的耦合
 * 可以在createObject()方法中执行任意的调整，这个过程对客户端程序员是透明的，只要最后能提供数据即可
 * 这个功能移出后还可以被其他组件使用
 */
//例如：
//namespace Angular\Mapper;
//Collection
abstract class Collection{
    protected $dofact;
    protected $total = 0;
    protected $raw = array();
    public function __construct(array $raw = null, \Angular\Mapper\DomainObjectFactory $dofact = null)
    {
        if(!is_null($raw) && !is_null($dofact)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->dofact = $dofact;
    }
}
//DomainObjectFactory 可以及时生成对象
if(isset($this->raw[$num])){
    $this->object[$num] = $this->dofact->createObject($this->raw[$num]);
    return $this->object[$num];
}
/**
 * 由于领域对象工厂消除了自身与数据库的耦合，它可以进行更高效的测试
 *
 * 把一个组件分解成几个部分会导致类的数量增加，同时也会是代码不易理解
 * 即使每个组件及其之间的关系都很符合逻辑，定义也很清晰，要从十几个名字相似的类中找出一个还是很难
 *
 * 数据映射器还有另一个问题
 * Mapper::getCollection()方法很方柏霓，但是存在一个问题就是
 * 其他类可能需要得到一个Collection东西，但是不想访问数据库
 * 现有两个抽象的组件  Collection和DomainObjectFactory
 * 根据使用的领域对象，可能需要各种具体的子类
 * 比如：VenueCollection 和 VenueDomainObjectFactory; 这个问题显然需要抽象工厂没事来处理
 */
?>
