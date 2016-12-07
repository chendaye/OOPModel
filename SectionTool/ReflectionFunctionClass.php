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
        public $df = 're';
        public function __construct(){
        }
        public function action($a = 1, $b = 2){
            $c =$a+$b;
            echo $c;
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
    /**
     * \检查方法
     * ReflectionClass用于检查类，与之类似ReflectionMethod对象可以用来检查类中的方法
     * 获得ReflectionMethod对象的方法有两种
     * 一、ReflectionClass::getMethods()获得ReflectionMethod对象的数组，参数是一个参数对象
     * 二、若要使用特定的类方法ReflectionClass::getMethod()可以接受一个方法名，相应返回ReflectionMethod对象，参数是一个方法名
     * 一个复数一个单数
     */
    $med = new Refle();
    //获取反射对象
    $ref = new ReflectionClass($med);
    //ReflectionMethod对象,数组
    $method = $ref->getMethods();
    //遍历检查所有方法
    foreach ($method as $methods){
        echo methosData($methods)."\n";
    }
    function methosData(ReflectionMethod $method){
        $detail = '';
        $name = $method->getName();
        if($method->isUserDefined()){
            echo "$name 是用户定义的";
        }
        if($method->isInternal()){
            echo "$name 是内置的";
        }
        if($method->isAbstract()){
            echo "$name 是抽象的的";
        }
        if($method->isPublic()){
            echo "$name 是公共的";
        }
        if($method->isProtected()){
            echo "$name 是保护的";
        }
        if($method->isPrivate()){
            echo "$name 是私有的";
        }
        if($method->isStatic()){
            echo "$name 是静态的";
        }
        if($method->isFinal()){
            echo "$name 是不可覆写的";
        }
        if($method->isConstructor()){
            echo "$name 是构造函数";
        }
        if($method->returnsReference()){
            echo "$name 返回一个引用";
        }
    }
    /**
     * 获取类方法的源代码
     */
    class ReflectionMethodDemo{
        public static function getDemo(ReflectionMethod $methos){
            //获取类所在的文件名
            $name = $methos->getFileName();
            //file() 函数把整个文件读入一个数组中,
            //与 file_get_contents() 类似，不同的是 file() 将文件作为一个数组返回。数组中的每个单元都是文件中相应的一行，包括换行符
            //把文件以行为单位读入数组
            $line = @file($name);
            $from = $methos->getStartLine(); //类的起始行
            $to = $methos->getEndLine(); //类的结束行
            $len = $to-$from+1;
            //截取代码数组，获取类的代码
            return implode(array_slice($line, $from-1,$len));
        }
    }
    //TODO:获取单个方法对象
    $meth = $ref->getMethod('action');
    echo ReflectionMethodDemo::getDemo($meth);

    /**
     * 检查方法参数
     * 反射API提供ReflectionParameter,检查方法的参数
     * 要获得ReflectionParameter对象
     * ReflectionMethod::ReflectionParameters() 可返回ReflectionParameter对象数组
     */
    //反射方法对象
    $param = $meth->getParameters(); //参数对象
    //遍历检查所有参数
    foreach ($param as $par){
        echo argData($par)."\r\n";
    }
    function argData(ReflectionParameter $arg){
        $details = '';
        $declaringclass = $arg->getDeclaringClass();
        $name = $arg->getName();
        $class = $arg->getClass();
        $position = $arg->getPosition();
        $details = "$name has position $position\r\n";
        if(!empty($class)){
            $classname = $class->getName();
            $details .= "$name 属于对象  $classname\r\nb";
        }
        if($arg->isPassedByReference()){
            $details .= "$name 是引用\r\nb";
        }
        if($arg->isDefaultValueAvailable()){
            //默认值
            $def = $arg->getDefaultValue();
            $details .= "$name 的默认值是 $def\r\nb";
        }
        return $details;
    }

    //TODO:总之，反射类是用来获取/检查，类  类的属性  类的方法  类的方法参数   基本能得到类的所有信息，非常强大
?>