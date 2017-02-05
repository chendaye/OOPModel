<?php
    /**
     * 外观模式：
     * 在集成第三方代码时；无论第三方代码是 面向过程 还是 面向对象；都是巨大复杂的工作
     * 对客户端代码来说，自己写的代码，也是个挑战
     * 外观模式可以为复杂的系统创建一个简单清晰的接口
     */
    namespace SurfaceModelProblem{
        /**
         * 系统中总会逐渐形成大量仅在自身内部又用的代码
         * 系统也应该像类那样提供清晰的公共接口，并对外隐藏内部结构
         * 但是系统中那部分 公开  那部分 隐藏  不容易确定
         *
         * 当使用子系统代码时，也许会过于深入的调用逻辑代码
         * 如果子系统代码在不断变化，而你的代码又在许多不同的地方与子系统代码交互
         * 那么随着子系统发展，代码会越来越难以调用
         *
         * 在创建系统时，将不同的部分 分层 是很好的办法
         * 可以分为 应用逻辑层  数据库交互层  表现层  使这些分层互相独立
         * 修改一些地方尽量不影响其他地方
         */
        /**
         * 一段乱糟糟的代码
         */
        function getProductFile($file){
            return file($file);
        }
        function getProductObject($id, $productname){
            //数据库查询
            return new product($id, $productname);
        }
        function getName($line){
            return $line;
        }
        function getIdForm($line){
            return $line;
        }
        class Product{
            public $id;
            public $name;
            public function __construct($id, $name)
            {
                $this->id = $id;
                $this->name;
            }
        }
        //TODO:下面为上面的代码创建一个接口（假设一个功能要用到上面的所有代码）
        class Face{
            private $products = array();
            private $file;
            public function __construct($file)
            {
                $this->file = $file;
                $this->compile();
            }
            private function compile(){
                $line = getProductFile($this->file);
                foreach ($line as $lines){
                    $id = getName($lines);
                    $name = getIdForm($id);
                    $this->products[$id] = getProductObject($id, $name);
                }
            }
            //TODO:把结果提供给外部
            public function getProducts(){
                return $this->products;
            }
            public function getId($id){
                return $this->products[$id];
            }
        }
        /**
         * 实际上；外观模式
         * 就是  把原先的代码的，组织调用过程， 放在一个类里面实现，并且对外部隐藏对原代码组织、调用的过程
         * 同时，通过方法，把调用的结果提供给客户端代码
         * 如此一来，客户端代码，面对的就是一个清晰的接口，
         * 与之前面对一团乱糟糟的代码，有天壤之别
         *
         * 总结：
         * 外观模式  简单  实用  却 强大，它为一个分层 或者 一个子系统  创建一个单一的入口
         * 这回带来许多好处：
         * 首先，有助于分离项目中的不同部分
         * 其次，对客户端来说，访问变得简洁，明了
         * 再者，因为只在一个地方调用子系统，减少了出错的可能，并预估了bug所在位置
         * 最或，还能能避免客户端不恰当的调用子系统，减少错误发生
         */
        //TODO:总的来说：外观模式，非常适合现在的工作场景；非常适用，基本垃圾的公司代码就靠你了
    }
?>