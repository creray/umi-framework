<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\exception;

/**
 * Исключение, происходящее при попытке использовать драйвер, который не доступен PDO.
 */
class NotAvailableDriverException extends RuntimeException
{
}