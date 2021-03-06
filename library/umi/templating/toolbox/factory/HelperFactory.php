<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\toolbox\factory;

use umi\templating\exception\UnexpectedValueException;
use umi\templating\extension\helper\IHelperFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика помощников вида.
 */
class HelperFactory implements IHelperFactory, IFactory
{
    use TFactory;

    /**
     * {@inheritdoc}
     */
    public function createHelper($class)
    {
        $helper = $this->createInstance($class);

        if (!is_callable($helper)) {
            throw new UnexpectedValueException($this->translate(
                'Helper object for class "{class}" is not callable.'
            ));
        }

        return $helper;
    }
}