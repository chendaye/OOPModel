<?php
/**
 * 解释其模式:可以创建一个一个迷你语言的解释器
 */
namespace Explain{
    /**
     * 问题：创建一个迷你语言
     * 有如下语句：$a eq "4"  or $a eq "four"
     * 上诉语句由一下几部分组成：
     * 变量：$a
     * 字符串："four"
     * 布尔值 与（or）: or
     * 相等测试: eq
     */
    //TODO:抽象基类
    abstract class Expression{
        //静态计数器
        private static $count = 0;
        //键值
        private $key;
        //TODO：接口功能，拥有一个 数据存储对象，用来处理各个数据类型，初始化的值, 存储的时候用到   private $key;
        abstract public function interpret(InterpreterContext $context);
        //TODO:获取键值,每次调用该方法，计数器加1
        public function getKey(){
            if(!isset($this->key)){
                //没实例化一个Expression 对象， 计数器加1
                self::$count++;
                //获取最新的计数器值
                $this->key = self::$count;
            }
            return $this->key;
        }
        //TODO:让外界访问计数器的值
        public function count(){
            return  $this->key;
        }
    }
    //TODO:数据存储类
    class InterpreterContext{
        private $store = array();
        //接受Expression 对象 和要存储的值 把值存在 属性数组里
        public function save(Expression $exp, $value){
            $this->store[$exp->getKey()] = $value;
        }
        //接受Expression 对象 并读取相应的值
        public function get(Expression $exp){
            return $this->store[$exp->getKey()];
        }
        //返回存储的数据
        public function getData(){
            return $this->store;
        }
        //当前计数器的值
        public function getCount(Expression $exp){
            return $exp->count();
        }
    }
    //TODO:字符表达式类
    class LiteralExpression extends Expression {
        private $value;
        //初始化要保存的值
        public function __construct($value)
        {
            $this->value = $value;
        }
        //TODO：接受InterpreterContext 数据存储对象，保存构造方法接受的值
        public function interpret(InterpreterContext $context)
        {
            // TODO: Implement interpret() method.
            $context->save($this, $this->value);
        }
        //初始化的结果
        public function value(){
            return $this->value;
        }
    }

    //TODO:使用
    //实例数据存储对象
    $cont = new InterpreterContext();
    //实例字符类  保存字符
    $litt = new LiteralExpression('four');
    $little = new LiteralExpression('4');
    //调用数据存储类，保存数据,interpret() 方法会调用 InterpreterContext 对象的 save() 方法存储数据；
    // 同时save() 中又会用到Expression 对象的getKey 方法
    $litt->interpret($cont);
    $little->interpret($cont);
    //读取字符,读取最新插入的数据
    print $cont->get($litt);
    print_r($cont->getData());
    print_r($cont->getCount($litt));
    /**
     * Expression 对象都实现了抽象基类Expression 的 interpret()方法
     * interpret()方法将 InterpreterContext 对象用做共享的数据存储
     * 每个Expression 对象都可以在InterpreterContext 对象中 存储数据
     * InterpreterContext 对象可以被传递给其他  Expression 对象
     * Expression 基类实现了一个返回唯一句柄的 getKey() 方法，所以能轻松的从 InterpreterContext 对象中获取数据
     */

    //TODO:依据上面的方式，可以在定义其他数据类型
    class VariableExpression extends Expression {
        //TODO:变量名
        private $name;
        //TODO:变量值
        private $val;
        //TODO:初始化变量名、变量值
        public function __construct($name, $val = null)
        {
            $this->name = $name;
            $this->val = $val;
        }
        //TODO:接口功能,用来处理初始化的数据
        public function interpret(InterpreterContext $context)
        {
            // TODO: Implement interpret() method.
            if(!is_null($this->val)){
                //调用数据存储对象，保存变量值
                $context->save($this, $this->val);
                $this->val = null;
            }
        }
        //TODO:设置变量的值
        public function setVal($value){
            $this->val = $value;
        }
        //TODO:重写获取键值的方法。返回变量键值
        public function getKey()
        {
           return $this->name;
        }
    }
    //TODO:使用变量类
    //实例数据存储对象
    $context = new InterpreterContext();
    //实例变量类
    $variable = new VariableExpression('a', 'chendaye');
    //存储变量,会调用 类  VariableExpression 中被重写的 getKey()
    $variable->interpret($context);
    //读取变量,会调用 类  VariableExpression 中被重写的 getKey()
    echo '<br>';
    echo $context->get($variable);
    print_r($context->getData());
    /**
     * 现在 变量值只支持字符串类型 ，如果要扩展语言  就要考虑让它与 其他Expression 对象一起工作
     *这就要包括检查和操作的结果
     *语言中的操作符 Operator 表达式总是和另外两个 Expression 对象一起工作
     * 所以 Operator 很自然的要继承同一个父类
     */
    abstract class OperatorExpression extends Expression {
        protected $l_op;
        protected $r_op;
        //TODO:初始化 两个 Expression 对象
        public function __construct(Expression $l_op, Expression $r_op)
        {
            $this->l_op = $l_op;
            $this->r_op = $r_op;
        }
        //TODO:实现 interpret 方法， 用到了组合模式
        public function interpret(InterpreterContext $context)
        {
            //TODO：接受InterpreterContext 数据存储对象，保存构造方法接受的值
            $this->l_op->interpret($context);
            $this->r_op->interpret($context);
            //TODO:取得相应的值
            $result_l = $context->get($this->l_op);
            $result_r = $context->get($this->r_op);
            //TODO:存储对象  两个值
            $this->doInterpret($context, $result_l, $result_r);
        }

        /**
         * OperatorExpression 是一个抽象类 实现了抽象方法 interpret,但同时有定义了抽象方法 doInterpret
         * @param InterpreterContext $context
         * @param $result_l
         * @param $result_r
         * @return mixed
         */
        protected abstract function doInterpret(InterpreterContext $context, $result_l, $result_r);
    }
    /**
     * OperatorExpression 对象的构造函数 用两个 Expression 对象 $l_op $r_op 为参数
     * 这两个对象被保存在对象属性中
     * OperatorExpression 的 interpret 方法首先调用  构造函数初始化 的两个对象的 interpret 函数
     * 这里便是 组合模式  OperatorExpression（二级抽象类） $l_op  $r_op 都是 Expression 的子类
     * OperatorExpression  是组合对象
     * $l_op  $r_op  是叶子对象
     *
     * 一旦运行了OperatorExpression interpret()会调用  每个属性（对象） 的 get()方法 获得操作对象的返回值
     * 最后调用 doInterpret  由子类来决定 如何处理操作结果
     *
     * doInterpret()方法是 模板方法模式的一个实例， 父类中定义并调用了一个抽象方法
     * 并留子类来决定怎么实现
     * 该模式使具体类的开发更加流畅
     * 因为 共享功能有父类处理，可能会有多级抽象方法，把一级一级的共享功能由抽象父类实现
     * 子类就只需要完成干净简单的职责
     */
    class EqualsExpression extends OperatorExpression {
        protected function doInterpret(InterpreterContext $context, $result_l, $result_r)
        {
            // TODO: Implement doInterpret() method.
            //先检查$result_l == $result_r，如果相等  $result_l == $result_r 的值就是 true 把true 存起来
            $context->save($this, $result_l == $result_r);
        }
    }

    /**
     *  与相等 是一样的逻辑
     * 布尔 或 操作
     * Class BoolOrExpression
     * @package Explain
     */
    class BoolOrExpression extends OperatorExpression {
        protected function doInterpret(InterpreterContext $context, $result_l, $result_r)
        {
            // TODO: Implement doInterpret() method.
            $context->save($this, $result_l || $result_r);
        }
    }

    /**
     * 与相等 是一样的逻辑
     * 布尔 或 操作
     * Class BoolAndExpression
     * @package Explain
     */
    class BoolAndExpression extends OperatorExpression {
        protected function doInterpret(InterpreterContext $context, $result_l, $result_r)
        {
            // TODO: Implement doInterpret() method.
            $context->save($this, $result_l && $result_r);
        }
    }
    //使用
    /**
     * InterpreterContext 类是  Expression 类用来存取数据的
     * interpret() 方法是 Expression 类的核心，它来完成 具体Expression子类的 核心职责
     * Expression 应用组合模式  将不同 子类组合  也就是把不同子类 的功能 interpret() 组合，共同实现某个逻辑功能
     * 比如：
     * 字符类 LiteralExpression 的interpret() 就是储存字符 到 InterpreterContext 对象中
     * 变量类 VariableExpression 的interpret() 就是 把 变量名 变量值  存到 InterpreterContext 对象中，并提供更改值得方法
     * 操作类 OperatorExpression  组合 两个  Expression 对象，组合 interpret() 进行逻辑判断， 把判断结果 用interpret() 存到 InterpreterContext 对象中
     *
     */
    $context = new InterpreterContext();
    $input = new VariableExpression('four');
    $l = new EqualsExpression($input, new LiteralExpression('four'));   //判断相等 interpret 的结果 true
    $r = new EqualsExpression($input, new LiteralExpression('4'));  //判断相等 interpret 的结果 flase
    //布尔操作
    $bool = new BoolOrExpression($l, $r);
    foreach (array('four', '4', '5') as $val){
        //  设置变量值，初始化是 four
        $input->setVal($val);
        //布尔操作的 interpret 方法， 再组合调用属性（对象） $l  $r 的interpret()方法
        //把 $l  $r 的interpret()方法 的操作结果 (true or  flase)，存到$context 中 再把值取出来 交给doInterpret() 方法处理
        //假设 four  $input->setVal($val) 置变量 $input 值为 four
        //$l interpret()+doInterpret() 处理结果是 true
        //$r interpret()+doInterpret() 处理结果是 false
        //$bool interpret() 将 true false 保存 取出  传递至  doInterpret() 进行 或运算  =》 true || false = true
        //TODO:在叶子对象 如 LiteralExpression VariableExpression 功能简单的由  interpret()实现
        //TODO:在组合对象中  OperatorExpression  多了一个抽象级 interpret() 获取功能组合结果  具体执行 放到下级 doInterpret()中完成
        $bool->interpret($context);
        if($context->get($bool)){
            echo 'yes';
        }else{
            echo 'no';
        }
        /**
         * 总结：
         * 创建解释器的核心类后，解释器容易扩展
         * 但当语言变得复杂类的数量会很快增加，只适合小语言
         */
    }
}
?>