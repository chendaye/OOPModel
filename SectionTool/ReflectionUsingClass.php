<?php
    /**
     * 反射类的应用
     * 创建一个Module对象，该类可以自由加载第三方插件，集成进自己的系统
     * 不需要把代码硬编码进自己的系统
     * 要实现这个效果，可以在module接口或者抽象类中定义一个execute()方法，强制所有子类都实现此方法
     * 用户可以在外部配置一个xml文件，列出所有的Module类
     * 系统可以根据xml信息加载一定数目的Module对象，然后对每个对象调用execute()方法
     */
    class person{
        public $name;
        public  function __construct($name){
            $this->name = $name;
        }
    }
    //TODO:接口,每一个接口类都必须要实现exwcute()
    interface Module{
        public function exwcute();
    }
    //接口类
    class FtpModule implements Module {
        public function setHost($host){
            //TODO:
        }
        public function setuser($user){
            //TODO:
        }
        //TODO:实现接口
        public function exwcute(){
            // TODO: Implement exwcute() method.
        }
    }
    //接口类
    class PersonModule implements Module {
        public function setPerson(person $person){
            //TODO:
        }
        public function setMan($man){
            //TODO:
        }
        //TODO:实现接口
        public function exwcute(){
            // TODO: Implement exwcute() method.
        }
    }
    /**
     * PersonModule 和 FtpModule 都提供了exectue()的空实现
     * 每个类都有seter方法，这些方法只告诉自己被调用
     * 同时seter方法都有一个参数：要么是一个字符串  要么是一个可以用字符串参数来实例化的对象
     * 比如setPerson(person $person)
     *
     * 要使用类PersonModule  FtpModule   要创建一个ModuleRunner类、
     * 它使用  使用 键名为模块名（类名）的多维数组来展示xml信息
     *
     * init()运行的时候ModuleRunner对象存放这个很多Module对象，所有Module对象都包含数据
     * ModuleRunner类可以用一个类方法来遍历每个Module方法，并逐一调用各自的execute()方法
     */
    class ModuleRunner{
        private $configData = array(
            "PersonModule" => array('person' => 'bob'),
            "FtpModule" => array('host' => 'example.com','user' => 'anon')
        );
        private $modules = array();

        /**
         * @throws Exception
         * init()用于创建正确的Module对象
         * 它遍历$configData数组，为每个模块创建ReflectionClass对象，
         * 用不存在的类名调用ReflectionClass的构造方法是会抛出异常
         * isSubclassOf($interface)来确保模块属于Module类
         *
         * 在调用exectue()方法前，用$module = $module_class->newInstance();来创建Module实例
         * newInstance()方法可以接受任意数目的参数，并且传递这些参数到对应类的构造方法
         * 若一切正常，返回类的实例
         *
         * getMethods()返回一个包含所有可用的ReflectionMethod对象的数组
         */
        public function init(){
            $interface = new ReflectionClass('Module');
            //TODO:遍历Module接口的所有子类，并对每个子类，用反射方法来检查处理；
            //TODO:说白了反射就是能够利用一个类的名称，来取得该类内部的几乎所有信息
            foreach ($this->configData as $modulename => $param){
                //TODO:获取每一个对象的反射对象
                $module_class = new ReflectionClass($modulename);
                //TODO:如果不是继承自Module接口
                if(!$module_class->isSubclassOf($interface)){
                    throw new Exception("不是 $modulename 类型");
                }
                //TODO:把参数传给对象的构造方法，并且返回实例
                $module = $module_class->newInstance();
                //TODO:遍历该被反射对象的所有方法
                foreach ($module_class->getMethods() as $method){
                    //TODO:handleMethod($module, $method, $param)用来检验并调用Module的seter方法
                    //TODO:$module 》Module类的实例   $method 》 该类的一个方法  $param 》 该类的属性数组
                    $this->handleMethod($module, $method, $param);
                }
                //TODO:array_push() 函数向第一个参数的数组尾部添加一个或多个元素（入栈），然后返回新数组的长度
                array_push($this->modules, $module);
            }
        }

        /**
         * @param Module $module
         * @param ReflectionMethod $method
         * @param $params
         * @return bool
         *
         * handleMethod($module, $method, $param)用来检验并调用Module的seter方法
         * 先检查方法是否为有效的setter方法
         *
         * invoke（）它以一个对象和任意数目的方法作为参数，如果对象与方法不匹配救护抛出异常
         */
        public function handleMethod(Module $module, ReflectionMethod $method, $params){
            //TODO:方法名
            $name = $method->getName();
            //TODO:方法参数
            $args = $method->getParameters();
            //TODO:参数个数不为1,或者方法名不满足要求
            if(count($args) != 1 || substr($name, 0 ,3) != 'set'){
                return false;
            }
            //TODO:获取方法名,方法名不存在返回false
            $property = strtolower(substr($name, 3));
            if(!isset($params[$property])){
                return false;
            }
            $arg_class = $args[0]->getClass();
            if(empty($arg_class)){
                $method->invoke($module, $params[$property]);
            }else{
                $method->invoke($module, $arg_class->newInstance($params[$property]));
            }
        }
    }
?>