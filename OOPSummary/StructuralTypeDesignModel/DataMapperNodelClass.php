<?php
/**
 * 数据映射器模式
 *
 *数据映射器是一个数据访问层，用于将数据在持久性数据存储（通常是一个关系数据库）和内存中的数据表示（领域层）之间进行相互转换。
 * 其目的是为了将数据的内存表示、持久存储、数据访问进行分离。该层由一个或者多个映射器组成（或者数据访问对象），并且进行数据的转换。
 * 映射器的实现在范围上有所不同。通用映射器将处理许多不同领域的实体类型，而专用映射器将处理一个或几个。
 * 此模式的主要特点是，与Active Record不同，其数据模式遵循单一职责原则(Single Responsibility Principle)。
 *
 * 例子
 * DB Object Relational Mapper (ORM) : Doctrine2 使用 DAO “EntityRepository” 作为DAO
 */

namespace DesignPatterns\Structural\DataMapper{
    class User
    {
        /**
         * @var string
         */
        private $username;

        /**
         * @var string
         */
        private $email;

        public static function fromState(array $state): User
        {
            // validate state before accessing keys!

            return new self(
                $state['username'],
                $state['email']
            );
        }

        public function __construct(string $username, string $email)
        {
            // validate parameters before setting them!

            $this->username = $username;
            $this->email = $email;
        }

        /**
         * @return string
         */
        public function getUsername()
        {
            return $this->username;
        }

        /**
         * @return string
         */
        public function getEmail()
        {
            return $this->email;
        }
    }

    class UserMapper
    {
        /**
         * @var StorageAdapter
         */
        private $adapter;

        /**
         * @param StorageAdapter $storage
         */
        public function __construct(StorageAdapter $storage)
        {
            $this->adapter = $storage;
        }

        /**
         * finds a user from storage based on ID and returns a User object located
         * in memory. Normally this kind of logic will be implemented using the Repository pattern.
         * However the important part is in mapRowToUser() below, that will create a business object from the
         * data fetched from storage
         *
         * @param int $id
         *
         * @return User
         */
        public function findById(int $id): User
        {
            $result = $this->adapter->find($id);

            if ($result === null) {
                throw new \InvalidArgumentException("User #$id not found");
            }

            return $this->mapRowToUser($result);
        }

        private function mapRowToUser(array $row): User
        {
            return User::fromState($row);
        }
    }

    class StorageAdapter
    {
        /**
         * @var array
         */
        private $data = [];

        public function __construct(array $data)
        {
            $this->data = $data;
        }

        /**
         * @param int $id
         *
         * @return array|null
         */
        public function find(int $id)
        {
            if (isset($this->data[$id])) {
                return $this->data[$id];
            }

            return null;
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\DataMapper\Tests{
    use DesignPatterns\Structural\DataMapper\StorageAdapter;
    use DesignPatterns\Structural\DataMapper\User;
    use DesignPatterns\Structural\DataMapper\UserMapper;
    use PHPUnit\Framework\TestCase;

    class DataMapperTest extends TestCase
    {
        public function testCanMapUserFromStorage()
        {
            $storage = new StorageAdapter([1 => ['username' => 'domnikl', 'email' => 'liebler.dominik@gmail.com']]);
            $mapper = new UserMapper($storage);

            $user = $mapper->findById(1);

            $this->assertInstanceOf(User::class, $user);
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testWillNotMapInvalidData()
        {
            $storage = new StorageAdapter([]);
            $mapper = new UserMapper($storage);

            $mapper->findById(1);
        }
    }
}




?>