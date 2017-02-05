<?php
/**
 * 事物脚本模式，描述了不同系统的实现方式，该模式简单而高效，随着系统的增长优势会下降
 * 事物脚本自己处理请求；而不是委托给其他的对象处理
 * 事物脚本模式是一种快速工具，也是一种很难分类的模式；它将其他几个层的元素集合在一起
 *
 * 问题：
 * 发送到系统的每个请求都要进行处理，很多系统都有一些访问和过滤请求的数据层，这些层需要调用一些类来完成任务
 * 有时候需要将这些类拆开才行，或许会用到外观接口（这种方式要很小心）
 * 有些项目（特别是小型项目、工期紧的项目中）在设计上花费过多时间不值得，
 * 在这种情况下可以把业务逻辑写成一系列的过程式操作，每个操作处理一个特定的请求
 *
 * 事物脚本模式的好处在于可以很快的得到想要的结果。
 * 每个脚本都能很好的处理输入数据并操作数据库来得到想要的结果
 * 除了在同一个类中组织相关方法外，还要确保事物脚本类在它们自己的层中（尽可能独立于视图层、命令层、控制层）
 *
 * 业务逻辑的类通常与表现层明确的分离开来，单通常会嵌入到数据层中
 * 这是因为，获取和保存数据是系统工作的关键
 * 事物脚本类通常了解数据库的所有信息，单可以通过入口类来处理实际的查询细节
 *
 * 用什么模式（如：命令模式、观察者模式）来组织业务逻辑，是个要重点考虑的问题
 * 以前用的就是事物脚本模式，只是没有意识到而已
 * 简单的说就是:一个类处理一个请求
 */
/**
 * 例子：
 * 系统有3个关系数据表：venue space event
 * 一个场所可能有多个空间 space 每个空间都是多个事件发生的地方
 * 我们需要添加所有场景和事件的功能
 * 每个功能都可以称为一个事物；可以为每个功能都设计一个类，根据命令模式来组织
 *
 * 现在把所有方法放到一个类中，作为继承体系的一部分
 * 因为对于任何规模的项目，都需要添加任意规模的子类到继承体系中
 * 由于子类大多要和数据库打交道，那么把一些核心的数据库访问放到基类里就是很好的选择
 *
 * 此模式还有一个名称：层超类型；
 * 到多数时候在用的时候并没想太多；
 * 如果一个层中的多个类由很多共同点，那么将它们放到一个类中，
 * 通常把常用的功能房到基类中，这很有意义
 */
namespace Angular\Process;
use Angular\Registry\Scope\ApplicationRegistry;

/**
 * 此类用ApplicationRegistry来得到一个数据源DSN 然后传递给PDO
 * prepareStatment 方法调用PDO类的prepare方法，返回一个sql句柄
 * 该句柄最终被传递给execute方法
 * prepareStatment方法中把资源类型存储在静态数组 $stmt 中；使用sql语句本身作为数组索引
 *
 * prepareStatment方法建议直接被子类调用，一般通过doStatement 方法来调用
 * 对事物脚本来说，数据操作都是隐藏在幕后的，事物脚本只需要构造出sql语句和关注业务逻辑即可
 * Class Base
 * @package Angular\Process
 */
abstract class Base{
    static $db;
    static $stmt = array();
    public function __construct()
    {
        $dsn = ApplicationRegistry::getDSN();   //连接信息
        if(is_null($dsn)){
            throw new \Angular\AppException("no dsn!");
        }
        self::$db = new \PDO($dsn); //PDO 对象
        self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);   //连接属性
    }

    /**
     * 获取 PDOStmt 对象
     * @param $stmt_s
     * @return mixed|\PDOStatement
     */
    public function prepareStatment($stmt_s){
        if(isset(self::$stmt[$stmt_s])){
            return self::$stmt[$stmt_s];
        }
        $stmt_handle = self::$db->prepare($stmt_s);
        self::$stmt[$stmt_s] = $stmt_handle;
        return $stmt_handle;
    }
    protected function doStatement($stmt_s, $values_s){
        $sth = $this->prepareStatment($stmt_s);
        $sth = closeCursor();
        $db_result = $sth->execute($values_s);
        return $sth;
    }
}
?>