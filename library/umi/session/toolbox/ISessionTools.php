<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session\toolbox;

use umi\session\ISession;
use umi\session\ISessionManager;
use umi\toolkit\toolbox\IToolbox;

/**
 * Набор инструментов для работы с сессиями.
 */
interface ISessionTools extends IToolbox
{
    /**
     * Короткий alias
     */
    const ALIAS = 'session';

    /**
     * Возвращает менеджер сессии.
     * @return ISessionManager
     */
    public function getManager();

    /**
     * Возвращает сервис сессии.
     * @return ISession
     */
    public function getSession();
}