<?php
    /**
     * static可用于实例化自身
     * 还和self parent 一样作为静态成员的标识符，调用静态成员
     */
    abstract class StaticTest{
        public static $key = 2;
        public static function create(){
            //TODO:实例化自身
            return new static();
        }
    }
    class TestOne extends StaticTest {
        public function one(){
            print 'one';
        }
    }
    class TestTwo extends StaticTest {
        public function two(){
            print 'two';
        }
    }
    //TODO:返回一个自身的实例
    $one = TestOne::create();
    $one->one();
    $two = TestTwo::create();
    $two->two();
    /**
     * static 作为静态成员的标识符
     */
    class TestSym{
        public static $key = 6;
        public function test(){
            //TODO:静态成员标识符
            echo static::$key;
        }
    }
    $ret = new TestSym();
    $ret->test();
?>