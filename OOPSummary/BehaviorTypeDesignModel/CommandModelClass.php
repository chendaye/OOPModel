<?php
/**
 * 命令模式
 *
 * 封装调用和去耦。
 * 我们有一个调用器和接收器。此模式使用“命令”将方法调用委托给接收者，并呈现与“执行”相同的方法。因此，调用者只知道叫“执行”来处理客户端的命令。接收器是由调用者解耦。
 * 这种模式的第二个方面是undo()，撤消方法execute()。命令也可以聚合成更复杂的命令与最小复制粘贴和依赖于继承。
 *
 * 文本编辑器：所有事件都是可撤消、堆叠和保存的命令。
 * Symfony2：SF2的命令可以从CLI命令模式只是记住
 * 大的CLI工具使用分区分配各种任务，把它们装在“模块”，这些都可以用命令模式来实现的（例如流浪
 */

namespace DesignPatterns\Behavioral\Command{
    interface CommandInterface
    {
        /**
         * this is the most important method in the Command pattern,
         * The Receiver goes in the constructor.
         */
        public function execute();
    }

    /**
     * This concrete command calls "print" on the Receiver, but an external
     * invoker just knows that it can call "execute"
     */
    class HelloCommand implements CommandInterface
    {
        /**
         * @var Receiver
         */
        private $output;

        /**
         * Each concrete command is built with different receivers.
         * There can be one, many or completely no receivers, but there can be other commands in the parameters
         *
         * @param Receiver $console
         */
        public function __construct(Receiver $console)
        {
            $this->output = $console;
        }

        /**
         * execute and output "Hello World".
         */
        public function execute()
        {
            // sometimes, there is no receiver and this is the command which does all the work
            $this->output->write('Hello World');
        }
    }

    /**
     * Receiver is specific service with its own contract and can be only concrete.
     */
    class Receiver
    {
        /**
         * @var bool
         */
        private $enableDate = false;

        /**
         * @var string[]
         */
        private $output = [];

        /**
         * @param string $str
         */
        public function write(string $str)
        {
            if ($this->enableDate) {
                $str .= ' ['.date('Y-m-d').']';
            }

            $this->output[] = $str;
        }

        public function getOutput(): string
        {
            return join("\n", $this->output);
        }

        /**
         * Enable receiver to display message date
         */
        public function enableDate()
        {
            $this->enableDate = true;
        }

        /**
         * Disable receiver to display message date
         */
        public function disableDate()
        {
            $this->enableDate = false;
        }
    }

    /**
     * Invoker is using the command given to it.
     * Example : an Application in SF2.
     */
    class Invoker
    {
        /**
         * @var CommandInterface
         */
        private $command;

        /**
         * in the invoker we find this kind of method for subscribing the command
         * There can be also a stack, a list, a fixed set ...
         *
         * @param CommandInterface $cmd
         */
        public function setCommand(CommandInterface $cmd)
        {
            $this->command = $cmd;
        }

        /**
         * executes the command; the invoker is the same whatever is the command
         */
        public function run()
        {
            $this->command->execute();
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Behavioral\Command\Tests{

    use DesignPatterns\Behavioral\Command\HelloCommand;
    use DesignPatterns\Behavioral\Command\Invoker;
    use DesignPatterns\Behavioral\Command\Receiver;
    use PHPUnit\Framework\TestCase;

    class CommandTest extends TestCase
    {
        public function testInvocation()
        {
            $invoker = new Invoker();
            $receiver = new Receiver();

            $invoker->setCommand(new HelloCommand($receiver));
            $invoker->run();
            $this->assertEquals('Hello World', $receiver->getOutput());
        }
    }
}



?>