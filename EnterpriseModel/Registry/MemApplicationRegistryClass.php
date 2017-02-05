<?php
namespace Angular\Registry\Scope;
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