<?php
namespace Angular\Registry\Scope;
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
?>