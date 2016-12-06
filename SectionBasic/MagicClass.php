<?php
    /**
     * 魔术方法又叫拦截方法
     * 它可以拦截发送到未定义方法和属性的消息\
     * 类似于__construct()遇到合适的条件会被调用
     */
    class Person{
        /**
         * @param $name
         * 客户端调用未声明的属性是，此方法会被调用
         * 并且，被调用的属性名的字符串作为参数
         */
        public function __get($name){
            // TODO: Implement __get() method.
            echo $name;

        }

        /**
         * @param $name
         * @param $value
         * 客户端给未定义属性赋值时会调用
         * unset() 一个未定义的属性还 __unset('被调用的变量')会被调用
         */
        public function __set($name, $value){
            // TODO: Implement __set() method.
            echo $name.'='.$value;
        }

        public function run(){
            //TODO:some thing
        }
    }
    $person = new Person();
    echo $person->play;
    $person->play = 'lol';

    /**
     * __call()当用户调用未定义的方法是调用
     * 两个参数
     * 一个是：方法名
     * 一个是：传递给方法的所有参数（数组形式）
     *
     * 对于实现委托很有用
     * 委托是指一个对象转发或者委托一个请求给另一个对象
     */
    class Write{
        public function write(){
            return '委托成功';
        }
    }
    class Fight{
        private $write;
        public function __construct(Write $write){
            //用对象Write，初始化$this->write
            $this->write = $write;
        }
        public function __call($name, $arguments){
            // TODO: Implement __call() method.
            //判断类Write中是否有调用的未定义的方法
            if(method_exists($this->write, $name)){
                echo $this->write->$name();
            }
        }
    }
    $ret = new Fight(new Write());
    $ret->write();


?>