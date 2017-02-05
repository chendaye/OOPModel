<?php
/**
 * Venue Space Event 对象之间已经建立起了一定的关系
 * 当创建一个Venue对象时，会传递SpaceCollection对象给 Venue 对象
 * 如果罗列在Venue 对象中的每个Space 对象，Venue 对象会自动发出数据库请求来获得与每个Space相关的Event
 * 这些 Event对象被保存在EventCollection对象中
 * 如果不想查看任何事件，就没有必要去连接数据库
 * 如果要查询对个Venue 而每个Venue都有两三个Space，每个Space又有上百个事件，这将是一个代价非常搞得过程
 * 显然有时候我们要避免这样的自动加载
 *
 * 实现：
 * 延迟加载就是要在客户端代码真正需要的时候采取获取数据
 * 达到这个目的最简单的方法就是在包含的对象中详细说明延迟
 */
//Space中可以这么写
//就是加条件判断一下
function getEvents(){
    if(is_null($this->events)){
        $this->events = self::getFinder('\\Angukar\\Domain\\Event')->findBySpaceId($this->getId());
    }
    return $this->events;
}
/**
 * 这个方法可行但是有点麻烦
 * 现在回到 Iterator(用于生成 Collection对象)，接口下隐藏了实现（客户端代码访问Iterator对象时，原始数据还未被用来实例化一个领域对象）
 * 可以隐藏更多内容
 * 可以创建一个 EventCollection 对象，延迟数据库访问，直到请求要求它访问数据库
 * 也就意味着 客户端对象（如Space）实例化时，不再需要知道它持有一个空的Collection
 * 一旦客户端代码需要，就会持有一个正常的EventCollection对象
 */
class DeferredEventCollection extends EventCollection{
    private $stmt;
    private $valueArray;
    private $run = false;
    public function __construct(Mapper $mapper, \PDOStatement $stmt_handle, array $valueArray)
    {
        parent::__construct(null, $mapper);
        $this->stmt = $stmt_handle;
        $this->valueArray = $valueArray;
    }
    public function notifyAccess(){
        if(!$this->run){
            $this->stmt->execute($this->valueArray);
            $this->raw = $this->stmt->fetchAll();
            $this->total = count($this->raw);
        }
        $this->run = true;
    }
}
/**
 * 显然，这个类是EventCollection的子类
 * 其构造方法的参数是EventMapper对象和PDOStatment对象，以及一个与预编译语句相匹配的数组
 * 在第一次实例化的时候这个类知识保存其属性，并没有查询数据库
 *
 * Collection 基类中定义了空方法 notifyAccess() ,外部调用Collection 类的方法时  notifyAccess()会被调用
 * DeferredEventCollection覆盖了这个方法，如果访问 Collection，该类就会获取真正的数据
 * 获取数通过 PDOStatment::execute() 完成
 * 和PDOStatment::fecth()一起，获得合适传递给 Mapper::createObject()的数组
 */
//EventMapper类中实例化 DeferredEventCollection的方法
function findBySpaceId($s_id){
    return new DeferredEventCollection($this, $this->selectBySpaceStmt, array($s_id));
}
/**
 * 效果：
 * 无论是否在领域类中显示的添加延迟加载代码，延迟加载都是一个好习惯
 * 除了类型安全的好处之外，使用集合对象而非数组的好处是可以使用延迟加载
 */
?>