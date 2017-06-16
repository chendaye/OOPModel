<?php
/**
 * 依赖注入和控制反转（同义词）  代码内部创建依赖关系，而是让其作为一个参数传递
 * 科普：
 * 首先依赖注入和控制反转说的是同一个东西，是一种设计模式，这种设计模式用来减少程序间的耦合，
 * 首先先别追究这个设计模式的定义，否则你一定会被说的云里雾里
 * 不管怎么样，总算弄清楚一些了，下面就以php的角度来描述一下依赖注入这个概念。
 */

namespace example_one{
    /**
     * 先假设我们这里有一个类，类里面需要用到数据库连接，按照最最原始的办法，我们可能是这样写这个类的：
     * 过程：
     * 在构造函数里先将数据库类文件include进来；
     * 然后又通过new Db并传入数据库连接信息实例化db类；
     * 之后getList方法就可以通过$this->_db来调用数据库类，实现数据库操作。
     */
    class example {

        private $_db;
        function __construct(){
            include "./Lib/Db.php";
            $this->_db = new Db("localhost","root","123456","test");
        }
        function getList(){
            $this->_db->query("......");//这里具体sql语句就省略不写了
        }
    }
}

namespace example_two{
    /**
     * 看上去我们实现了想要的功能，但是这是一个噩梦的开始，以后example1,example2,example3....越来越多的类需要用到db组件，
     * 如果都这么写的话，万一有一天数据库密码改了或者db类发生变化了，岂不是要回头修改所有类文件？
     * ok，为了解决这个问题，工厂模式出现了，我们创建了一个Factory方法，并通过Factory::getDb()方法来获得db组件的实例：
     */
    class Factory {
        public static function getDb(){
            include "./Lib/Db.php";
            return new Db("localhost","root","123456","test");
        }
    }
    //example类变成：
    class example {

        private $_db;
        function __construct(){
            $this->_db = Factory::getDb();
        }
        function getList(){
            $this->_db->query("......");//这里具体sql语句就省略不写了
        }
    }
}

namespace example_three{
    /**
     * 这样就完美了吗？再次想想一下以后example1,example2,example3....所有的类，你都需要在构造函数里通过Factory::getDb();
     * 获的一个Db实例，实际上你由原来的直接与Db类的耦合变为了和Factory工厂类的耦合，
     * 工厂类只是帮你把数据库连接信息给包装起来了，虽然当数据库信息发生变化时只要修改Factory::getDb()方法就可以了，
     * 但是突然有一天工厂方法需要改名，或者getDb方法需要改名，你又怎么办？当然这种需求其实还是很操蛋的，
     * 但有时候确实存在这种情况，一种解决方式是：
     *
     * 们不从example类内部实例化Db组件，我们依靠从外部的注入，什么意思呢？看下面的例子
     */
    class example {
        private $_db;
        function getList(){
            $this->_db->query("......");//这里具体sql语句就省略不写了
        }
        //从外部注入db连接 从外部注入
        function setDb($connection){
            $this->_db = $connection;
        }
    }

    /**
     * 这样一来，example类完全与外部类解除耦合了，你可以看到Db类里面已经没有工厂方法或Db类的身影了。
     * 我们通过从外部调用example类的setDb方法，将连接实例直接注入进去。这样example完全不用关心db连接怎么生成的了。
     * 这就叫依赖注入，实现不是在     代码内部创建依赖关系，而是让其作为一个参数传递，
     * 这使得我们的程序更容易维护，降低程序代码的耦合度，实现一种松耦合。
     */
    //调用
    $example = new example();
    $example->setDb(Factory::getDb());//注入db连接
    $example->getList();

    //这还没完，我们再假设example类里面除了db还要用到其他外部类，我们通过：    $example->setDb(Factory::getDb());//注入db连接
    $example->setFile(Factory::getFile());//注入文件处理类
    $example->setImage(Factory::getImage());//注入Image处理类
    //...

    //我们没完没了的写这么多set？累不累? ok，为了不用每次写这么多行代码，我们又去弄了一个工厂方法：
    class Factory {
        //直接返回一个注入好的  example  实例
        public static function getExample(){
            //在工厂方法里完成类的依赖注入  注入的时候也用到工厂方法
            $example = new example();
            $example->setDb(Factory::getDb());//注入db连接
            $example->setFile(Factory::getFile());//注入文件处理类
            $example->setImage(Factory::getImage());//注入Image处理类
            return $example;
        }
    }

    //实例化example时变为：
    $example=Factory::getExample();
    $example->getList();

   // 似乎完美了，但是怎么感觉又回到了上面第一次用工厂方法时的场景？这确实不是一个好的解决方案，所以又提出了一个概念：容器，又叫做IoC容器、DI容器。

}

namespace example_four{
    /**
     * 我们本来是通过setXXX方法注入各种类，代码很长，方法很多，虽然可以通过一个工厂方法包装，但是还不是那么爽，
     * 好吧，我们不用setXXX方法了，这样也就不用工厂方法二次包装了，那么我们还怎么实现依赖注入呢？
     *
     * 这里我们引入一个约定：在example类的构造函数里传入一个名为Di $di的参数，如下
     */
    class example {
        private $_di;
        //引用一个 Di 的实例
        function __construct(Di &$di){
            $this->_di = $di;
        }
        //通过di容器获取db实例
        function getList(){
            $this->_di->get('db')->query("......");//这里具体sql语句就省略不写了
        }
    }

    class Di{
        private $di;
        private static $instance;
        private function __construct()
        {
            //单例
        }

        static public function instance()
        {
            if(empty(self::$instance))self::$instance = new self();
            return self::$instance;
        }

        public function set($name, $closure)
        {
            if($closure instanceof \Closure){
                //call_user_func_array($closure, []);
                $this->$name = call_user_func($closure);
            }
        }

        public function get($instance)
        {
            return $this->$instance;
        }
    }
    $di = Di::instance();
    $di->set("db",function(){
        return new Db("localhost","root","root","test");
    });

    //给构造方法传入容器实例
    $example = new example($di);
    $example->getList();

    /**
     * Di就是IoC容器，所谓容器就是存放我们可能会用到的各种类的实例，
     * 我们通过$di->set()设置一个名为db的实例，因为是通过回调函数的方式传入的，所以set的时候并不会立即实例化db类，
     * 而是当$di->get('db')的时候才会实例化，同样，在设计di类的时候还可以融入单例模式。
     *
     * 这样我们只要在            全局范围内申明一个Di类，将所有需要注入的类放到容器里，
     *
     * 然后将容器作为构造函数的参数传入到example，即可在example类里面从容器中获取实例。当然也不一定是构造函数，
     * 你也可以用一个 setDi(Di $di)的方法来传入Di容器，总之约定是你制定的，你自己清楚就行。
     * 这样一来依赖注入以及关键的容器概念已经介绍完毕，剩下的就是在实际中使用并理解它吧！
     */
}

//http://www.cnblogs.com/liuhaorain/p/3747470.html
namespace {
    /**
     * 深入理解DIP、IoC、DI以及IoC容器
     *
     * 面向对象设计（OOD）有助于我们开发出高性能、易扩展以及易复用的程序。其中，OOD有一个重要的思想那就是依赖倒置原则（DIP），
     * 并由此引申出IoC、DI以及Ioc容器等概念。通过本文我们将一起学习这些概念，并理清他们之间微妙的关系。
     *
     * 总结
     * 在本文中，我试图以最通俗的方式讲解，希望能帮助大家理解这些概念。下面我们一起来总结一下：
     *
     * DIP是软件设计的一种思想，IoC则是基于DIP衍生出的一种软件设计模式。
     *
     * DI是IoC的具体实现方式之一，使用最为广泛。
     *
     * IoC容器是DI构造函注入的框架，它管理着依赖项的生命周期以及映射关系。
     *
     * IoC -> DIP  -> DI
     */

    //todo:依赖倒置原则（DIP）
    /**
     * 依赖倒置原则，它转换了依赖，
     * 上层模块不依赖于底层模块的实现，而底层模块依赖于上层模块定义的接口。
     * 通俗的讲，就是上层模块定义接口，底层模块负责实现。
     *
     * Bob Martins对DIP的定义：
     * 上层模块不应依赖于底层模块，两者应该依赖于抽象。
     * 抽象不不应该依赖于实现，实现应该依赖于抽象。
     *
     * DIP的优点：
     * 系统更柔韧：可以修改一部分代码而不影响其他模块。
     * 系统更健壮：可以修改一部分代码而不会让系统崩溃。
     * 系统更高效：组件松耦合，且可复用，提高开发效率。
     */

    /**
     * 控制反转（IoC）
     * DIP是一种 软件设计原则，它仅仅告诉你两个模块之间应该如何依赖，但是它并没有告诉如何做。
     * IoC则是一种 软件设计模式，它告诉你应该如何做，来解除相互依赖模块的耦合。
     * 控制反转（IoC），它为相互依赖的组件提供抽象，
     * 将依赖（低层模块）对象的获得交给第三方（系统）来控制，
     * 即依赖对象不在被依赖模块的类中直接通过new来获取。
     * 在图1的例子我们可以看到，ATM它自身并没有插入具体的银行卡（工行卡、农行卡等等），
     * 而是将插卡工作交给人来控制，即我们来决定将插入什么样的银行卡来取钱。
     * 同样我们也通过软件开发过程中场景来加深理解。
     */

    //TODO：场景
    /**
     * 做过电商网站的朋友都会面临这样一个问题：
     * 订单入库。假设系统设计初期，用的是SQL Server数据库。通常我们会定义一个SqlServerDal类，用于数据库的读写
     */

    class SqlServerDal
    {
        public function Add()
        {
            //todo:添加一条数据
        }
    }

    /**
     * 然后我们定义一个Order类，负责订单的逻辑处理。
     * 由于订单要入库，需要依赖于数据库的操作。
     * 因此在Order类中，我们需要定义SqlServerDal类的变量并初始化。
     */
     class Order
    {
        private $dal;

        public function __call($name, $arguments)
        {
            // TODO: Implement __call() method.
            $this->dal = new SqlServerDal();//添加一个私有变量保存数据库操作的对象
        }

         public function Add()
        {
            $this->dal->Add();
        }
    }


    /**
     * 依赖注入（DI）
     * 控制反转（IoC）一种重要的方式，就是将依赖对象的创建和绑定转移到被依赖对象类的外部来实现。
     * 在上述的实例中，Order类所依赖的对象SqlServerDal的创建和绑定是在Order类内部进行的。
     * 事实证明，这种方法并不可取。
     * 既然，不能在Order类内部直接绑定依赖关系，那么如何将SqlServerDal对象的引用传递给Order类使用呢？
     *
     * 依赖注入（DI），它提供一种机制，将需要依赖（低层模块）对象的引用传递给被依赖（高层模块）对象。
     * 通过DI，我们可以在Order类的外部将SqlServerDal对象的引用传递给Order类对象。那么具体是如何实现呢？
     */

    //todo:方法一 构造函数注入
    /**
     * 构造函数函数注入，毫无疑问通过构造函数传递依赖。
     * 因此，构造函数的参数必然用来接收一个依赖对象。那么参数的类型是什么呢？
     * 具体依赖对象的类型？还是一个抽象类型？根据DIP原则，我们知道高层模块不应该依赖于低层模块，两者应该依赖于抽象。
     * 那么构造函数的参数应该是一个抽象类型。
     * 我们再回到上面那个问题，如何将SqlServerDal对象的引用传递给Order类使用呢？
     *
     * 从上面我们可以看出，我们将依赖对象SqlServerDal对象的创建和绑定转移到Order类外部来实现，
     * 这样就解除了SqlServerDal和Order类的耦合关系。当我们数据库换成Access数据库时，
     * 只需定义一个AccessDal类，然后外部重新绑定依赖，不需要修改Order类内部代码，则可实现Access数据库的操作。
     */
    //首选，我们需要定义SqlServerDal的抽象类型IDataAccess，并在IDataAccess接口中声明一个Add方法
     interface IDataAccess
    {
        public function Add();
    }
    //然后在SqlServerDal类中，实现IDataAccess接口。
     class SqlServerDal_ implements IDataAccess
     {
        public function Add()
        {
            //todo:添加数据
        }
    }
    //接下来，我们还需要修改Order类。
    class Order_
    {
        private  $_ida;//定义一个私有变量保存抽象

            //构造函数注入
        public function __construct(IDataAccess $ida)
        {
            $this->_ida = $ida;//传递依赖
        }

        public function Add()
        {
            $this->_ida->Add();
        }
    }

    //todo:方法二 属性注入
    //顾名思义，属性注入是通过属性来传递依赖。因此，我们首先需要在依赖类Order中定义一个属性：
    class Order__
    {
        private $_ida;  //定义一个私有变量保存抽象

        //给属性赋值
        public function set(IDataAccess $_ida)
        {
            $this->_ida = $_ida;
            return $this->_ida;
        }

        public function Add()
        {
            $this->_ida->Add();
        }
    }

    //使用
    $use = new Order__();
    //注入依赖
    $use->set(new \SqlServerDal_());
    $use->Add();

    //todo:方法三 接口注入
    //相比构造函数注入和属性注入，接口注入显得有些复杂，使用也不常见。具体思路是先定义一个接口，包含一个设置依赖的方法。然后依赖类，继承并实现这个接口。
    interface IDependent
    {
        public function SetDependence(IDataAccess $_ida);
    }

    class Order_3 implements IDependent
    {
        private  $_ida;//定义一个私有变量保存抽象

        public function SetDependence(IDataAccess $_ida)
        {
            // TODO: Implement SetDependence() method.
            $this->_ida = $_ida;
        }

        public function Add()
        {
            $this->_ida->Add();
        }
    }

    //使用
    $use = new Order_3();
    $use->SetDependence(new \SqlServerDal_());
    $use->Add();



    //todo:IoC容器
    //前面所有的例子中，我们都是通过手动的方式来创建依赖对象，并将引用传递给被依赖模块。比如：

    /**
     * 对于大型项目来说，相互依赖的组件比较多。如果还用手动的方式，自己来创建和注入依赖的话，显然效率很低，而且往往还会出现不可控的场面。正因如此，IoC容器诞生了。
     * IoC容器实际上是一个DI框架，它能简化我们的工作量。它包含以下几个功能：
     * 动态创建、注入依赖对象。
     * 管理对象生命周期。
     * 映射依赖关系。
     */
}
?>