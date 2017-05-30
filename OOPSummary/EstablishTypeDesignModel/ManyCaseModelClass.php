<?php
/**
 * 多里模式
 *
 *目的：使类仅有一个命名的对象的集合可供使用，像单例模式但是有多个实例。
 *
 * 例子：2 个数据库连接，比如，一个连接MySQL，另一个连接SQLite
 * 多个日志记录器（一个记录调试信息，另一个记录错误信息）
 */

namespace DesignPatterns\Creational\Multiton{
    final class Multiton
    {
        const INSTANCE_1 = '1';
        const INSTANCE_2 = '2';

        /**
         * @var Multiton[]
         */
        private static $instances = [];

        /**
         * this is private to prevent from creating arbitrary instances
         */
        private function __construct()
        {
        }

        public static function getInstance(string $instanceName): Multiton
        {
            if (!isset(self::$instances[$instanceName])) {
                self::$instances[$instanceName] = new self();
            }

            return self::$instances[$instanceName];
        }

        /**
         * prevent instance from being cloned
         */
        private function __clone()
        {
        }

        /**
         * prevent instance from being unserialized
         */
        private function __wakeup()
        {
        }
    }
}

?>