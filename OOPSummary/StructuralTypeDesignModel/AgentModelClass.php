<?php
/**
 * 代理模式
 *
 * 为昂贵或者无法复制的资源提供接口。
 *
 * 例子
 * Doctrine2 使用代理来实现框架特性（如延迟初始化），同时用户还是使用自己的实体类并且不会使用或者接触到代理
 */
namespace DesignPatterns\Structural\Proxy{
    /**
     * @property string username
     */
    class Record
    {
        /**
         * @var string[]
         */
        private $data;

        /**
         * @param string[] $data
         */
        public function __construct(array $data = [])
        {
            $this->data = $data;
        }

        /**
         * @param string $name
         * @param string  $value
         */
        public function __set(string $name, string $value)
        {
            $this->data[$name] = $value;
        }

        public function __get(string $name): string
        {
            if (!isset($this->data[$name])) {
                throw new \OutOfRangeException('Invalid name given');
            }

            return $this->data[$name];
        }
    }

    class RecordProxy extends Record
    {
        /**
         * @var bool
         */
        private $isDirty = false;

        /**
         * @var bool
         */
        private $isInitialized = false;

        /**
         * @param array $data
         */
        public function __construct(array $data)
        {
            parent::__construct($data);

            // when the record has data, mark it as initialized
            // since Record will hold our business logic, we don't want to
            // implement this behaviour there, but instead in a new proxy class
            // that extends the Record class
            if (count($data) > 0) {
                $this->isInitialized = true;
                $this->isDirty = true;
            }
        }

        /**
         * @param string $name
         * @param string  $value
         */
        public function __set(string $name, string $value)
        {
            $this->isDirty = true;

            parent::__set($name, $value);
        }

        public function isDirty(): bool
        {
            return $this->isDirty;
        }
    }
}


?>