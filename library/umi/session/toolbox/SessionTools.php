<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\toolbox;

use umi\session\entity\factory\ISessionEntityFactory;
use umi\session\entity\factory\ISessionEntityFactoryAware;
use umi\session\ISessionAware;
use umi\session\ISessionManagerAware;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для работы с сессиями.
 */
class SessionTools implements ISessionTools
{

    use TToolbox;

    /**
     * @var string $managerClass класс менеджера сессии
     */
    public $managerClass = 'umi\session\SessionManager';
    /**
     * @var string $serviceClass класс сервиса сессии
     */
    public $serviceClass = 'umi\session\Session';
    /**
     * @var string $namespaceFactoryClass фабрика создания пространств имен
     */
    public $entityFactoryClass = 'umi\session\toolbox\factory\SessionEntityFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'entity',
            $this->entityFactoryClass,
            ['umi\session\entity\factory\ISessionEntityFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\session\ISessionManager':
                return $this->getManager();
            case 'umi\session\ISession':
                return $this->getSession();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{alias}" does not support service "{interface}".',
            ['alias' => self::ALIAS, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->createSingleInstance(
            $this->managerClass,
            [],
            ['umi\session\ISessionManager']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->createSingleInstance(
            $this->serviceClass,
            [],
            ['umi\session\ISession']
        );
    }

    /**
     * Возвращает фабрику сущностей сессии.
     * @return ISessionEntityFactory
     */
    protected function getEntityFactory()
    {
        return $this->getFactory('entity');
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof ISessionAware) {
            $object->setSessionService($this->getSession());
        }

        if ($object instanceof ISessionManagerAware) {
            $object->setSessionManager($this->getManager());
        }

        if ($object instanceof ISessionEntityFactoryAware) {
            $object->setNamespaceFactory($this->getEntityFactory());
        }
    }

}