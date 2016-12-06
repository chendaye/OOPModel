<?php
    /**
     * 反射
     * 由一系列分析  属性 方法 类  的内置类组成
     * 可以看编译到的PHP的扩展
     *
     * 部分类
     * Reflection   为类的摘要信息提供静态函数export()
     * ReflectionClass  类信息和工具
     * ReflectionMethod     类方法信息和工具
     * ReflectionPaeameter      方法参数信息
     * ReflectionProperty       类属性信息
     * ReflectionFunction       函数信息和工具
     * ReflectionExtension      PHP扩展信息
     * ReflectionException      错误类
     */
    //TODO:ReflectionClass  类信息和工具,提供揭示类信息的方法，以类名为参数，不论是自定义类还是内置类
    class Refle{
        public $re = 're';
        public function __construct(){
        }
        public function action(){
            echo 777;
        }
    }
    $ret = new Refle();
    $reflection = new ReflectionClass($ret);
    //TODO:输出类的信息,需要一个ReflectionClass的实例
    Reflection::export($reflection);

    /**
     * 检查类
     */
    function classData(ReflectionClass $class){
        $detail = '';
        $name = $class->getName();
        if($class->isUserDefined()){
            $detail .= "$name 没有定义\n";
        }
        if($class->isInternal()){
            $detail .= "$name 是内置类\n";
        }
        if($class->isInterface()){
            $detail .= "$name 是接口\n";
        }
        if($class->isAbstract()){
            $detail .= "$name 是抽象类\n";
        }
        if($class->isFinal()){
            $detail .= "$name 是不能继承的类\n";
        }
        if($class->isInstantiable()){
            $detail .= "$name 是可行\n";
        }
        return $detail;
    }
    echo classData($reflection);
    //TODO:获取源代码
    class ReflectionDemo{
        public static function getDemo(ReflectionClass $class){
            //获取类所在的文件名
            $name = $class->getFileName();
            //file() 函数把整个文件读入一个数组中,
            //与 file_get_contents() 类似，不同的是 file() 将文件作为一个数组返回。数组中的每个单元都是文件中相应的一行，包括换行符
            //把文件以行为单位读入数组
            $line = @file($name);
            $from = $class->getStartLine(); //类的起始行
            $to = $class->getEndLine(); //类的结束行
            $len = $to-$from+1;
            //截取代码数组，获取类的代码
            return implode(array_slice($line, $from-1,$len));
        }
    }
    echo ReflectionDemo::getDemo($reflection);
?>