<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\toolbox;

use umi\config\entity\Config;
use umi\dbal\toolbox\DbalTools;
use utest\TestCase;

/**
 * Тестирование инструментария для работы с базами данных
 */
class DbToolsTest extends TestCase
{

    /**
     * @var DbalTools
     */
    protected $dbal;

    protected function setUpFixtures()
    {
        $this->dbal = new DbalTools();
        $this->resolveOptionalDependencies($this->dbal);
    }

    public function testGetService()
    {

        $dbCluster = $this->dbal->getCluster();
        $this->assertInstanceOf('umi\dbal\cluster\IDbCluster', $dbCluster);
        $this->assertTrue($dbCluster === $this->dbal->getService('umi\dbal\cluster\IDbCluster', null));
    }

    public function  testDbToolsServerConfig1()
    {
        $this->dbal->servers = ['wrongServerConfig' => 'wrongServerConfig'];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Server configuration should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function  testDbToolsServerConfig2()
    {
        $this->dbal->servers = ['wrongServerConfig' => []];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Cannot find server id in configuration.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function  testDbToolsServerConfig3()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'   => 'wrongServerId',
                'type' => 'master'
            ]
        ];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals('Cannot find driver configuration.', $e->getMessage(), 'Неверный текст исключения');
    }

    public function  testDbToolsServerConfig4()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => 'wrongDriverConfig'
            ]
        ];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Db driver configuration should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function  testDbToolsServerConfig5()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => [
                    'type' => ''
                ]
            ]
        ];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Cannot find driver type in configuration.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function  testDbToolsServerConfig6()
    {
        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => [
                    'type'    => 'mysql',
                    'options' => 'WrongOptionsValue'
                ]
            ]
        ];
        $e = null;
        try {
            $this->dbal->getCluster();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при неверно заданной конфигурации инструментария баз данных'
        );
        $this->assertEquals(
            'Db driver options should be an array or Traversable.',
            $e->getMessage(),
            'Неверный текст исключения'
        );
    }

    public function  testDbToolsServerConfig7()
    {

        $this->dbal->servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => [
                    'type'    => 'mysql',
                    'options' => []
                ]
            ]
        ];

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $this->dbal->getCluster(),
            'Ожидается, что IDbTools::getCluster() вернет IDbCluster'
        );
    }

    public function  testDbToolsServerConfig8()
    {

        $servers = [
            'wrongServerConfig' => [
                'id'     => 'wrongServerId',
                'type'   => 'master',
                'driver' => [
                    'type'    => 'mysql',
                    'options' => []
                ]
            ]
        ];

        $this->dbal->servers = new Config($servers);

        $this->assertInstanceOf(
            'umi\dbal\cluster\IDbCluster',
            $this->dbal->getCluster(),
            'Ожидается, что IDbTools::getCluster() вернет IDbCluster'
        );
    }
}