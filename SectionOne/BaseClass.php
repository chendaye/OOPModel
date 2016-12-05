<?php

    /**
     * Class ShopProducts
     * 属性用来存类所需的数据
     * 构造函数用来初始化数据
     */
    class ShopProducts{
        public $title = 'grass';
        public $last_name = 'last';
        public $first_name = 'first';
        public function __construct($tiele, $firstName, $lastName){
            $this->title = $tiele;
            $this->last_name = $lastName;
            $this->first_name = $firstName;
        }

        public function getName(){
            return $this->first_name;
        }
    }
    $name = new ShopProducts('chen', 'chen', 'daye');
    echo $name->getName();

    /**
     * 每定义一个类都定义了一个数据类型
     * php是弱类型语言，当使用了与预期类型不一致的数据，不会报错，而是php把它转化为与环境对应的数据类型
     * 因此很多时候就需要人为的限制数据类型
     * php提供了一下函数：
     */
    $bool = false;
    if(is_bool($bool)){
        echo '这是判断bool类型';
    }
    $int = 12;
    if(is_int($int)){
        echo '这是判断整型';
    }
    $double = 12.00;
    if(is_double($double)){
        echo '这是判断双精度';
    }
    $string = 'abcdefg';
    if(is_string($string)){
        echo '这是判断字符串';
    }
    $class = new ShopProducts('a', 'b', 'c');
    if(is_object($class)){
        echo '这是判断类';
    }
    $array = array();
    if(is_array($array)){
        echo '这是判断数组';
    }
    $rescorce = 'rescorce';
    if(!is_resource($rescorce)){
        echo '这是判断资源类型';
    }

    /**
     * 限制对象的类型
     * 对象也是一般变量
     */
    class ProductWite{
        public function write(ShopProducts $shopProducts){
            echo $shopProducts->getName();
        }
        public function ArrayTip(array $msg){
            echo '$msg 必须是数组';
        }
    }
    $write = new ProductWite();
    echo $write->write(new ShopProducts('git', 'very', 'good'));
    /**
     *对象类型判断
     */
    if($write instanceof ProductWite){
        echo '$write 是 ProductsWrite 的子类';
    }
    /**
     *继承
     * 访问限制中  私有  和  保护  类型 属于类 内部的东西 不向外部开放
     */
    class CDproducts extends ShopProducts {
        public $new;
        //调用父类的构造方法，同样也要给定对应变量
        public function __construct($tiele, $firstName, $lastName, $new){
            parent::__construct($tiele, $firstName, $lastName);
            $this->new = $new;
        }
        //覆写父类的方法，调用父类被覆写的方法
        public function getName(){
            echo parent::getName();
            echo '$name';
            echo $this->new;
        }
    }
    $extent = new CDproducts('title', 'firstname', 'lastname', 'new');
    $extent->getName();
?>

