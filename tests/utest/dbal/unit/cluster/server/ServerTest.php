<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\cluster\server;

use umi\dbal\cluster\server\IServer;
use umi\dbal\cluster\server\ShardServer;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\TestCase;

/**
 * Тест фабрики построителей запросов
 *
 */
class ServerTest extends TestCase
{

    /**
     * @var IServer $server ;
     */
    protected $server;

    protected function setUpFixtures()
    {
        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $driver = $this->getDbServer()
            ->getDbDriver();

        $this->server = new ShardServer('test_server', $driver, $queryBuilderFactory);
        $this->server->modifyInternal("CREATE TABLE IF NOT EXISTS `test` (`a` text)");
    }

    protected function tearDownFixtures()
    {
        $this->server->modifyInternal("DROP TABLE IF EXISTS `test`");
    }

    public function testQueryBuilderFactory()
    {

        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IServer',
            $this->server,
            'Ожидается, что любой сервер реалтзует интерфейс IServer'
        );
        $this->assertEquals('test_server', $this->server->getId(), 'Неверный id сервера');
        $this->assertInstanceOf(
            'umi\dbal\driver\IDbDriver',
            $this->server->getDbDriver(),
            'Ожидается, что IServer::getDbDriver() вернет IDbDriver'
        );

        $this->assertEquals(1, $this->server->modifyInternal("INSERT INTO `test` (`a`) VALUES('test')"));
        $this->assertInstanceOf('PDOStatement', $this->server->selectInternal("SELECT * FROM `test`"));

        $this->assertInstanceOf('umi\dbal\builder\ISelectBuilder', $this->server->select());
        $this->assertInstanceOf('umi\dbal\builder\IUpdateBuilder', $this->server->update('test'));
        $this->assertInstanceOf('umi\dbal\builder\IInsertBuilder', $this->server->insert('test'));
        $this->assertInstanceOf('umi\dbal\builder\IDeleteBuilder', $this->server->delete('test'));
    }
}
