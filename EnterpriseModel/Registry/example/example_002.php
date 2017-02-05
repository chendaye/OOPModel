<?php
/**
 * 作用域：常用于描述代码中对象或值得可见程度
 *请求级别作用域：
 * 变量的生命周期可以用时间来衡量
 * 变量的作用域有3个级别；标准级别是指一个HTTP请求开始到结束的周期
 *
 * 会话级别作用域：
 * php内置了对会话变量的支持
 * 在一次请求结束后，会话变量会被序列化并储存到文件系统或数据库中，然后在下一次请求开始时取回
 * 因此可以认为某些变量拥有会话级别的作用域，利用这一点可以在几次请求之间存放对象，保存用户访问的踪迹到数据库中
 * 本质上，对象也是变量  所以这样处理很正常
 * 但是要避免持有同一个对象的不同版本，因此当把一个会话对象存到数据库是要考虑一定的锁定策略
 * 所谓同一个对象的不同版本：
 * 人 是一个类   小明 是一个具体的实例；小明 一年长高5厘米  ，小明还是小明 但是不是一年前的小明
 * 也就是所谓同一个对象的不同版本
 * 由此可知：一个对象的实例  不是一成不变的（就算是普通的变量，值也会变化），所以同一个对象可能有很多版本
 *
 * 应用程序级别作用域：
 * 在 java perl 中，有一个应用程序作用域的概念：内存中的变量可以被程序中所有对象实例访问
 * 这和php 有很大不同，单是在更大规模的应用中，为访问配置变量，通过 应用程序级别的数据很有用
 * 可以通过构建一个注册表类来模拟 程序作用域
 */
namespace Angular\Registry\Scope;
/**
 * Class Registry
 * @package Angular\Registry\Scope
 * Registry 基类定义两个  protecteed 方法： get()  set();客户端代码不能直接使用它们
 * 基类也可以定义其他 public 方法 isEmpty() isPopulated() clear()
 * 可以在各个子类中保留具体的 get() set() 方法，而在特定的领域类中定制  public 的 getA() setA() 方法
 * 且定制对象会成为单例对象
 * 通过这种方法，可以实现 重用核心的储存获取操作，即在多个项目中重复使用同一个注册表
 */
abstract class Registry{
    abstract protected function get($key);
    abstract protected function set($key, $val);
}
//TODO:请求级别的注册表
class RequestRegistry extends Registry {
    private $values = array();
    private static $instance;
    private function __construct(){}
    public static function insatnce(){
        if(!isset(self::$instance)){
            self::$instance = new self();   //单例
        }
        return self::$instance;
    }
    protected function get($key)
    {
        return $this->values[$key]; //取
    }
    protected function set($key, $val)
    {
        $this->values[$key] = $val; //存
    }

    /**
     * 静态方法获取请求
     * @return mixed
     */
    public static function getRequest(){
        return self::$instance->get('request');
    }

    /**
     * 静态方法保存请求在数组中，便于集中处理
     * @param \Angular\Controller\Request $request
     * @return mixed
     */
    public static function setRequest(\Angular\Controller\Request $request){
        return self::$instance->set('request', $request);
    }
}
//TODO:会话级别的注册表
/**
 * 用session 来保存信息
 * Class SessionRegistry
 * @package Angular\Registry\Scope
 */
class SessionRegistry extends Registry {
    private static $instance;
    private function __construct(){
        session_start();    //开启session
    }
    public static function instance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance; //单例
    }
    protected function get($key)
    {
        if(isset($_SESSION[__CLASS__][$key])){
            return $_SESSION[__CLASS__][$key];  //会话信息
        }
        return null;
    }
    protected function set($key, $val)
    {
        $_SESSION[__CLASS__][$key] = $val;  //保存会话信息
    }
    public function getComplex(){
        return self::instance()->get('complex');    //子类的功能
    }
    public function setComplex(Complex $complex){
        self::instance()->set('complex',$complex);
    }
}
//TODO:应用程序级别的注册表
/**
 * 用序列化来存储和获取每个属性的值。 get()方法检查某个相应的文件是否存在
 * 如果文件存在并且上次被修改过，该方法反序列化文件内容并返回
 * 因为访问每个变量都要打开一次文件的做法效率不高，所以采用另一种办法：把所有属性都保存到一个文件中
 * set()方法改变了$key引用的属性在类中和在文件中的值，也更新了$mtime 属性的值
 * $mtime 是保存修改时间段的数组，用于检测被保存的文件是否被更新过
 * 在get()被调用时会检测 $mtime 元素，以此判断文件在上次写入之后是否被修改
 * Class ApplicationRegistry
 * @package Angular\Registry\Scope
 */
class ApplicationRegistry extends Registry {
    private static $instance;
    private $freezedir = "data";
    private $values = array();
    private $mtime = array();
    private function __contruct(){}
    public static function instance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance; //单例
    }
    protected function get($key){
        $path = $this->freezedir.DIRECTORY_SEPARATOR.$key;  //文件路径
        if(file_exists($path)){ //文件存在
            clearstatcache();
            $mtime = filemtime($path);  //创建时间
            if(!isset($this->mtime[$key])){     //$key 的创建时间不存在就为0
                $this->mtime['key'] = 0;
            }
            if($mtime > $this->mtime[$key]){    //文件被修改过
                $data = file_get_contents($path);   //获取文件内容
                $this->mtime[$key] = $mtime;
                return $this->values[$key] = unserialize($data);    //返回文件内容
            }
        }
        if(isset($this->values[$key])){     //值存在
            return $this->values[$key]; //返回值
        }
        return null;
    }
    protected function set($key, $val)
    {
        $this->values[$key] = $val;
        $path = $this->freezedir.DIRECTORY_SEPARATOR.$key;  //文件路径
        file_put_contents($path, serialize($val));  //存入文件
        $this->mtime = time();  //记录时间
    }
    public static function getDSN(){
        return self::instance()->get('dsn');
    }
    public static function setDSN($dsn){
        return self::instance()->set('dsn', $dsn);
    }
}
//TODO:如果启用了php shm扩展。就可以使用该扩展中的函数来实现注册表
//shm 在win下不可用  但是 memcache 也可以实现
class MemApplicationRegistry extends Registry {
    private static $instance;
    private $id;
    const DSN = 1;
    private function __construct(){
        $this->id = @shm_attach(55, 10000, 0600);
        if(!$this->id){
            throw new \Exception('error');
        }
    }
    public static function instance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance; //单例
    }

    /**
     * 子类首先实现 基本的存取方法（protected） 然后调用基本的存储方法 实现 公共静态的 特定的 数据存储
     * @param $key
     * @return mixed
     */
    protected function get($key){
        return shm_get_var($this->id, $key);    //取
    }
    protected function set($key, $val){
        return shm_put_var($this->id, $key, $val);  //存
    }
    protected static function getDSN(){
        return self::instance()->get(self::DSN);
    }
    protected static function setDSNN($dsn){
        return self::instance()->set(self::DSN, $dsn);
    }
}
/**
 * SessionRegistry  ApplicationRegistry 都将数据序列化后保存到文件系统
 * 因此有件事很重要：从不同其请求取回的对象是同一个对象的不同副本
 * 而不是对同一个对象的引用
 * 这对SessionRegistry 来说没什么关系，因为访问对象的是同一个用户
 * 但是对于ApplicationRegistry 来说是严重的问题：
 * 比如：进程1中修改了对象  进程2中覆盖了修改， 在两个进程中修改都要保存到文件中；这样就产生了冲突
 * 所谓进程： 就是指一个程序的运行，一个进程就是全局变量和单例的生命范围，进程信息持久化是另一回事
 *
 * 要解决冲突就要为 ApplicationRegistry 来实现一个锁定方案， 或者把ApplicationRegistry设置为只读 资源
 * 代码只有在找不到储存文件的情况下才计算新的值并写入文件，所以可以通过删除储存文件来强制重新加载配置文件
 *
 * 另外，并不是所有的对象都时候序列化输出，特别是资源类型的数据，它们无法被序列化；要想办法在序列化时处理句柄，
 * 并且在反序列化的时候搜取句柄
 * 管理序列化的一个办法是魔术方法：
 * __sleep() 对象被序列化时被调用，可用来执行对象被序列化前任何清理工作
 * __wakeup()　对象、在反序列化的时候被调用，可用来获取储存对象是使用的文件 或者数据库句柄
 *
 * 虽然序列化时php一个非常高效的方法，但是仍要小心保存内容
 * 一个看似简单的对象可能包含一个其他对象的引用，该引用又指向一个从数据库获得的巨大对象集合
 *
 * Registry 对象使数据全局有效，任何客户端代码都可以在自己的类中加上对注册表的依赖
 * 但是过分依赖注册表来存放大量数据，将会导致严重的问题
 * 因此：Registry 对象最好不要存大量的数据，里面的数据集合也要经过良好的定义
 */

?>