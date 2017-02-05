<?php
namespace Angular\Controller;

use Angular\Request\Request;

class ApplicationController{
    private static $base_cmd;
    private static $default_cmd;
    private $controllerMap;
    private $invoked = array();
    public function __construct(ControllerMap $map)
    {
        $this->controllerMap = $map;
        if(!self::$base_cmd){
            self::$base_cmd = new \ReflectionClass("\\Angular\\Command\\Command");
            self::$default_cmd = new \DefaultCommand();
        }
    }
    public function getView(Request $req){
        $forward = $this->getResource($req, 'Forward');
        if($forward){
            $req->setProperty('cmd', $forward);
        }
        return $forward;
    }

    /**
     * 执行查找操作，用于转向或选择视图  对于由 getFroward() getView() 负责
     * 注意它会优先查找最具体的命令字符串和状态标识的组合，然后搜索通用的组合
     * @param Request $req
     * @param $res
     * @return mixed
     */
    private function getResource(Request $req, $res){
        //得到前一个命令的执行状态
        $cmd_str = $req->getProperty('cmd');
        $previous = $req->getLastCommand();
        $status = $previous->getStatus();
        if(!$status){
            $status = 0;
        }
        $acquire = "get$res";
        //得到前一个命令的资源及其状态
        $resource = $this->controllerMap->$acquire($cmd_str, $status);
        //查找命令并且状态为0的资源
        if(!$resource){
            $resource = $this->controllerMap->$acquire($cmd_str, 0);
        }
        //或者 default 命令 和命令状态
        if(!$resource){
            $resource = $this->controllerMap->$acquire('default', $status);
        }
        //其他情况区 default 失败，状态为 0
        if(!$resource){
            $resource = $this->controllerMap->$acquire('default', 0);
        }
        return $resource;
    }

    /**
     * 此方法负责返回转向中需要使用的所有命令
     * 过程：当请求第一次被接受到，就会生出一个cmd属性，本次请求之前没有任何Command命令执行过
     * Request对象储存着与这个过程相关的信息
     * 如果cmd属性未被赋值，则类方法使用默认值，并返回默认的Command类
     * $cmd字符串变量被传递给 resolveCommand() 该方法得到一个Command 对象
     * 在请求中第二次调用 getCommand() 方法时，Request 对象将持有一个对之前执行过的Command 对象的引用
     * 此时，getCommand() 将根据该命令和状态标识判断是否需要转向，转向通过 getForawrd() 实现
     * 如果 getForward() 找到一个匹配的对象，会返回一个字符串，该字符串可被解析成一个命令对象供控制器使用
     *
     * getCommand() 中需要注意的一点是通过判断来避免循环转向
     * 数组索引为Command 类的名称，在添加元素时如果元素已经存在，说明该命令之前已经被获取过
     * 入过发生这种情况就抛出一个异常
     *
     * 前端控制器通过一个应用控制器来获得 Command 对象和 视图
     * @param Request $req
     * @return \DefaultCommand|null|object
     */
    public function getCommand(Request $req){
        $previous = $req->getLastCommand();
        if(!$previous){
            //本次请求调用的第一个命令
            $cmd = $req->getProperty('cmd');
            if(!$cmd){
                //如果无法得到命令，使用默认命令
                $req->getProperty('cmd', 'default');
                return self::$default_cmd;
            }else{
                //之前已经执行过一个命令
                $cmd = $this->getForward($req);
                if(!$cmd){
                    return null;
                }
            }
            $cmd_obj = $this->resolveCommand($cmd);
            if(!$cmd_obj){
                throw new Angular\AppExpection("不能解析{$cmd}");
            }
            $cmd_class = get_class($cmd_obj);
            if(isset($this->invoked[$cmd_obj])){
                throw new Angular\AppExpection("circular forwarding");
            }
            $this->invoked[$cmd_class] = 1;
            return $cmd_obj;
        }
    }
    public function resolveCommand($cmd){
        $classroot = $this->controllerMap->getClassroot($cmd);
        $filepath = "Angular/Command/{$classroot}.php";
        $classname = "\\Angular\\Command\\{$classroot}";
        if(file_exists($filepath)){
            require_once($filepath);
            if(class_exists($classname)){
                $cmd_calss = new \ReflectionClass($classname);
                if($cmd_calss->isSubclassOf(self::$base_cmd)){
                    return $cmd_calss->newInstance();
                }
            }
        }
        return null;
    }
}
?>