<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter\toolbox;

use umi\filter\IFilterFactory;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов для фильтрации.
 */
interface IFilterTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'filter';

    /**
     * Возвращает фабрику фильтров.
     * @return IFilterFactory
     */
    public function getFilterFactory();
}