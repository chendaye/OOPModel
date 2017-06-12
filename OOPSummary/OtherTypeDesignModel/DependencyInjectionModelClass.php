<?php
/**
 * 依赖注入和控制反转（同义词）
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
        //从外部注入db连接
        function setDb($connection){
            $this->_db = $connection;
        }
    }

    /**
     * 这样一来，example类完全与外部类解除耦合了，你可以看到Db类里面已经没有工厂方法或Db类的身影了。
     * 我们通过从外部调用example类的setDb方法，将连接实例直接注入进去。这样example完全不用关心db连接怎么生成的了。
     * 这就叫依赖注入，实现不是在代码内部创建依赖关系，而是让其作为一个参数传递，
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
        public static function getExample(){
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
        function __construct(Di &$di){
            $this->_di = $di;
        }
        //通过di容器获取db实例
        function getList(){
            $this->_di->get('db')->query("......");//这里具体sql语句就省略不写了
        }
    }
    $di = new Di();
    $di->set("db",function(){
        return new Db("localhost","root","root","test");
    });
    $example = new example($di);
    $example->getList();

    /**
     * Di就是IoC容器，所谓容器就是存放我们可能会用到的各种类的实例，
     * 我们通过$di->set()设置一个名为db的实例，因为是通过回调函数的方式传入的，所以set的时候并不会立即实例化db类，
     * 而是当$di->get('db')的时候才会实例化，同样，在设计di类的时候还可以融入单例模式。
     * 这样我们只要在全局范围内申明一个Di类，将所有需要注入的类放到容器里，
     * 然后将容器作为构造函数的参数传入到example，即可在example类里面从容器中获取实例。当然也不一定是构造函数，
     * 你也可以用一个 setDi(Di $di)的方法来传入Di容器，总之约定是你制定的，你自己清楚就行。
     * 这样一来依赖注入以及关键的容器概念已经介绍完毕，剩下的就是在实际中使用并理解它吧！
     */
}
?>