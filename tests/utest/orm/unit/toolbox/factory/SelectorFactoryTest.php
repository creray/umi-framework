<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox\factory;

use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\orm\ORMTestCase;

/**
 * Тест фабрики селекторов
 *
 */
class SelectorFactoryTest extends ORMTestCase
{

    /**
     * @var SelectorFactory $selectorFactory
     */
    protected $selectorFactory;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [];
    }

    protected function setUpFixtures()
    {
        $objectSetFactory = new ObjectSetFactory();
        $this->resolveOptionalDependencies($objectSetFactory);

        $this->selectorFactory = new SelectorFactory($objectSetFactory);
        $this->resolveOptionalDependencies($this->selectorFactory);
    }

    public function testCreate()
    {
        $selector = $this->selectorFactory->createSelector(
            $this->collectionManager->getCollection(self::USERS_USER)
        );
        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $selector,
            'Ожидается, что ISelectorFactory::create() вернет ISelector'
        );

        $emptySelector = $this->selectorFactory->createEmptySelector(
            $this->collectionManager->getCollection(self::USERS_USER)
        );
        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $emptySelector,
            'Ожидается, что ISelectorFactory::createEmptySelector() вернет ISelector'
        );

        $this->assertCount(
            0,
            $emptySelector->getResult()
                ->fetchAll(),
            'Ожидается, что пустой селектор всегда содержит 0 элементов'
        );
    }
}

