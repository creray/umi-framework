<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\i18n\toolbox;

use umi\i18n\ILocalesAware;
use umi\i18n\ILocalesService;
use umi\i18n\ILocalizable;
use umi\i18n\translator\ITranslator;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для поддержки локализации.
 */
class I18nTools implements I18nToolsInterface
{

    use TToolbox;

    /**
     * @var string $translatorClass класс для локализации сообщений
     */
    public $translatorClass = 'umi\i18n\translator\Translator';
    /**
     * @var string $localesServiceClass класс сервиса для работы с локалями
     */
    public $localesServiceClass = 'umi\i18n\LocalesService';
    /**
     * @var array $translator опции транслятора
     */
    public $translator = [];
    /**
     * @var string $defaultLocale локаль по умолчанию
     */
    public $defaultLocale = 'en-US';
    /**
     * @var string $currentLocale текущая локаль
     */
    public $currentLocale = 'en-US';

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof ILocalizable) {
            $object->setTranslator($this->getTranslator());
        }
        if ($object instanceof ILocalesAware) {
            $object->setLocalesService($this->getLocalesService());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\i18n\translator\ITranslator':
            {
                return $this->getTranslator();
            }
            case 'umi\i18n\ILocalesService':
            {
                return $this->getLocalesService();
            }
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{alias}" does not support service "{interface}".',
            ['alias' => self::ALIAS, 'interface' => $serviceInterfaceName]
        ));
    }

    /**
     * Возвращает транслятор для локализации сообщений
     * @return ITranslator
     */
    protected function getTranslator()
    {
        return $this->createSingleInstance(
            $this->translatorClass,
            [],
            ['umi\i18n\translator\ITranslator'],
            $this->translator
        );
    }

    /**
     * Возвращает сервис для работы с локалями.
     * @return ILocalesService
     */
    protected function getLocalesService()
    {
        /**
         * @var ILocalesService $localesService
         */
        if (null !== ($localesService = $this->getSingleInstance($this->localesServiceClass))) {
            return $localesService;
        }

        $localesService = $this->createSingleInstance(
            $this->localesServiceClass,
            [],
            ['umi\i18n\ILocalesService']
        );
        $localesService->setDefaultLocale($this->defaultLocale);
        $localesService->setCurrentLocale($this->currentLocale);

        return $localesService;
    }
}
