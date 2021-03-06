<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\unit\type;

use umi\filter\IFilter;
use umi\filter\type\StripNewLines;
use utest\TestCase;

/**
 * Класс тестирование фильтра Boolean
 */
class StripNewLinesFilterTests extends TestCase
{

    /**
     * @var IFilter $filter
     */
    private $filter = null;

    public function setUpFixtures()
    {
        $this->filter = new StripNewLines();
    }

    public function testFilterBaseUsage()
    {
        $this->assertEquals(
            "test string",
            $this->filter->filter("test\nstring"),
            "Ожидается, что перенос строки будет заменен пробелом"
        );

        $this->assertEquals(
            "test string",
            $this->filter->filter("test\r\nstring"),
            "Ожидается, что перенос строки будет заменен 1им пробелом"
        );
    }
}