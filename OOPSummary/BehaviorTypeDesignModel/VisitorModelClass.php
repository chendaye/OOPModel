<?php
/**
 * 访问者模式
 *
 * 访问者模式允许您将对象的操作外包给其他对象。做这件事的主要原因是保持关注的分离。但是类必须定义一个允许访问者访问的合同（角色：示例中的接受方法）。
 * 合同是一个抽象类，但你也可以有一个干净的界面。在这种情况下，每个访问者都必须自己选择调用访问者的方法。
 */

namespace DesignPatterns\Behavioral\Visitor{
    /**
     * Note: the visitor must not choose itself which method to
     * invoke, it is the Visitee that make this decision
     */
    interface RoleVisitorInterface
    {
        public function visitUser(User $role);

        public function visitGroup(Group $role);
    }

    class RoleVisitor implements RoleVisitorInterface
    {
        /**
         * @var Role[]
         */
        private $visited = [];

        public function visitGroup(Group $role)
        {
            $this->visited[] = $role;
        }

        public function visitUser(User $role)
        {
            $this->visited[] = $role;
        }

        /**
         * @return Role[]
         */
        public function getVisited(): array
        {
            return $this->visited;
        }
    }

    interface Role
    {
        public function accept(RoleVisitorInterface $visitor);
    }

    class User implements Role
    {
        /**
         * @var string
         */
        private $name;

        public function __construct(string $name)
        {
            $this->name = $name;
        }

        public function getName(): string
        {
            return sprintf('User %s', $this->name);
        }

        public function accept(RoleVisitorInterface $visitor)
        {
            $visitor->visitUser($this);
        }
    }

    class Group implements Role
    {
        /**
         * @var string
         */
        private $name;

        public function __construct(string $name)
        {
            $this->name = $name;
        }

        public function getName(): string
        {
            return sprintf('Group: %s', $this->name);
        }

        public function accept(RoleVisitorInterface $visitor)
        {
            $visitor->visitGroup($this);
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Tests\Visitor\Tests{
    use DesignPatterns\Behavioral\Visitor;
    use PHPUnit\Framework\TestCase;

    class VisitorTest extends TestCase
    {
        /**
         * @var Visitor\RoleVisitor
         */
        private $visitor;

        protected function setUp()
        {
            $this->visitor = new Visitor\RoleVisitor();
        }

        public function provideRoles()
        {
            return [
                [new Visitor\User('Dominik')],
                [new Visitor\Group('Administrators')],
            ];
        }

        /**
         * @dataProvider provideRoles
         *
         * @param Visitor\Role $role
         */
        public function testVisitSomeRole(Visitor\Role $role)
        {
            $role->accept($this->visitor);
            $this->assertSame($role, $this->visitor->getVisited()[0]);
        }
    }
}




?>