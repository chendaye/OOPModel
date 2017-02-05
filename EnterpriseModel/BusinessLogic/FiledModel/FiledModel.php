<?php
/**
 * 领域模型是原始的逻辑引擎
 * 他是对项目中各种个体的抽象表达
 *
 * 领域模型是一个平台，各种业务问题都可以在上面表达出来；且不需要纠缠于各种细节（数据库、网页等）
 *
 * 领域模型象征着真实世界里项目的参与者，它们常常被描述为一组属性或者附加的代理
 *
 * 问题：
 * 事物脚本处理相同的问题时会重复变成一个问题；有时可以通过重构代码来解决，但是粘贴复制可能成为开发中难以避免的事
 * 可以使用领域模型来抽象和具体化系统中的参与者和操作过程
 * 简单的说就是把系统中的要素 提炼出来 抽象具体 为模型
 * 然后一个事物的实现就是领域模型的组合
 *
 * 实现：
 * 领域模型的设计比较简单，复杂性在于使领域模型从应用其他层中分离出来
 *
 * 将领域模型从表现层中分离出来并不难，只要确保这些参与者保持独立即可
 * 但是从数据层分离不容易
 *
 * 理想状态下领域模型应该只包含它要表达和解决的问题，但实际上，领域模型中很难去除数据库的操作
 * 将领域模型直接映射到关系数据库的数据表是通常的做法，这使开发变得简单
 *
 * 直接映射：每一个对象对于一个数据表，直接映射便于管理
 * 直接映射并非总可行，特别当数据库在应用之前已经存在；对象和表的直接关联本身可能会产生一些问题
 *
 * 领域模型常常映射到数据库的结构上，并不意味着模型类应该了解数据库相关的信息
 * 通过将模型与数据库分离，整个层会更易于测试，更不会受到数据库结构的影响
 * 领域模型只关心每个类本身要完成的核心工作和承担的责任
 */
namespace Angular\Domain;
abstract class DomainObject{
    private $id;
    public function __construct($id = null)
    {
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    static public function getCollerction($type){
        return array();
    }
    public function collerction(){
        return self::getCollerction(get_class($this));  //返回自身对象
    }
}

/**
 * 领域模型实例
 * Class Venue
 * @package Angular\Domain
 */
class Venue extends DomainObject {
    private $name;
    private $space;
    public function __construct($id = null, $name = null)
    {
        parent::__construct($id);
        $this->name = $name;
        $this->space = self::getCollerction("\\Angular\\Domain\\Space");
    }

    /**
     * 具体职责(操作)
     * @param SpaceCollection $space
     */
    public function setSpace(SpaceCollection $space){
        $this->space = $space;
    }
    public function addSpace(Space $space){
        $this->space->add($space);
        $space->setVenue($this);
    }
    public function setName($name_s){
        $this->name = $name_s;
        $this->markDirty();
    }
    public function getName(){
        return $this->name;
    }
}
/**
 * 效果：
 * 领域模型简单还是复杂取决于，业务逻辑的复杂度
 * 领域模型的好处在于：设计领域模型时可以专注于系统要解决的问题；其他问题可以由其他层来解决
 *
 * 领域模型和数据层分离会导致一定的代价，可能要经数据库代码直接放入模型中
 * 相对于简单的模型，特别是类与数据表一一对应时，这种方法完全可行
 * 可以减少因协调对象和数据库而创建外部系统导致的时间耗费
 *
 * 总结：
 * 设计模式应该被用在合适的地方，并在必要的时候组合使用
 * 当项目需要的时候可以考虑设计模式，
 * 但是不要认为在项目一开始就一定要打造出一个完整的框架
 * 本章的内容可以构成一个框架的基础
 */
?>