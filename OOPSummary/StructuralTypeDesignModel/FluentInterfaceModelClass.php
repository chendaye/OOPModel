<?php
/**
 * 连贯接口模式
 *
 * 用来编写易于阅读的代码，就像自然语言一样（如英语）
 *
 * 例子
 * Doctrine2 的 QueryBuilder，就像下面例子中类似
 * PHPUnit 使用连贯接口来创建 mock 对象
 * Yii 框架：CDbCommand 与 CActiveRecord 也使用此模式
 */

namespace DesignPatterns\Structural\FluentInterface{
    class Sql
    {
        /**
         * @var array
         */
        private $fields = [];

        /**
         * @var array
         */
        private $from = [];

        /**
         * @var array
         */
        private $where = [];

        public function select(array $fields): Sql
        {
            $this->fields = $fields;

            return $this;
        }

        public function from(string $table, string $alias): Sql
        {
            $this->from[] = $table.' AS '.$alias;

            return $this;
        }

        public function where(string $condition): Sql
        {
            $this->where[] = $condition;

            return $this;
        }

        public function __toString(): string
        {
            return sprintf(
                'SELECT %s FROM %s WHERE %s',
                join(', ', $this->fields),
                join(', ', $this->from),
                join(' AND ', $this->where)
            );
        }
    }
}

/**
 * 测试
 */
namespace DesignPatterns\Structural\FluentInterface\Tests{
    use DesignPatterns\Structural\FluentInterface\Sql;
    use PHPUnit\Framework\TestCase;

    class FluentInterfaceTest extends TestCase
    {
        public function testBuildSQL()
        {
            $query = (new Sql())
                ->select(['foo', 'bar'])
                ->from('foobar', 'f')
                ->where('f.bar = ?');

            $this->assertEquals('SELECT foo, bar FROM foobar AS f WHERE f.bar = ?', (string) $query);
        }
    }
}




?>