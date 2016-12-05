<?php
    /**
     * 析构方法，对象被销毁时自动调用
     */
    class Hai{
        private $name;
        public function __construct(){
            $this->name = 'chen';
        }
        public function save(){
            print $this->name;
        }
        //TODO:对象被销毁时自动保存数据
        public function __destruct(){
            // TODO: Implement __destruct() method.
            print 'save data';
        }
    }
    $ret = new Hai();
    unset($ret);
?>