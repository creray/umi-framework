<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit\engine;

use umi\cache\engine\Memcached;
use utest\TestCase;

/**
 * Тест memcached
 * @package
 */
class MemcachedTest extends TestCase
{
    /**
     * @var Memcached
     */
    private $storage;

    protected function setUpFixtures()
    {
        if (!class_exists('Memcached')) {
            $this->markTestSkipped('Расширение Memcached не установлено на этом сервере.');

            return;
        }
        $servers = ['localhost' => ['port' => 11211]];
        $this->storage = new Memcached($servers);
    }

    protected function tearDownFixtures()
    {
        if ($this->storage) {
            $this->storage->clear();
        }
    }

    public function testStorage()
    {
        $this->assertFalse($this->storage->get('testKey'), 'Значение уже есть в кеше');
        $this->assertTrue($this->storage->set('testKey', 'testValue', 10), 'Не удалось сохранить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('testKey'), 'В кеше хранится неверное значение');

        $this->assertTrue(
            $this->storage->set('testKey', 'newTestValue', 10),
            'Не удалось переопределить значение в кеше'
        );
        $this->assertFalse(
            $this->storage->add('testKey', 'newNewTestValue', 10),
            'Удалось переопределить значение в кеше'
        );
        $this->assertEquals('newTestValue', $this->storage->get('testKey'), 'В кеш добавилось неверное значение');

        $this->assertTrue($this->storage->add('newTestKey', 'testValue', 10), 'Не удалось добавить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('newTestKey'), 'В кеш добавилось неверное значение');

        $this->assertTrue($this->storage->remove('testKey'), 'Не удалось удалить значение из кеша');
        $this->assertFalse($this->storage->get('testKey'), 'Значение в кеше существует после удаления');

        $this->storage->set('testKey1', 'testValue1', 10);
        $this->storage->set('testKey2', 'testValue2');

        $expectedResult = array(
            'testKey1' => 'testValue1',
            'testKey2' => 'testValue2',
            'testKey3' => false
        );
        $this->assertEquals(
            $expectedResult,
            $this->storage->getList(array('testKey1', 'testKey2', 'testKey3')),
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
