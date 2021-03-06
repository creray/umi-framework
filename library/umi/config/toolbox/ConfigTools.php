<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\config\toolbox;

use umi\config\cache\IConfigCacheEngineAware;
use umi\config\entity\factory\IConfigEntityFactoryAware;
use umi\config\exception\OutOfBoundsException;
use umi\config\io\IConfigAliasResolverAware;
use umi\config\io\IConfigIOAware;
use umi\i18n\TLocalesAware;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с конфигурацией.
 */
class ConfigTools implements IConfigTools
{

    use TToolbox;

    /**
     * Конфигурация на основе Php файлов.
     */
    const TYPE_PHP = 'php';
    /**
     * @var string $entityFactoryClass фабрика сущностей конфигурации
     */
    public $entityFactoryClass = 'umi\config\toolbox\factory\ConfigEntityFactory';
    /**
     * @var string $ioServiceClass сервис I/O конфигурации
     */
    public $ioServiceClass = 'umi\config\io\ConfigIO';
    /**
     * @var string $ioServiceClass сервис I/O конфигурации
     */
    public $cacheServiceClass = 'umi\config\cache\ConfigCacheEngine';
    /**
     * @var bool $hasCache использовать ли кэш?
     */
    public $hasCache = false;
    /**
     * @var array $cache настройки для механизма кэширования
     */
    public $cache = [];
    /**
     * @var array $aliases список зарегистрированных символических имен
     */
    public $aliases = [];
    /**
     * @var string $type установленный тип конфигурации для набора инструментов
     */
    public $type = self::TYPE_PHP;
    /**
     * @var array $readers список reader'ов конфигурации
     */
    public $readers = [
        self::TYPE_PHP => 'umi\config\io\reader\PhpFileReader',
    ];
    /**
     * @var array $writers список writer'ов конфигурации
     */
    public $writers = [
        self::TYPE_PHP => 'umi\config\io\writer\PhpFileWriter',
    ];

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'entity',
            $this->entityFactoryClass,
            ['umi\config\entity\factory\IConfigEntityFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\config\io\IConfigIO':
                return $this->getConfigIO();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{alias}" does not support service "{interface}".',
            ['alias' => self::ALIAS, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IConfigIOAware) {
            $object->setConfigIO($this->getConfigIO());
        } elseif ($object instanceof IConfigAliasResolverAware) {
            $object->setConfigIO($this->getConfigIO());
        }

        if ($object instanceof IConfigEntityFactoryAware) {
            $object->setConfigEntityFactory($this->getConfigEntityFactory());
        }

        if ($this->hasCache && $object instanceof IConfigCacheEngineAware) {
            $object->setConfigCacheEngine($this->getConfigCacheEngine());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigIO()
    {
        return $this->createSingleInstance(
            $this->ioServiceClass,
            [$this->getReader(), $this->getWriter(), $this->aliases],
            ['umi\config\io\IConfigIO']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getReader()
    {
        if (!isset($this->readers[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Reader "{type}" is not available.',
                ['type' => $this->type]
            ));
        }

        return $this->createSingleInstance(
            $this->readers[$this->type],
            [],
            ['umi\config\io\reader\IReader']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWriter()
    {
        if (!isset($this->writers[$this->type])) {
            throw new OutOfBoundsException($this->translate(
                'Writer "{type}" is not available.',
                ['type' => $this->type]
            ));
        }

        return $this->createSingleInstance(
            $this->writers[$this->type],
            [],
            ['umi\config\io\writer\IWriter']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigEntityFactory()
    {
        return $this->getFactory('entity');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigCacheEngine()
    {
        return $this->createSingleInstance(
            $this->cacheServiceClass,
            [],
            ['umi\config\cache\IConfigCacheEngine'],
            $this->cache
        );
    }
}