<?php
/**
 * 创建对象不是件容易的事，利用多态的灵活性（在运行时切换不同的实现），有多种方法来处理抽象类
 * 单例模式：生成一个只生成一个对象的特殊类
 * 工厂方法模式：构建创建者类的继承层级
 * 抽象工厂模式：功能相关产品的创建
 * 原型模式：用克隆的方法来生成对象
 *
 * 就针对接口编程而言，鼓励在类中使用超类（父类），可以在运行时使用不同子类的对象
 */
namespace FactoryModel_ProblemNoe{
    /**
     * 抽象父类
     * Class Employee
     * @package FactoryModel_Problem
     */
    abstract class Employee{
        protected $name;
        public function __construct($name){
            $this->name = $name;
        }
        abstract public function fire();
    }

    /**
     * 一个具体实现
     * Class Marin
     * @package FactoryModel_Problem
     */
    class Marin extends Employee {
        public function fire(){
            // TODO: Implement fire() method.
            echo $this->name;
        }
    }

    /**
     * 实例化Employ类，并使用
     * Class Boss
     * @package FactoryModel_Problem
     */
    class Boss{
        public $employees = array();
        //TODO:实例化对象，并保存在私有变量中
        public function addEmployee($employName){
            $this->employees[] = new Marin($employName);
        }
        //TODO:使用保存的实例
        public function projectFail(){
            if(count($this->employees) > 0){
                //array_pop() 函数删除数组中的最后一个元素,返回数组的最后一个值。如果数组是空的，或者非数组，将返回 NULL。
                $emp = array_pop($this->employees);
                $emp->fire();
            }
        }
    }
    $boss = new Boss();
    //调用方法实例化Employee对象
    $boss->addEmployee('peter');
    $boss->addEmployee('join');
    $boss->addEmployee('mary');
    print_r($boss->employees);
    var_dump($boss->projectFail());
}
namespace FactoryModel_ProblemTwo{

    use FactoryModel_ProblemNoe\Employee;

    /**
     * 在Boss 类中实例化Marin对象，代码灵活受限制
     *如能在Boss中能使用任何Employee类的实例
     */
    class Boss {
        private $employees = array();

        /**
         * 接受Employee 对象作为参数
         * 注意与问题一的区别，问题一时接受字符串，用字符动态创建对象
         * 这里是直接接受对象作为参数
         * @param Employee $employee
         */
        public function addEmployee(Employee $employee){
            //TODO：$this->employees[]  中无论问题一二 都是存的Employee的对象
            $this->employees[] = $employee;
        }

        /**
         *使用Employee对象
         */
        public function projectFail(){
            if(count($this->employees) > 0){
                //array_pop() 函数删除数组中的最后一个元素,返回数组的最后一个值。如果数组是空的，或者非数组，将返回 NULL。
                $emp = array_pop($this->employees);
                $emp->fire();
            }
        }
    }

    /**
     * 有一个Employee实例
     * Class CludeUp
     * @package FactoryModel_ProblemTwo
     */
    class CludeUp extends Employee {
        public function fire(){
            // TODO: Implement fire() method.
            echo $this->name;
        }
    }
    $new_boss = new Boss();
    $new_boss->addEmployee(new CludeUp('CludeUp'));
    $new_boss->projectFail();
}

namespace Factory{

    use FactoryModel_ProblemTwo\Boss;

    /**
     * 上个方案中Boss 类和 Employee 类能够引起工作
     * 也能享受多态的优点
     * 但是还是没有创建对象的方法
     *
     * 现在把对象实例化的工作委托出来
     * 委托一个独立的类或者方法来生成Employee 对象
     */
    abstract class Employee{
        protected $name;
        private static $type = array('xiaoming', 'xiaogang', 'xiaoli');
        /**
         * 构造函数
         * Employee constructor.
         * @param $name
         */
        public function __construct($name){
            $this->name = $name;
        }

        /**
         * 委托方法，用来生成对象
         * @param $name
         * @return mixed
         */
        public static function recruit($name){
            $num = rand(0,2);
            $class = self::$type[$num];
            $class = 'Factory\\'.$class;
            return new $class($name);
        }
        //静态 测试方法
        static public function test(){
            return new xiaoming('chen');
        }
        //父类抽象方法
        abstract public function fire();
    }

    /**
     * Employee 实例
     * Class xiaoming
     * @package Factory
     */
    class xiaoming extends Employee {
        public function fire(){
            // TODO: Implement fire() method.
            print $this->name;
        }
    }

    /**
     * Employee 实例
     * Class xiaogang
     * @package Factory
     */
    class xiaogang extends Employee {
        public function fire(){
            // TODO: Implement fire() method.
            print $this->name;
        }
    }

    /**
     * Employee 实例
     * Class xiaoli
     * @package Factory
     */
    class xiaoli extends Employee {
        public function fire(){
            // TODO: Implement fire() method.
            print $this->name;
        }
    }

    $m = Employee::recruit('mary');
    $m->fire();

    /**
     * 测试查错
     * Class test
     * @package Factory
     */
    class test{
        public $test = 'test';
    }
    //TODO:用字符串动态实例化类，必须要把命名空间拼接上，否则默认在跟空间下找，会报找不到类
    $name = 'Factory\test';
    $ret = new $name();
    $reflection = new \ReflectionClass($ret);
    //TODO:输出类的信息,需要一个ReflectionClass的实例
    \Reflection::export($reflection);
}
//TODO:工厂就是负责  生成对象  的 类或方法

namespace SingleModle{
    /**
     * 单例模式
     * 全局变量是面向对象编程，引发bug的主要原因之一
     * 全局变量将类捆绑与特定的环境，破坏封装
     * 全局变变零不受保护的本质是很大的问题
     * 一旦开始依赖全局变量，那么某个类库中的全局变量和其他地方声明的全局变量迟早会发生冲突
     * 蛋疼的是php不会对全局变量的冲突给出警告
     *
     * 经过良好设计的系统一般通过方法的调用来传递对象实例
     * 每个类都会与背景环境保持独立，并且通过清晰的通信方式来与系统中的其他部分来通信
     * 当我们需要一些类来作为沟通渠道，就不得不引入依赖关系
     *
     * 现有一个保存应用程序的类Preferences 该类作为公告板
     * 其他类用它来获取设置、消息
     *要保证系统中所有对象都使用同一个Preferences 对象；不能出现一个类在Preferences上设置值，其他对象从一个不同的Preferences对象上获取
     *
     *问题关键点：
     *Preferences 对象能被系统中的任何对象使用
     *Preferences 对象不应该被储存在会被覆盖的全局变量中
     *系统中不应该超过一个Preferences对象
     */

    //TODO:要实现上面的要求，要创建一个无法从自身外部来实例化的类，只有声明一个私有的构造方法即可
    class Preferences{
        //TODO:可以不断的接受值
        private $props = array();
        //TODO:  private 且 static 只能在内部访问
        //TODO:关于静态  可以将其看做 声明的一个简单的 函数、变量 在脚本任何地方可用
        private static $instance;
        /**
         * 私有的构造方法
         * 限制外部实例化
         * Preferences constructor.
         */
        private function __construct(){

        }

        /**
         * 公共的静态方法
         * 在内部实例化自己
         * @return Preferences
         */
        //TODO:  public  且  static  能够访问 static 的 instance，并且在脚本的任何地方都能被使用
        //TODO: 静态方法只能调用静态属性
        public static function getInstance(){
            //TODO:实例化自己
            if(empty(self::$instance)){
                self::$instance = new Preferences();
            }
            //TODO:Preferences对象
            return self::$instance;
        }

        /**
         * 设置值
         * @param $key
         * @param $val
         */
        public function setProps($key, $val){
            $this->props[$key] = $val;
        }

        /**
         * 读取值
         * @param $key
         * @return mixed
         */
        public function getProps($key){
            return $this->props[$key];
        }
    }
    //TODO:获取单例对象
    $single = Preferences::getInstance();
    echo '<br>';
    $ref = new \ReflectionClass($single);
    \Reflection::export($ref);
    //TODO:设置值
    $single->setProps('chendaye', 'shuaizhale');
    //TODO:读取值
    echo $single->getProps('chendaye');
    //TODO:销毁对象
    unset($single);
    $single_two = Preferences::getInstance();
    //TODO:设置的值并没有随着$single的销毁而消失；因为值是保存在Preferences对象里的
    echo $single_two->getProps('chendaye');

    /**
     * 全局变量 与 单例
     * 缺点：全局变量和单例都可能被误用，因为单例在系统任何地方都能被访问，可能导致难以调试的依赖关系
     * 单例的全局性会是程序绕过类接口定义的通信线路，单例被使用时依赖被隐藏在方法内部，不会出现在方法声明中，系统难以追踪
     *
     * 优点：适度的使用单例，可以优化系统设计，可以从 在系统中传递不必要的对象 的情况中解放出来
     */

}


namespace FactoryMethodModel\Problem{
    /**
     * 面向对象设计强调:抽象类高于实现
     * 即：要尽量一般化，而不是特殊化
     * 工厂方法模式：解决当代码关注于抽象类型时，创建对象实例的问题
     * 答案就是用特定的类来处理实例化
     *
     * 问题：一个关于个人事务管理的项目
     * 功能之一：管理Appointment 预约对象
     * 团队和其他团队建立了关系；要用一个BloggsCal 的格式来交流数据
     * 但是将来有肯面对更多的格式
     *
     * 在接口级别上：
     * 需要一个数据编码器来把 Appointment 来转化为专有格式， 编码器命名为：AppEncoder 类
     * 还需要一个类来获得编码器  并使用编码器来与第三方进行通信， 该类命名为：CommsManager 类
     *
     * 对应的模式语言：CommsManager = 创建者   AppEncoder = 产品
     *
     */
    /**
     * 编码器接口
     * Class AppEncoder
     * @package FactoryMethodModel
     */
    abstract class AppEncoder{
        abstract public function encode();
    }

    /**
     * 接口的实现
     * Class BloggsEncoder
     * @package FactoryMethodModel
     */
    class BloggsEncoder extends AppEncoder {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'Bloggs';
        }
    }

    /**
     * 接口实现
     * Class MegaEncoder
     * @package FactoryMethodModel
     */
    class MegaEncoder extends AppEncoder {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'Mega';
        }
    }

    /**
     * 工厂
     * 创建者
     * 创建编码器类
     * Class CommsManager
     * @package FactoryMethodModel
     */
    class CommsManager{
        //TODO:可能要创建的编码器类型
        const BLOGG = 1;
        const MEGA = 2;
        private $mode;

        /**
         * 获取要实例的编码器类型
         * CommsManager constructor.
         * @param $model
         */
        public function __construct($model){
            $this->mode = $model;
        }

        /**
         * 创建对象
         * @return BloggsEncoder
         */
        public function getEncoder(){
            switch ($this->mode){
                case (self::BLOGG):
                    return  new BloggsEncoder();
                case (self::MEGA):
                    return new MegaEncoder();
                default:
                    return new BloggsEncoder();
            }
        }

        /**
         * 问题:通过switch来创建不同的接口实例，可能会导致逻辑代码重复
         * 重复条件代码在代码中蔓延，不是好代码。
         * @return string
         */
        public function getText(){
            switch ($this->mode){
                case (self::BLOGG):
                    return  'bloggs text';
                case (self::MEGA):
                    return 'mega text';
                default:
                    return 'bloggs text';
            }
        }
    }
    //TODO:测试
    $maneger = new CommsManager(CommsManager::MEGA);
    //TODO:从工厂获取对象
    $encoder = $maneger->getEncoder();
    print '<br>';
    $encoder->encode();
}
namespace FactoryMethodModel\Solution{
    /**
     * 提炼问题：
     * 代码运行的时候我们才知道要生成的对象
     * 需要能轻易加入新的业务处理逻辑
     * 每一个业务逻辑都可以定制特定的属于自己的功能
     *
     * 由于条件语句可以被多态代替：条件语句 处于一个类中  我们乐意把这个类 抽象成一个 抽象接口 不同的条件分支 转化为不同的 接口实例
     * 于是我们可以为每一种编码方式 创建一个 Commsmanager 子类 每一个子类都有 getEncoder 方法
     *
     * 工厂方法把 创建者类 与  产品类分开
     * 一般：创建者类 的每一个实例 对应 生产一个  产品的子类
     */
    /**
     * 编码器接口
     * Class AppEncoder
     * @package FactoryMethodModel
     */
    //TODO:产品类接口
    abstract class AppEncoder{
        abstract public function encode();
    }
    //TODO: 产品类的实例
    class BloggsEncoder extends AppEncoder {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'blogg';
        }
    }
    //TODO:创建类借口
    abstract class CommsManager{
        abstract public function getEncode();
        abstract public function getText();
        abstract public function getHead();
    }
    //TODO:创建者实例 一个创建者的实现对应哪一个产品的实现
    class BloggsManager extends CommsManager {
        public function getEncode(){
            // TODO: Implement getEncode() method.
            return new BloggsEncoder();
        }
        public function getHead(){
            // TODO: Implement getHead() method.
            echo '2';
        }
        public function getText(){
            // TODO: Implement getText() method.
            echo '3';
        }
    }
    //TODO:测试
    echo '<br>';
    //工厂子类
    $manage = new BloggsManager();
    //工厂子类实例对应产品子类
    $encode = $manage->getEncode();
    $encode->encode();
}

namespace FactoryMethodModel\Abct{

    use FactoryMethodModel\Problem\AppEncoder;

    /**
     * 上个工厂模式中 创建者 与产品 的结构非常类似； 这是使用工厂模式常见的结构
     * 但是这样的结构带来了一种特殊的代码重复，也有可能导致不必要的子类化
     *
     * 抽象工厂模式
     * 上个工厂方法中 我们可以不断增加 编码器的 （同一个接口产品）种类  让工厂结构横向生长
     * 当然也可以增加 （不同产品）接口种类  实现（产品）接口的纵向生长
     * 这样可以叫产品家族（一个创建者多个产品）
     *
     * 就是有之前的 一对一 变成  一对多
     */
    //TODO:创建者接口
    abstract class CommsManager{
        //TODO:创建四个系列的产品，每个系列对应一个产品接口类
        abstract public function getAppEncode();
        abstract public function getTtdEncode();
        abstract public function getHeadText();
        abstract public function getFootText();
    }
    //TODO:创建者实例
    class BloggsManager extends CommsManager {
        //TODO:实例化系列产品
        public function getAppEncode()
        {
            // TODO: Implement getAppEncode() method.
            return new BloogsAppEncode();
        }
        public function getTtdEncode()
        {
            // TODO: Implement getTtdEncode() method.
            return new BloggsTtdEncode();
        }
        public function getHeadText()
        {
            // TODO: Implement getHeadText() method.
            return new BloogsHeadText();
        }
        public function getFootText()
        {
            // TODO: Implement getFootText() method.
            return new BloogsFootText();
        }
    }
    class MageManager extends CommsManager {
        //TODO:实例化系列产品
        public function getAppEncode()
        {
            // TODO: Implement getAppEncode() method.
            return new MageAppEncode();
        }
        public function getTtdEncode()
        {
            // TODO: Implement getTtdEncode() method.
            return new MageTtdEncode();
        }
        public function getHeadText()
        {
            // TODO: Implement getHeadText() method.
            return new MageHeadText();
        }
        public function getFootText()
        {
            // TODO: Implement getFootText() method.
            return new MageFootText();
        }
    }

    //TODO:系列产品AppEncode
    abstract class AppEncode{
        abstract public function encode();
    }
    class BloogsAppEncode extends AppEncoder {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'BloogsAppEncode';
        }
    }
    class MageAppEncode extends AppEncoder {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'MageAppEncode';
        }
    }
    //TODO:系列产品TtdEncode
    abstract class TtdEncode{
        abstract public function encode();
    }
    class BloogsTtdEncode extends TtdEncode {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'BloogsTtdEncode';
        }
    }
    class MageTtdEncode extends TtdEncode {
        public function encode()
        {
            // TODO: Implement encode() method.
            echo 'MageTtdEncode';
        }
    }
    //TODO:系列产品HeadText
    abstract class HeadText{
        abstract public function text();
    }
    class BloogsHeadText extends HeadText {
        public function text()
        {
            // TODO: Implement encode() method.
            echo 'BloogsHeadText';
        }
    }
    class MageHeadText extends HeadText {
        public function text()
        {
            // TODO: Implement encode() method.
            echo 'MageHeadText';
        }
    }
    //TODO:系列产品FootText
    abstract class FootText{
        abstract public function text();
    }
    class BloogsFootText extends FootText {
        public function text()
        {
            // TODO: Implement encode() method.
            echo 'BloogsFootText';
        }
    }
    class MageFootText extends FootText {
        public function text()
        {
            // TODO: Implement encode() method.
            echo 'MageFootText';
        }
    }
    //TODO:测试
    echo '<br>';
    $create = new BloggsManager();
    $BloogsAppEncode = $create->getAppEncode();
    $BloogsAppEncode->encode();
    /**
     * 此模式的结果：
     * 将系统与实现分离开， 可以添加删除任意数目的子类而不会影响系统
     * 对系统功能相关的类进行强制组合
     */
}

namespace PrototypeModel{
    /**
     * 原型模式
     * 平行继承层次的出现时工厂方法模式带来的一个问题， 这也是一种耦合
     * 没添加一个产品家族，就必须创建一个对应的创建者；当产品越来越多时，不易于维护
     *
     * 一个解决的方法是：使用 clone 关键字赋值已存在的具体产品
     * 然后，具体产品本身就成了他们自己生产的基础
     * 这就是原型模式，这样可以用组合代替继承；促进了代码的灵活性，减少了必须创建的类
     *
     * 问题：
     * 有一款游戏，可在区块组成的格子中操作战斗单元
     * 每个区块分别代表：海洋、平原、森林
     * 地形的类别约束了占有区块单元的战斗能力
     *
     * 现有一个TerrainFactory 对象来提供 Sea Forest  Plains 对象
     * Sea 可能是MarsSea 和 EarthSea 的抽象父类
     * Foeest Plains 也是雷同
     *
     * 这些分支就构成了抽象工厂模式
     * 有产品体系：Sea Plains Forest
     * 产品体系有不同的分支 Earth Mars
     *
     * 先依赖继承来组合工厂生成 地形 产品家族
     * 若不想要一个平行级继承体系，而需要运行时的最大灵活性时
     *
     * 实现：当使用抽象工厂模式或者工厂方法模式时，必须决定使用哪个具体的创建者
     * 很可能通过检查配置的值来决定
     * 于是，可以创建一个保存具体产品的工厂类，并在初始化时加入这种方法
     * 这样可以减少类的数量
     */
    //TODO:海洋
    class Sea{}
    class EarthSea extends Sea {}
    class MarsSea extends Sea{}
    //TODO:平原
    class Plains{}
    class EarthPlains extends Plains {}
    class MarsPlains extends Plains {}
    //TODO:森林
    class Forest{}
    class EarthForest extends Forest {}
    class MarsForest extends Forest {}

    /**
     * 地形类
     * Class TerrainFactory
     * @package PrototypeModel
     */
    class TerrainFactory{
        private $sea;
        private $plains;
        private $forest;

        /**
         * 构造方法  传入特定的类
         * TerrainFactory constructor.
         * @param Sea $sea
         * @param Plains $plains
         * @param Forest $forest
         */
        public function __construct(Sea $sea, Plains $plains, Forest $forest){
            $this->sea = $sea;
            $this->plains = $forest;
            $this->forest = $forest;
        }

        /**
         * 克隆生成两个不同的对象
         * 不同于赋值，赋值时两个指向同一对象
         *
         * 通过克隆获取对象
         * @return Sea
         */
        public function getSea(){
            return clone $this->sea;
        }

        /**
         *通过克隆获取对象
         * @return Forest
         */
        public function getPlains(){
            return clone $this->plains;
        }

        /**
         * 通过克隆获取对象
         * @return Forest
         */
        public function getForest(){
            return clone $this->forest;
        }
    }
    //TODO:测试
    $factory = new TerrainFactory(new Sea(),new Plains(), new Forest());
    echo '<br>';
    var_dump($factory->getSea());
    /**
     * 加载一个TerrainFactory类 在调用getSea()方法时 返回对象初始化时保存的Sea 对象的一个副本
     * 也就是 传入构造方法一个对象，然后在getSea()中克隆该对象  并且返回，克隆出的就是副本
     * 主要就省略了一下类，且增加了灵活性
     *
     * 如果想要在一个类似地球，有海洋 森林 平原的星球上玩游戏
     * 只要在初始化（实例化）new TerrainFactory(new MarsSea(),new MarsPlains(), new MarsForest()); 时传入不同的对象便可
     *
     * 原型模式：就是把传入的对象  克隆 然后返回
     * 可以利用组合提供的灵活性
     *
     * 原型模式中在传入对象时；可以从新设置传入对象的具体状态
     * 然后这个对象被克隆  同样拥有此状态
     */
    class leadSea extends Sea {
        private $lead = 0;
        public function __construct($lead){
            $this->lead = $lead;
        }
    }
    //TODO:在初始化的时候设置状态
    $fact = new TerrainFactory(new leadSea(6),new Plains(), new Forest());
    /**
     * 如果产品对象（传入的对象）引用了其他对象，那么克隆对象时要 实现__clone() 方法来保证 是深度复制
     *
     * clone关键字能给应用的对象生成一个浅复制：
     * 产品对象会具有和原对象一样的属性；如果原对象的任何属性是对象；那么这些对象属性不会被直接复制到产品对象副本中
     * 而是会引用同样的 对象属性 也就是说 浅克隆来的产品对象副本 中的对象属性  和原对象产品中的  对象属性 是指向同一个对象
     *
     * 通过使用 __clone()方法，可以自定义 克隆行为， 此方法在 clone 关键字被使用是自动调用
     */
    class Contained{}
    class Create{
        //TODO:此属性用来保存产品对象
        public $contained;
        public function __construct(){
            //TODO:初始化私有属性
            $this->contained = new Contained();
        }
        //TODO:深度复制
        public function __clone()
        {
            // TODO: 确保克隆的对象 持有的是 self::$contauintd 的克隆而不是引用
            $this->contained = clone $this->contained;
        }
    }
    //TODO:面向对象编程中  属性是用来  专门存储信息、数据的
}

namespace ModelLiar{

    use FactoryMethodModel\Abct\BloogsAppEncode;

    /**
     * 以上一些模式，狡猾的避开了对象生成的相关决策过程
     * 抽象工厂模式中 产品家族的创建 分布到 不同的具体的创建者中
     * 而如何创建具体的创建者现在又成了问题
     *
     * 系统经常根据配置值选择决定  具体的创建者
     * 这些配置信息可以在数据库，配置文件，或者服务器文件中；或者干脆用php属性 或者变量硬编码
     * Apache的配置文件通常是.htaccess
     *
     * PHP硬编码一般有手写 或者 写一个自动生成类文件的脚本
     *
     * 下面是一个一个包含日历协议类型的标记的类
     */
    //TODO:配置类，存放配置信息
    class Setting{
        static $type = 'Blog';
    }

    /**
     * Config 是一个标准的单例（除了 私有 就是 静态），在系统任何地方都可以得到其实例，并且得到的是同一个实例
     * 此类首先通过配置，进行初始化，然后把初始化的结果 提供给外界
     *
     * init()会被构造方法调用， 因此在一个进程中只会运行一次
     * Class Config
     * @package ModelLiar
     */
    class Config{
        //TODO:静态属性
        private static $instance;
        private $manager;
        //TODO:私有的构造方法 ，无法在外部实例化
        private function __construct(){
            //TODO:初始化操作
            $this->init();
        }
        //TODO:根据配置类的信息来初始化
        private function init(){
            switch (Setting::$type){
                case 'Blog':
                    $this->manager = new BloogsAppEncode();
                break;
                default:
                    $this->manager = new BloogsAppEncode();
                break;
            }
        }
        //TODO:静态方法，用来让外界访问类内部的私有属性
        public static function getInstance(){
            //TODO:看 私有 的 静态 属性 有没有被赋值，没有就实例化自己  所以整个进程只会实例化一次
            if(empty(self::$instance)){
                //TODO:实例化自身，实例的同时，构造函数执行了初始化
                self::$instance = new self();
            }
            //返回自身的一个实例
            return self::$instance;
        }
        //TODO:用静态的公共方法  提供 初始化的 结果  -》根据配置类 实例化的对应的 对象
        public function getManager(){
            return $this->manager;
        }
    }
    //TODO:单例 实例化
    $single = Config::getInstance();
    $manager = $single->getManager();
}
?>