<?php
/**
 * 组合由于继承
 * 组合体现了类和对象间的组装弹性
 *
 * 组合是将继承用于组合对象的极端例子，简单实用
 * 组合模式可以很好的聚合和管理许多相似的对象
 * 对客户端来说，一个独立的对象  和 一个对象集合 没有差别
 * 因为模式中类的结构 和对象的组织结构非常相似
 * 组合模式中继承的层级结构是树形结构；就是从基类开始，分支到不同的子类，在继承树中可以轻松生成枝叶；也容易遍历整棵对象树
 * 组合有利于我们为集合和组件之间的关系建立模型
 *
 * 回到游戏的问题：
 * 玩家可以在由一大块区块所组成的地图上移动战斗单元，独立的战斗单元被组合起来用于战斗
 * 预先定义战斗单元
 */
namespace ComboProblem{
    //定义战斗单元接口
    abstract class Unti{
        abstract public function strangth();
    }
    //Archer 战斗单元
    class Archer extends Unti {
        public function strangth()
        {
            // TODO: Implement strangth() method.
            return 45;
        }
    }
    //Laser 战斗单元
    class Laser extends Unti {
        public function strangth()
        {
            // TODO: Implement strangth() method.
            return 60;
        }
    }

    /**
     * 一个类用来组合战斗单元
     * Class Army
     * @package ComboProblem
     */
    class Army{
        private $unit = array();
        //TODO:添加战斗单元
        public function addUnit(Unti $unit){
            //把战斗单元存入数组
            array_push($this->unit, $unit);
        }
        //TODO:调用战斗单元
        public function getStrangth(){
            $strangth = 0;
            foreach ($this->unit as $unit){
                $strangth += $unit->strangth();
            }
            return $strangth;
        }
    }
}
//TODO:问题：现在要求可以把不同的军队组合起来，且还有更多类似的问题
namespace ComboSolution{
    /**
     * 容器对象与他们包含的对象共享同一个接口；所有它们应该共享同一个类型家族
     *
     * 组合模式定义了一个单根基础体系，使具有不同职责的类集合在一起个工作
     * 组合模式中的类必须支持共同的操作集，并将其作为首要的职责
     *
     * 例：Archer Laser 都是一个作战单元  由它们组合起来的 Army 同样也是一个战斗单元
     * 在客户端代码看来，三者并无差别；它们 都支持共同的操作集  战斗  防御 移动 等
     * 也就是说  三个 都可以看做是 等同的 单元
     * 其中：Archer Laser 称作 叶子对象（最小的单元 不包含其它 unit 对象）
     * Army 称作 组合对象 包含其它 unit对象
     */

    /**
     * 单元的公共接口
     *
     * 注意：在叶子单元中并不需要 addUnit  delUnit 方法 这是一个问题
     * Class Unit
     * @package ComboSolution
     */
    abstract class Unit{
        //TODO:添加单元
        abstract public function addUnit(Unit $unit);
        //TODO:删除单元
        abstract public function delUnit(Unit $unit);
        //TODO:单元的职责
        abstract public function strength();
    }

    /**
     * 现在可以 既可以保存  组合对象 Army 也可以保存叶子对象 Archer Laser
     * Class Army
     * @package ComboSolution
     */
    class Army extends Unit{
        private $unit = array();
        //TODO:增加单元
        public function addUnit(Unit $unit)
        {
            // TODO: Implement addUnit() method.
            if(in_array($unit, $this->unit)){
                return ;
            }
            $this->unit[] = $unit;
        }
        //TODO:删除单元
        public function delUnit(Unit $unit)
        {
            // TODO: Implement delUnit() method.
            //array_udiff() 函数用于比较两个（或更多个）数组的键值 ，并返回差集
            $this->unit = array_udiff($this->unit, array($unit), function($a, $b){return ($a === $b)?1:0;});
        }
        //TODO:职责
        public function strength()
        {
            // TODO: Implement strength() method.
            $strength = 0;
            foreach ($this->unit as $unit) {
                $strength += $unit->strength();
            }
            return $strength;
        }
    }
}

namespace ComboSolutionTwo{
    //TODO:组合模式的问题在于：如何实现 add del  方法
    //TODO:一般组合模式都在 超类（接口）中添加这两个方法， 则所有的单元都必须加以实现，但是意味着 叶子对象 也要实现这两个方法 这是不必要的
    use ComboProblem\Archer;
    use ComboProblem\Army;
    use ComboProblem\Laser;
    use ComboProblem\Unti;

    /**
     * 现不希望 叶子对象中 出现 add del 方法，于是让在叶子对象 中添加此二方法是  抛出异常
     */

    /**
     * 为组合写一个 异常类
     * Class UnitExcepyion
     * @package ComboSolutionTwo
     */
    class UnitException extends \Exception {
        public function __construct($message, $code, Exception $previous)
        {
            parent::__construct($message, $code, $previous);
        }

        public function msg(){
            return '叶子对象 不能添加，删除单元';
        }
    }

    /**
     * 在叶子对象中抛出异常
     * Class Leaf
     * @package ComboSolutionTwo
     */
    class Leaf extends Unit {
        //TODO:增加单元
        public function addUnit(Unit $unit)
        {
            // TODO: 抛出异常
            throw new UnitException(get_class($this).'叶子对象不能有删除添加方法');
        }
        //TODO:删除单元
        public function delUnit(Unit $unit)
        {
            // TODO: 抛出异常
            throw new UnitException(get_class($this).'叶子对象不能有删除添加方法');
        }
        //TODO:职责
        public function strength()
        {
            // TODO: Implement strength() method.
            $strength = 0;
            return $strength;
        }
    }

    /**
     * 在叶子节点中  抛出异常  会导致 局部重复代码
     * 所以在单元接口中抛出异常
     * Class Unit
     * @package ComboSolutionTwo
     */
    abstract class Unit{
        //TODO:添加单元
        public function addUnit(Unit $unit){
            throw new UnitException(get_class($this).'叶子对象不能有删除添加方法');
        }
        //TODO:删除单元
        public function delUnit(Unit $unit){
            throw new UnitException(get_class($this).'叶子对象不能有删除添加方法');
        }
        //TODO:单元的职责
        abstract public function strength();
    }
    class Aleaf extends Unit{
        public function strength()
        {
            // TODO: Implement strength() method.
            return 66;
        }
    }
    //TODO:这样免去了叶子对象中的代码重复，但是组合单元内不需要再强制实现添加删除方法了，这回带来新方法
    /**
     * 组合模式的优势
     * 灵活：因为组合模式中一切类都共享一个父类，所以可以轻松添加 组合对象 和 叶子对象，不需要大量修改代码
     * 简单：使用组合结构的客户端代码只需要实际简单的接口， 客户端没必要在意是 组合对象 和  叶子对象
     * 隐式到达：组合模式中对象是树形结构，每个组合对象中 都保存着 叶子对象 的引用，因此对树中某部分的小改动影响也很大
     * 显式到达：树形结构可以轻松遍历，可以通过迭代树形结构来获取 组合对象 和 叶子对象 的信息；或者对 组合对象 和 叶子对象进行批量处理
     */
    //TODO：创建一个组合对象
    $combo = new Army();
    //TODO:给组合对象添加叶子对象
    $combo ->addUnit(new Archer());
    $combo ->addUnit(new Laser());
    //TODO:把Army 这个组合对象 添加入组合对象
    $sub = new Army();
    $sub->addUnit($combo);
}

namespace ComboSolutionThree{

    use ComboProblem\Army;
    use ComboSolutionTwo\UnitException;

    /**
     * 对于  add  del 方法 是放在接口中 还是放在子类中 不好解决
     *
     * 如若放在 父类中  则叶子对象  中 也要实现  add del 方法，造成代码重复；
     * 如果父类中  的 add del 不设置为抽象方法（并在方法中抛出异常）  也不好
     *
     * 如果把两个方法  放在下一级 则客户端 不知道  子对象是否支持该方法；这样给系统设计带来歧义
     *
     * 我可以将组合对象 分解为 两个对象 在其中一个中实现 add del
     */

    /**
     * 单元接口
     * Class Unit
     * @package ComboSolutionThree
     */
    abstract class Unit{
        public function getComposite(){
            return null;
        }
        abstract public function strength();
    }

    /**
     * 实现单元接口
     * Class CompositeUnit
     * @package ComboSolutionThree
     */
    abstract class CompositeUnit extends Unit {
        private $unit = array();
        //返回自身
        public function getComposite()
        {
            return $this;
        }
        //返回单元集合
        protected function units(){
            return $this->unit;
        }
        //删除单元
        public function delUnit(Unit $unit){
            $this->unit = array_udiff($this->unit, array($unit), function($a, $b){return ($a === $b)?1:0;});
        }
        //添加单元
        public function addUnit(Unit $unit){
            if(in_array($unit, $this->unit)){
                return '';
            }
            $this->unit[] = $unit;
        }
    }
    /**
     *  CompositeUnit类  继承自  Unit类   ，并没有实现抽象方法  strength()， 所以 CompositeUnit 也被声明为抽象类
     * CompositeUnit  本身并没有抽象方法， 但是 任何组合单元 都可以 扩展 CompositeUnit 类， 不用直接扩展 Unit 类；
     * 通过这样 多加一级 抽象类  ，实现了 叶子对象 继承Unit 组合单元  不用再强制实现 add del 方法,同时组合对象 可以继承 CompositeUnit类
     *
     * 但这样会存在一个问题：客户端代码在使用 add del 方法时 必须检查对象是否为  CompositeUnit 对象
     * 于是在 Unit 中添加 getComposite() 方法默认返回 空， 只有在 CompositeUnit 中 getComposite() 返回 CompositeUnit 对象自身
     *所有 只有该方法返回的是一个对象，才能调用 add del 方法
     */

    /**
     * Class UnitScript
     * @package ComboSolutionThree
     *
     * 一开始  并不知道 $oldUnit 是继承自  CompositeUnit ，但通过调用  getComposite() 判断 完美的解决了问题
     * getComposite()  在 Unit CompositeUnit 中都有  且实现 不同  是 关键
     */
    class UnitScript{
        static function join(Unit $newUnit, Unit $oldUnit){
            $camp = '';
            //TODO:如果 $oldUnit 是组合对象 继承自 CompositeUnit 就把 $newUnit 单元 添加进去
            if(!is_null($camp = $oldUnit->getComposite())){
                $camp->add($newUnit);
            }else{
                //:TODO：否则，就把两个都添加进  Army 中
                $camp = new Army();
                $camp->addUnit($newUnit);
                $camp->addUnit($oldUnit);
            }
        }
    }
    /**
     * 组合模式的缺陷：
     * 简化的前提是使所有的类都继承同一个基类，简化有时会降低对象类型安全
     * 当模型变得复杂，就要越来越多的检查类型
     *
     * 比如 有一个 叶子对象 “马”  不能放到 组合对象 “军队” 中
     * 此时就要进行类型检查
     * 如下面的例子 必须检查类型
     * 当特殊的对象越来多，组合模式显得弊大于利
     * 在 局部对象 可互换的情况下，组合模式才最实用
     *
     * 另外：组合操作 存在成本问题
     *
     * Army::strength() 方法及时一个例子，它会逐级调用对象树中下级分支中的该方法
     * 如果对象树中有大量的  Army 方法，那一个简单的调用 就可能导致系统崩溃
     * 一个解决办法是在 父级对象中 缓存计算结果 ，这样可以使接下来的调用减少 系统开销
     *
     * 另一个问题：组合模式 不容易存到关系型数据库；
     * 因为在默认情况下，通过级联引用来访问整个结构，所以若要在数据库中构造一个组合结构可能要使用多个昂贵的查询
     * 可以 通过给每个节点 赋 ID 来解决，但这样把ID导出了要重建 父子关系 很混乱
     *
     *不过组合模式非常适合持久化为 xml；因为xml元素也是树形结构
     *
     *总结：
     *组合模式中  叶子对象 组合对象 可以一样看待
     *组合模式是树形结构 所有对整体的操作会影响局部
     *通过组合，队客户端来说局部数据是透明的，组合模式使这些操作和查询对客户端代码透明
     *对象树可以很方便的遍历，可以轻松的在组合结构中加入新的组件
     *但是：组合模式依赖于其组成部分的简单性， 不能很好的在关系型数据库中存储，但是容易在陈、xml中存储 ，也就是持久化
     */
    class Troop{
        public function add(Unit $unit){
            if($unit instanceof Army){
                //TODO:类型不符时抛出异常
                throw new UnitException('马 不能进军队');
            }
        }
        public function strength(){
            return 666;
        }
    }
}
?>