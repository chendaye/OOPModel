<?php
    /**
     * 抽象类中只定义或部分实现，子类中需要的方法
     * 子类可以继承抽象类，并通过实现其中的抽象方法，使抽象类具体化
     * 和普通类一样可以创建方法和属性
     */
    abstract class Example{
        public $name;
        abstract public function write();
        public function read(){
            print 'ni da ye';
        }

    }
    class Person extends Example {
        //TODO:具体实现抽象方法
        public function write(){
            // TODO: Implement write() method.
            $a = 1+1;
            echo $a;
        }
    }
    $result = new Person();
    $result->read();
    //TODO:动态为属性赋值
    echo $result->name = 2;
    $result->write();
    echo $result->name;
    /**
     * 接口
     * 抽象类提供了具体实现标准
     * 接口是纯粹的模板，只定义功能，不提供具体实现
     * 接口可以包含属性、方法声明
     */
    interface People{
        //TODO:定义功能
        public function run();
        public function work();
        public function smile();
    }
    class girls implements People {
        //TODO:具体实现功能
        public function run(){
            print '1000km/h';
        }
        public function work(){
            print '3 days';
        }
        public function smile(){
            print 'happy';
        }
    }
    $xuan = new girls();
    $xuan->run();
    $xuan->work();
    $xuan->smile();
    /**
     * 一个类可以同时继承父类和实现分多个接口
     */
    class Chen{
        public function boss(){
        }
    }
    interface Man{
        public function strong();
    }
    interface Woman{
        public function week();
    }
    class DaYe extends Chen implements Man, Woman{
        public function strong(){
            // TODO: Implement strong() method.
        }
        public function week(){
            // TODO: Implement week() method.
        }
    }

?>