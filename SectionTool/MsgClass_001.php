<?php
     /**
      * 自动加载
      * 当php 遇到实例化未知的类 的操作会自动调用 __autoload()
      * 并且将类名当做参数传递给它
      */
     function __autoload($classname){
         //TODO:根据类名定位到类文件所在的位置，这就需要自定义类文件的命名方法和文件结构，统一规范
         //TODO:这样才能通过类名定位到类文件，比如可以通过命名空间解析数文件路径
         require ("$classname.php");
     }

     /**
      * php 提供了一系列函数来检测类和对象
      */
    //TODO:php可以用字符串动态引用类
    class Task{
        public $chen = 'shuai';
        private $daye = 'Cool';
        public function __construct(){
        }

        public function get(){
            echo '动态引用类';
        }
    }
    $classname = 'Task';
    $ret = new $classname();
    $ret->get();
    //TODO:class_exists()可以判断类是否存在,接受表示类的字符串
    if(class_exists($classname)){
        echo '类存在';
    }
    //TODO:get_declared_classes()可以用来获得脚本进程中所有定义的类的数组
    print_r(get_declared_classes());
    //TODO:get_class()得到对象所属的类
    if(get_class($ret) == 'Task'){
        echo get_class($ret);
    }
    //TODO:instanceof 关键字，检查对象属于哪一个家族(类或接口)
    if($ret instanceof Task){
        echo '没错就是葬爱家族！';
    }
    //TODO:get_class_methods('Task') 获取类中所有的方法列表，数组形式
    print_r(get_class_methods('Task'));
    //TODO:get_class_vars('Task') 获取类中所有的 public属性列表，数组形式
    print_r(get_class_vars('Task'));
    //TODO:get_parent_class();获取类的父类，如果没有父类返回false
    class son extends Task {
        public $son = 'son';
        public function __construct(){
        }
        public function run($man){
            echo $man;
        }
    }
    if(get_parent_class('son')){
        echo get_parent_class('son');
    }
    //TODO:is_subclass_of()判断一个类是不是一个类的子类，是返回true不是返回false
    if(is_subclass_of('son','Task')){
        echo '是继承关系';
    }
    //TODO:动态调用函数
    $play = new son();
    $play->run(20);
    //TODO:等价于
    //调用类中的方法，如下，调用一般方法把方法名传进来就行
    call_user_func(array($play, 'run'), 20);
    //TODO:此方法与上唯一的不同就是把，所有参数都当做数组来接受
    call_user_func_array(array($play, 'run'), 20);
?>