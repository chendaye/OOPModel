<?php
namespace Angular\Registry\Scope;
/**
 * Class Registry
 * @package Angular\Registry\Scope
 * Registry 基类定义两个  protecteed 方法： get()  set();客户端代码不能直接使用它们
 * 基类也可以定义其他 public 方法 isEmpty() isPopulated() clear()
 * 可以在各个子类中保留具体的 get() set() 方法，而在特定的领域类中定制  public 的 getA() setA() 方法
 * 且定制对象会成为单例对象
 * 通过这种方法，可以实现 重用核心的储存获取操作，即在多个项目中重复使用同一个注册表
 */
abstract class Registry{
    abstract protected function get($key);
    abstract protected function set($key, $val);
}
?>