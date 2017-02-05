<?php
namespace Angular\Registry\Scope;
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
?>