<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit;

use umi\config\entity\IConfigSource;
use umi\config\entity\ISeparateConfigSource;
use umi\config\entity\LazyConfigSource;
use umi\config\io\IConfigIO;
use umi\toolkit\toolbox\TToolbox;
use utest\TestCase;

/**
 * Тесты конфигурации.
 */
class LazyConfigSourceTest extends TestCase
{
    /**
     * @var ISeparateConfigSource $source
     */
    private $cfgSource;

    public function setUpFixtures()
    {
        $this->cfgSource = new LazyConfigSource('~/alias.php');
        $this->cfgSource->setConfigIO(new MockConfigIO());
    }

    public function testAlias()
    {
        $this->assertSame('~/alias.php', $this->cfgSource->getAlias());
    }

    public function testLoading()
    {
        $e = null;
        try {
            $this->cfgSource->getSeparateConfig();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('\RuntimeException', $e, 'Ожидается, что конфигурация будет загружена.');

        $e = null;
        try {
            $this->cfgSource->get('key');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('\RuntimeException', $e, 'Ожидается, что конфигурация будет загружена.');

        $e = null;
        try {
            $this->cfgSource->set('key', 'value');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('\RuntimeException', $e, 'Ожидается, что конфигурация будет загружена.');

        $e = null;
        try {
            $this->cfgSource->del('key');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('\RuntimeException', $e, 'Ожидается, что конфигурация будет загружена.');

        $e = null;
        try {
            $this->cfgSource->reset('key');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('\RuntimeException', $e, 'Ожидается, что конфигурация будет загружена.');
    }

    public function testSerialize()
    {
        $this->assertEquals(
            'O:34:"umi\config\entity\LazyConfigSource":1:{s:8:" * alias";s:11:"~/alias.php";}',
            serialize($this->cfgSource)
        );
    }
}

/**
 * Mock набор инструментов для тестирования lazy
 */
class MockConfigIO implements IConfigIO
{

    use TToolbox;

    /**
     * {@inheritdoc}
     */
    public function registerAlias($alias, $masterDirectory, $localDirectory = null)
    { /* nope */
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesByAlias($alias)
    { /* nope */
    }

    /**
     * {@inheritdoc}
     */
    public function read($alias)
    {
        throw new \RuntimeException('Trying to read config ' . $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function write(IConfigSource $config)
    { /* nope */
    }
}