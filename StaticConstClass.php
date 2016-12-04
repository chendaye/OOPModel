<?php
    /**
     * 静态方法和属性，要用 类 或 static 来访问，   静态成员的访问呢不需要类的实例，它不属于任何实例
     * 静态方法不能访问一般属性，只能访问静态属性，因为一般属性属于实例
     * 不能再对象中调用静态方法，也不能在静态方法中使用 $this
     * 在类的内部 用 self::静态属性  来访问静态属性
     * 在类的外部 用 类名::静态成员  来访问静态成员
     * 静态方法和静态成员，又称类变量和属性
     *
     * 为什么要用静态成员
     * 1、静态成员在代码任何地方可用
     * 2、每个实例都可以访问静态属性，可以用静态属性设置值
     * 3、无需实例，即可访问，这样可以不必为了一个简单的功能来实例化一个类
     *
     * 常量属性只包含基本数据类型，不能包含对象
     * 常量的访问也只能通过类名来访问，而不是通过实例来访问
     */
    class StaticExample{
        const CHENBAIBAI = 'chen';
        static public $name = 'chendaye';
        static public function say(){
            $ret = self::$name;
            print'阳光灿烂'.$ret;
        }
    }
    echo StaticExample::$name;
    StaticExample::say();
    echo StaticExample::CHENBAIBAI;
?>