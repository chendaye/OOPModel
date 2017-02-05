<?php
/**
 * 组合中，在使用对象集合时，可能需要对结构上每一个单独的组件应用各种操作，这样的操作可以内建与组件本身，毕竟在组件内部调用其他组件是最方便的
 * 但这样的问题是：你不知道所有可能需要执行的操作
 * 如果每增加一个操作就在类中增加一个对新操作的支持，类会越来越臃肿
 */
namespace VisitorProblem{
    /**
     * 在组合模式栗子中：创建了有战斗单元组成的军队
     * 它的整体和局部可以互换
     * 操作可以在组件内部实现
     * 一般情况下，局部对象自己会执行操作，组合对象会调用其子对象来执行操作
     *
     * 如果操作容易整合到类中这样做没问题，但是更多的周边任务会使接口不够用
     */
    /**
     * 单元的公共接口
     *
     * 注意：在叶子单元中并不需要 addUnit  delUnit 方法 这是一个问题
     * Class Unit
     * @package ComboSolution
     */
    abstract class CompositeUnit{
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
    class Army extends CompositeUnit{
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
    //TODO:定义战斗单元接口
    abstract class Unit{
        abstract public function strangth();
    }
    //TODO:Laser 战斗单元
    class Laser extends Unit {
        public function strangth()
        {
            // TODO: Implement strangth() method.
            return 60;
        }
    }

    //TODO:现在，有个 转存叶节点文本信息的操作，此操作会被添加到 单元接口 Unit 中
    //Unit
    function textDump($num = 0){
        $ret = 4*$num;
        return $ret;
    }
    //TODO:这个方法可以在 CompositeUnit 中覆盖
    //CompositeUnit
    function texttDump($num = 0){
       foreach (array() as $item){
           $ret = 4*$num;
       }
        return $ret;
    }
    /**
     * 如上，我们可能还需要继续创建 统计树中单元个数的方法  保存组件到数据库的方法  计算食物消耗的方法
     * 问题是：为什么要在 CompositeUnit 中加入这些方法   本只用出现在  Unit 中？
     * 一个解释是：添加这些不同的操作有利于在组合结构中较为轻松的访问相关节点
     * 但是：虽然可以轻松遍历对象树 是组合模式的一大优势，但不是每一个需要遍历对象树的操作都需要在 CompositeUnit 中出现
     */
}
namespace VisitorSolution{
    /**
     * 解决:
     * 在抽象类中定义 accept() 方法
     */
    abstract class CompositeUnit{
        //TODO:添加单元
        abstract public function addUnit(Unit $unit);
        //TODO:删除单元
        abstract public function delUnit(Unit $unit);
        //TODO:单元的职责
        abstract public function strength();
    }

    /**
     * 组合对象
     * Class Army
     * @package VisitorSolution
     */
    class Army extends CompositeUnit{
        private $depth; //深度
        private $unit = array();
        //TODO:增加单元
        public function addUnit(Unit $unit)
        {
            // TODO: Implement addUnit() method.
            if(in_array($unit, $this->unit)){
                return ;
            }
            //TODO:节点深度
            $unit->setUnit($this->depth + 1);
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
        //TODO:组合类中添加 accept 方法
        /**
         * accept() 第一步： 动态拼接要访问的方法；  第二步：通过拼接的方法，调用访问者对象中相应的方法
         *
         * 这个 accept() 方法 和 Unit::accept() 方法基本一样只是多了些内容
         * 它根据当前的类名 构造一个 方法名  然后通过 传进来的  ArmyVisitor $visitor 对象来调用对应的方法
         *
         * 比如：当前是 Army 类 就调用 ArmyVisitor::visitArmy() 方法
         * 这实际上是通过 动态拼接 方法名 实现 一个方法在不同 子类中的不同实现； 就避免了在每个子类中实现 该方法
         *
         * 现在 accept 方法实现了两件事：
         * 为当前组件调用 正确的访问者方法
         * 将访问者对象传递给当前对象元素的所有子元素（如果是组合对象）
         */

        /**
         * 先调用组合对象自己的 访问者方法  再逐个调用 局部对象的访问者方法
         * @param ArmyVisitor $visitor
         */
        public function accept(ArmyVisitor $visitor){
            $name = explode('\\',get_class($this));
            $method = "visit".end($name);     //动态拼接方法名 visitArmy
            $visitor->$method($this);
            foreach ($this->unit as $unit){
                $unit->accept($visitor);    //accept() 方法只在单元 接口 中实现，不用在每个具体的单元实例中实现（通过动态拼接方法名）
            }
        }
    }

    //TODO:定义战斗单元接口
    /**
     * accept() 方法要求一个  ArmyVisitor 对象
     * PHP支持 动态的定义一个我们希望调用的方法，
     * 这样我们就不用在类的继承体系中每一个叶节点上 都实现  accept() 方法。
     * 因为  accept() 或根据当前具体实例 动态拼接 一个方法名，实现方法调用
     */
    abstract class Unit{
        private $depth;
        abstract public function strangth();
        //TODO:在抽象类Unit 中定义 accept() 方法
        public function accept(ArmyVisitor $visitor){
            //TODO:在动态拼接  类名 方法名 是一定不要呢忽略了  命名空间  命名空间  命名空间
            $name = explode('\\',get_class($this));
            $method = "visit".end($name);     //动态拼接方法名 visitUnit
            $visitor->$method($this);
        }
        //设置一个单元的深度
        protected function setDepth($depth){
            $this->depth = $depth;
        }
        //获取一个单元的深度
        public function getDepth(){
            return $this->depth;
        }
        public function setUnit($depth){
            return $depth += 1;
        }
    }
    //TODO:Laser 战斗单元
    class Laser extends Unit {
        public function strangth()
        {
            // TODO: Implement strangth() method.
            return 60;
        }
    }
    class Toop extends Unit {
        public function strangth()
        {
            // TODO: Implement strangth() method.
            return 60;
        }
    }

    /**
     * 现在还需要定义 ArmyVisitor 接口
     * 访问这类也应该 为继承体系中每一个 具体类定义 accept() 方法，
     * 这样就能为不同对象提供不同功能
     *
     * 下面定义一个默认得 visit() 方法 当类中没有呢为 Unit 类提供特殊处理时，该方法被调用
     *
     * 现在 剩余的工作让 ArmyVisitor  具体实现
     */
    //TODO:访问者类， 抽象类，具体可有很多实现
    abstract class  ArmyVisitor{
        //持有一个 单元对象
        abstract function visit(Unit $node);
        //TODO:定义访问者方法； 每一个组合对象都有相应的访问者方法
        /**
         * 组合对象 的访问者 默认方法  可以在子类中 重写
         * @param Army $node
         */
        public function visitToop(Army $node){
            //$this->visit($node);
        }

        /**
         * 局部对象 的 访问者方法 ，可以在子类中重写
         * @param Laser $node
         */
        public function visitLaser(Laser $node){
            $this->visit($node);
        }
        //TODO:......
    }
    /**
     * 这样我们就创建了一个新机制
     * 只需要添加几个方法，新功能就能方便的添加到组合类中
     * 而不需要包含他们的接口，也不会产生大量重复代码
     */
    /**
     * 现在军队需要交纳税金
     * 征税者访问军队，并向找到的每个单位征税
     * 不同的单位税率不同
     */
    //TODO:每一个访问者类的子类，就为被访问者，实现一个具体的功能
    class TaxVisitor extends ArmyVisitor {
        private $due = 0;
        private $report = '';
        public function visit(Unit $node){
            //$this->levy($node, 1);
        }
        /**
         * 收税方法
         * @param Unit $unit
         * @param $amount
         */
        private function levy(Unit $unit, $amount){
            $this->report = get_class($unit);
            $this->due += $amount;
        }

        /**
         * 重写父类的 访问者方法
         * @param Army $node
         * @return int
         */
        //TODO:组合对象 访问者方法
        public function visitArmy(Army $node){
            //$this->levy($node, 1);
            return 66;
        }
        //TODO:局部对象 访问者方法  注意参数 是  Laser  类（Unit 的子类）
        public function visitLaser(Laser $node){
            $this->levy($node, 1);
        }
        //TODO:......

        /**
         * 获取被收税者的信息
         * @return string
         */
        public function getReport(){
            return $this->report;
        }

        /**
         * 获取税收
         * @return int
         */
        public function getTax(){
            return $this->due;
        }
    }
    //TODO:具体使用
    $army = new Army();
    //添加组件
    $army->addUnit(new Laser());
    //税收
    $tax = new TaxVisitor();    //访问者类实例
    $army->accept($tax);    //遍历调用 局部对象的  accept 方法， accept 方法中 访问 $tax 对应的动态方法
    //echo $tax->getTax();

    /**
     * 总结：
     * 访问者方法 要素：
     * 一、在 局部/组合 单元 的抽象接口中 定义  accept() 方法，此方法需要一个访问者类的实例 new TaxVisitor();
     *  accept() 方法中 首先得到 当前 局部/组合类的类名  然后根据 类名 动态拼接出 一个 一定形式的方法名
     * 例： visitLaser visitArmy
     * 然后通过传进来的 访问者实例 调用访问者实例中预先定义好的方法 TaxVisitor::visitLaser()  TaxVisitor::visitArmy()  实现与特定功能
     *
     * 二、访问者类； 首先会定义一个 访问者类接口 ArmyVisitor
     * 提供所有  visitLaser()  visitArmy() 等的一个名人实现
     * 然后可以在 此接口上 扩展无数个 具体访问者类， 并且 具体的访问者类 会重写这些函数  实现特定的功能
     * 如此，每一个访问者实例中  都会有 每一个 组合对象 对应的一个方法，这些方法实现共同的功能
     * 这样每一个访问者类就 为 组合单元扩展一个功能
     *
     * 组合对象的 accept() 方法会遍历调用 局部对象的 accept() 方法，一起完成工作
     * 访问者模式，要在组合模式中实现一个新的功能 就不必再每一个组合单元中 编写新的函数
     * 只需要，扩展一个访问者类，实现这个功能即可
     * 减少代码重复，可以在不改变原 单元对象  的基础上随意添加新功能，简单实用的很
     */

    /**
     * 问题：
     * 访问者模式，虽然完美的符合，组合模式；但访问者模式可以用于任意对象集合
     * 其次，外部化操作可能会破坏封装，也就是说，需要公开被访问者内部来让访问者执行有关操作
     *
     */
}
?>