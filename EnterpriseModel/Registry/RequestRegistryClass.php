<?php
namespace Angular\Registry\Scope;
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
?>