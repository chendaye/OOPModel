<?php
/**
 * 状态模式
 *
 * 根据对象的状态为同一例程封装不同的行为。这可以为对象在运行时改变其行为而不诉诸大的单片条件语句提供一种更清洁的方法。
 */

namespace DesignPatterns\Behavioral\State{

    class OrderRepository
    {
        /**
         * @var array
         */
        private static $orders = [
            1 => ['status' => 'created'],
            2 => ['status' => 'shipping'],
            3 => ['status' => 'completed'],
        ];

        public static function findById(int $id): Order
        {
            if (!isset(self::$orders[$id])) {
                throw new \InvalidArgumentException(sprintf('Order with id %d does not exist', $id));
            }

            $order = self::$orders[$id];

            switch ($order['status']) {
                case 'created':
                    return new CreateOrder($order);
                case 'shipping':
                    return new ShippingOrder($order);
                default:
                    throw new \InvalidArgumentException('Invalid order status given');
                    break;
            }
        }
    }

    interface Order
    {
        /**
         * @return mixed
         */
        public function shipOrder();

        /**
         * @return mixed
         */
        public function completeOrder();

        public function getStatus(): string;
    }

    class ShippingOrder implements Order
    {
        /**
         * @var array
         */
        private $details;

        /**
         * @param array $details
         */
        public function __construct(array $details)
        {
            $this->details = $details;
        }

        public function shipOrder()
        {
            throw new \Exception('Can not ship the order which status is shipping!');
        }

        public function completeOrder()
        {
            $this->details['status'] = 'completed';
            $this->details['updatedTime'] = time();
        }

        public function getStatus(): string
        {
            return $this->details['status'];
        }
    }

    class CreateOrder implements Order
    {
        /**
         * @var array
         */
        private $details;

        /**
         * @param array $details
         */
        public function __construct(array $details)
        {
            $this->details = $details;
        }

        public function shipOrder()
        {
            $this->details['status'] = 'shipping';
            $this->details['updatedTime'] = time();
        }

        public function completeOrder()
        {
            throw new \Exception('Can not complete the order which status is created');
        }

        public function getStatus(): string
        {
            return $this->details['status'];
        }
    }
}

?>