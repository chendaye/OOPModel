<?php
namespace Angular\Mapper;
use Angular\Domain\DomainObject;
use Angular\Domain\Venue;
/**
 *构造方法预编译了一些sql，供将来使用
 * 这个工作也可以放到静态方法中，以便所有VenueMapper对象的实例共享
 * 可以将Mapper对象放到Registry 中，以此节省重复实例化的开销
 */
class VenueMapper extends Mapper{
    /**
     * 预处理语句 获取PDOStmt 对象
     */
    public function __construct()
    {
        parent::__construct();
        $this->selectStmt = self::$pdo->prepare("SELECT * FROM venue WHEN id = ?"); //预处理sql语句
        $this->updateStmt = self::$pdo->prepare("UPDATE venue SET name = ?, id = ? WHERE id = ?");
        $this->insertStmt = self::$pdo->prepare("INSERT INTO venue (name) VALUES (?)");
    }

    /**
     * 获取一个SpaceCollection对象
     * @param array $raw
     * @return SpaceCollection
     */
    public function getCollection(array $raw){
        return new SpaceCollection($raw, $this);
    }

    /**
     * 创建对象 Venue
     * @param array $array
     * @return Venue
     */
    protected function doCreateObject(array $array)
    {
        // TODO: Implement doCreateObject() method.
        $obj = new Venue($array['id']);
        $obj->setName($array['name']);
        return $obj;
    }

    /**
     * 新增操作
     * @param DomainObject $obj
     */
    protected function doInsert(DomainObject $obj)
    {
        // TODO: Implement doInsert() method.
        print_r("instering!");
        debug_print_backtrace();
        $values = array($obj->getName());
        $this->insertStmt->execute($values);
        $id = self::$pdo->lastInsertId();
        $obj->setId();
    }

    /**
     * 更新操作
     * @param DomainObject $obj
     */
    public function update(DomainObject $obj)
    {
        // TODO: Implement update() method.
        $values = array($obj->getName(), $obj->getId(), $obj->getId());
        $this->updateStmt->execute($values);
    }

    /**
     * 返回一个PDOStmt 查询对象
     * @return \PDOStatement
     */
    public function selectStmt()
    {
        // TODO: Implement selectStmt() method.
        return $this->selectStmt;
    }
}
//todo:例子
$venue = new Venue();
$venue->setName("chen");
//插入对象到数据库
$mapper = new VenueMapper();
$mapper->insert($venue);
//读取刚插入的记录
$venue = $mapper->find($venue->getId());
print_r($venue);
//更新
$mapper->update($venue);
?>