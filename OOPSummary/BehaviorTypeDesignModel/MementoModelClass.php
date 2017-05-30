<?php
/**
 * 纪念模式
 *
 * 它提供了将对象还原为先前状态（通过回滚撤消）或访问对象状态的能力，而无需透露其实现（即对象不需要有返回当前状态的函数）。
 * 纪念模式是用三个对象：鼻祖，管理员和纪念品。
 * 一个包含任何对象或资源的具体状态快照的对象：字符串、数字、数组、类的实例等等。
 * 这种情况下的唯一性并不意味着在不同的快照中禁止类似状态的存在。这意味着状态可以被提取为独立克隆。
 * 存储在纪念品中的任何对象都应该是原始对象的完整副本，而不是对原始对象的引用。纪念品对象是“不透明对象”（没有人可以或应该改变的对象）。
 * 它是一个包含外部对象的实际状态的对象。发起人能够创建这个状态的唯一副本，并返回它包裹在一个纪念。发起人不知道变化的历史。您可以从外部设置一个具体的状态给发起人，这将被视为实际。
 * 发起人必须确保给定的状态对应于允许的对象类型。发起人可能（但不应该）有任何方法，但他们不能对保存的对象状态进行更改。
 * 看守者控制各州历史。他可以对一个对象进行更改；决定保存原始对象中的外部对象的状态；从当前状态的发起者快照中查询；或将发端状态与历史中的一些快照等价。
 */

namespace DesignPatterns\Behavioral\Memento{
    class Memento
    {
        /**
         * @var State
         */
        private $state;

        /**
         * @param State $stateToSave
         */
        public function __construct(State $stateToSave)
        {
            $this->state = $stateToSave;
        }

        /**
         * @return State
         */
        public function getState()
        {
            return $this->state;
        }
    }

    class State
    {
        const STATE_CREATED = 'created';
        const STATE_OPENED = 'opened';
        const STATE_ASSIGNED = 'assigned';
        const STATE_CLOSED = 'closed';

        /**
         * @var string
         */
        private $state;

        /**
         * @var string[]
         */
        private static $validStates = [
            self::STATE_CREATED,
            self::STATE_OPENED,
            self::STATE_ASSIGNED,
            self::STATE_CLOSED,
        ];

        /**
         * @param string $state
         */
        public function __construct(string $state)
        {
            self::ensureIsValidState($state);

            $this->state = $state;
        }

        private static function ensureIsValidState(string $state)
        {
            if (!in_array($state, self::$validStates)) {
                throw new \InvalidArgumentException('Invalid state given');
            }
        }

        public function __toString(): string
        {
            return $this->state;
        }
    }

    /**
     * Ticket is the "Originator" in this implementation
     */
    class Ticket
    {
        /**
         * @var State
         */
        private $currentState;

        public function __construct()
        {
            $this->currentState = new State(State::STATE_CREATED);
        }

        public function open()
        {
            $this->currentState = new State(State::STATE_OPENED);
        }

        public function assign()
        {
            $this->currentState = new State(State::STATE_ASSIGNED);
        }

        public function close()
        {
            $this->currentState = new State(State::STATE_CLOSED);
        }

        public function saveToMemento(): Memento
        {
            return new Memento(clone $this->currentState);
        }

        public function restoreFromMemento(Memento $memento)
        {
            $this->currentState = $memento->getState();
        }

        public function getState(): State
        {
            return $this->currentState;
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Memento\Tests{
    use DesignPatterns\Behavioral\Memento\State;
    use DesignPatterns\Behavioral\Memento\Ticket;
    use PHPUnit\Framework\TestCase;

    class MementoTest extends TestCase
    {
        public function testOpenTicketAssignAndSetBackToOpen()
        {
            $ticket = new Ticket();

            // open the ticket
            $ticket->open();
            $openedState = $ticket->getState();
            $this->assertEquals(State::STATE_OPENED, (string) $ticket->getState());

            $memento = $ticket->saveToMemento();

            // assign the ticket
            $ticket->assign();
            $this->assertEquals(State::STATE_ASSIGNED, (string) $ticket->getState());

            // now restore to the opened state, but verify that the state object has been cloned for the memento
            $ticket->restoreFromMemento($memento);

            $this->assertEquals(State::STATE_OPENED, (string) $ticket->getState());
            $this->assertNotSame($openedState, $ticket->getState());
        }
    }
}




?>