<?php

    /**
     * Exception的构造方法可接受两个参数
     * 消息字符串
     * 错误代码
     */
    class Error{
        private $key;
        public function __construct($data){
            if(!is_array($data)){
                //TODO:错误发生，抛出异常
                throw new Exception('不是数组');
            }else{
                throw new TionException('','扩展');
            }


            $this->key = $data;
        }
    }
    try{
       $ret = new Error(array(1,2));
    }catch (TionException $e){
        //TODO:异常处理扩展
        $e->extent();
    }catch(Exception $error){
        //TODO:捕获异常
        //TODO:获取消息字符串
        print $error->getMessage();
        //TODO:获取传递给构造方法的错误代码
        print $error->getCode();
        //TODO:获取产生异常的文件
        print $error->getFile();
        //TODO:获取生成异常的行号
        print $error->getLine();
        //TODO:获取一个嵌套异常的对象
        print $error->getPrevious();
        //TODO:获取一个多维数组，数组追踪导致异常的方法调用，包含，方法，类，文件，参数的数据
        print_r($error->getTrace());
        //TODO:获取getTrace()的字符串版本
        print $error->getTraceAsString();
        //TODO:获取在字符串中使用Exception对象是自动调用，返回一个描述异常细节的字符串
        $error->__toString();
    }

    /**
     * 异常的子类化
     * 扩展异常类的功能
     * 进行特定的错误处理
     */
    class TionException extends Exception {
        protected $error;
        public function __construct($message,$error){
            $this->error = $error;
            parent::__construct($message);
        }

        public function extent(){
            echo $this->error;
        }
    }

?>