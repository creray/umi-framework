<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\validation\toolbox;

use umi\toolkit\toolbox\TToolbox;
use umi\validation\IValidationAware;

/**
 * Набор инструментов валидации.
 */
class ValidationTools implements IValidationTools
{

    use TToolbox;

    /**
     * @var string $validatorFactoryClass класс фабрики валидаторов
     */
    public $validatorFactoryClass = 'umi\validation\toolbox\factory\ValidatorFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'validator',
            $this->validatorFactoryClass,
            ['umi\validation\IValidatorFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IValidationAware) {
            $object->setValidatorFactory($this->getValidatorFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatorFactory()
    {
        return $this->getFactory('validator');
    }
}