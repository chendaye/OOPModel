<?php
/**
 * 观察者模式
 *
 * 要实现对对象的发布/订阅行为，每当“主体”对象更改其状态时，将通知附加的“观察员”。它用于缩短耦合对象的数量，并使用松耦合代替。
 */

namespace DesignPatterns\Behavioral\Observer{
    /**
     * User implements the observed object (called Subject), it maintains a list of observers and sends notifications to
     * them in case changes are made on the User object
     */
    class User implements \SplSubject
    {
        /**
         * @var string
         */
        private $email;

        /**
         * @var \SplObjectStorage
         */
        private $observers;

        public function __construct()
        {
            $this->observers = new \SplObjectStorage();
        }

        public function attach(\SplObserver $observer)
        {
            $this->observers->attach($observer);
        }

        public function detach(\SplObserver $observer)
        {
            $this->observers->detach($observer);
        }

        public function changeEmail(string $email)
        {
            $this->email = $email;
            $this->notify();
        }

        public function notify()
        {
            /** @var \SplObserver $observer */
            foreach ($this->observers as $observer) {
                $observer->update($this);
            }
        }
    }

    class UserObserver implements \SplObserver
    {
        /**
         * @var User[]
         */
        private $changedUsers = [];

        /**
         * It is called by the Subject, usually by SplSubject::notify()
         *
         * @param \SplSubject $subject
         */
        public function update(\SplSubject $subject)
        {
            $this->changedUsers[] = clone $subject;
        }

        /**
         * @return User[]
         */
        public function getChangedUsers(): array
        {
            return $this->changedUsers;
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Observer\Tests{
    use DesignPatterns\Behavioral\Observer\User;
    use DesignPatterns\Behavioral\Observer\UserObserver;
    use PHPUnit\Framework\TestCase;

    class ObserverTest extends TestCase
    {
        public function testChangeInUserLeadsToUserObserverBeingNotified()
        {
            $observer = new UserObserver();

            $user = new User();
            $user->attach($observer);

            $user->changeEmail('foo@bar.com');
            $this->assertCount(1, $observer->getChangedUsers());
        }
    }
}




?>