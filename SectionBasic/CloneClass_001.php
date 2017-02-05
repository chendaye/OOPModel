<?php

    /**
     * Class Copy
     * $first 与 $two 是指向同一个对象的引用
     * 不是两个不同的对象，没有各自保留一个对象的副本
     */
    class Copy{}
    $first = new Copy();
    $two = $first;

    /**
     * 有时我们需要一个对象的副本
     * 此时我们需要clone
     */
    //TODO:此时 $first $three 是两个对象而不是一个对象的两个引用
    $three = clone $first;

    /**
     * 有时克隆对象的时候，需要做一些处理
     * 譬如不能让两个对象中的id指向数据库中同一条数据
     * 此时就需要 __clone() 方法
     * 它在克隆是自动调用
     * 注意它是运行在 克隆出来的对象上，而不是原对象上
     */
    class CloneMe {
        public $id = 6;
        public function clonee(){
            echo '克隆';
        }
        //TODO:运行在克隆出来的对象上
        public function __clone(){
            // TODO: Implement __clone() method.
            $this->id = 0;
        }
    }
    $me = new CloneMe();
    $you = clone $me;
    echo $you->id;
    echo $me->id;

    /**
     * clone 属于浅复制
     * 所有基本类型属性被完全复制
     * 对象只复制引用
     *
     * 但有时会有问题，比如对象中有一个账户，是对象，
     * 因为是同一个账户对象的引用
     * 在被复制的对象中操作账户，会影响原对象中的账户，
     * 显然这样是不行的
     *
     * 这是可以在 __clone()中显示指定，复制的对象
     */
    class Acount{
        public $money = 10000000;
    }
    class man{
        private $name;
        private $age;
        private $acount;
        public function __construct($name, $age, Acount $acount){
            $this->name = $name;
            $this->age = $age;
            $this->acount = $acount;
        }
        public function DoD(){
            //TODO:some thing
        }
        public function __clone(){
            // TODO: 显式指定克隆内容
            $this->acount = clone $this->acount;
        }
    }
?>