<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit\engine;

use umi\cache\engine\Db;
use umi\dbal\driver\IColumnScheme;
use utest\TestCase;

/**
 * Тест хранения кеша в бд
 * @package
 */
class DbTest extends TestCase
{

    /**
     * @var Db $storage
     */
    private $storage;

    private $tableName = 'test_cache_storage';

    protected function setUpFixtures()
    {

        $server = $this->getDbServer();
        $options = [
            'table'    => [
                'tableName'        => $this->tableName,
                'keyColumnName'    => 'key',
                'valueColumnName'  => 'cacheValue',
                'expireColumnName' => 'cacheExpiration'
            ],
            'serverId' => $server->getId()
        ];

        $driver = $server->getDbDriver();
        $table = $driver->addTable($this->tableName);
        $table->addColumn('key', IColumnScheme::TYPE_VARCHAR, [IColumnScheme::OPTION_COMMENT => 'Cache unique key']);
        $table->addColumn('cacheValue', IColumnScheme::TYPE_BLOB, [IColumnScheme::OPTION_COMMENT => 'Cache value']);
        $table->addColumn(
            'cacheExpiration',
            IColumnScheme::TYPE_INT,
            [IColumnScheme::OPTION_COMMENT => 'Cache expire timestamp', IColumnScheme::OPTION_UNSIGNED => true]
        );

        $table->setPrimaryKey('key');
        $table->addIndex('expire')
            ->addColumn('cacheExpiration');
        $driver->applyMigrations();

        $this->storage = new Db($options);
        $this->resolveOptionalDependencies($this->storage);

    }

    protected function tearDownFixtures()
    {
        $this->getDbServer()
            ->getDbDriver()
            ->dropTable($this->tableName);
    }

    public function testStorage()
    {

        $this->assertFalse($this->storage->get('testKey'), 'Значение уже есть в кеше');

        $this->assertTrue($this->storage->set('testKey', 'testValue', 1), 'Не удалось сохранить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('testKey'), 'В кеше хранится неверное значение');

        $this->assertTrue(
            $this->storage->set('testKey', 'newTestValue', 1),
            'Не удалось переопределить значение в кеше'
        );
        $this->assertFalse(
            $this->storage->add('testKey', 'newNewTestValue', 1),
            'Удалось переопределить значение в кеше'
        );
        $this->assertEquals('newTestValue', $this->storage->get('testKey'), 'В кеш добавилось неверное значение');

        $this->assertTrue($this->storage->add('newTestKey', 'testValue', 1), 'Не удалось добавить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('newTestKey'), 'В кеш добавилось неверное значение');

        $update = $this->getDbServer()
            ->update('test_cache_storage');
        $update
            ->set('cacheExpiration', ':expire')
            ->where()
            ->expr('key', '=', ':id');
        $update
            ->bindInt(':expire', time() - 1000)
            ->bindString(':id', 'testKey');
        $update->execute();

        $this->assertFalse($this->storage->get('testKey'), 'Время кеша должно было истечь');

        $this->storage->set('testKey', 'newTestValue', 1);
        $this->assertTrue($this->storage->remove('testKey'), 'Не удалось удалить значение из кеша');
        $this->assertFalse($this->storage->get('testKey'), 'Значение в кеше существует после удаления');

        $this->storage->set('testKey1', 'testValue1', 1);
        $this->storage->set('testKey2', 'testValue2');
        $this->storage->set('testKey3', 'testValue3', 1);

        $update = $this->getDbServer()
            ->update('test_cache_storage');
        $update
            ->set('cacheExpiration', ':expire')
            ->where()
            ->expr('key', '=', ':id');
        $update
            ->bindInt(':expire', time() - 1000)
            ->bindString(':id', 'testKey3');
        $update->execute();

        $expectedResult = array(
            'testKey1' => 'testValue1',
            'testKey2' => 'testValue2',
            'testKey3' => false,
            'testKey4' => false
        );
        $this->assertEquals(
            $expectedResult,
            $this->storage->getList(array('testKey1', 'testKey2', 'testKey3', 'testKey4')),
            'Неверное значение для массива ключей'
        );

        $this->assertTrue($this->storage->clear(), 'Не удалось очистить кеш');
        $this->assertEquals(
            array('testKey1' => false, 'testKey2' => false),
            $this->storage->getList(array('testKey1', 'testKey2')),
            'Неверное значение для массива ключей после очистки кеша'
        );
    }

}
