<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\engine;

use umi\templating\engine\ITemplateEngine;
use umi\templating\engine\twig\TwigTemplateEngine;
use umi\templating\extension\adapter\ExtensionAdapter;
use umi\templating\toolbox\factory\ExtensionFactory;
use umi\templating\toolbox\ITemplatingTools;
use utest\TestCase;

/**
 * Тесты Twig шаблонизатора.
 */
class TwigTemplateEngineTest extends TestCase
{
    /**
     * @var TwigTemplateEngine $view
     */
    protected $view;

    public function setUpFixtures()
    {
        $this->view = new TwigTemplateEngine([
            ITemplateEngine::OPTION_DIRECTORY => __DIR__ . '/data/twig',
            ITemplateEngine::OPTION_EXTENSION => 'twig',
        ]);
        $this->resolveOptionalDependencies($this->view);
    }

    public function testRender()
    {
        $response = $this->view->render(
            'example',
            [
                'var' => 'testVal'
            ]
        );

        $this->assertEquals(
            'Hello world! testVal',
            $response,
            'Ожидается, что контент будет установлен.'
        );
    }

    public function testHelpers()
    {
        $adapter = new ExtensionAdapter();
        $this->resolveOptionalDependencies($adapter);

        $extensionFactory = new ExtensionFactory();
        $this->resolveOptionalDependencies($extensionFactory);

        $collection = $extensionFactory->createHelperCollection();
        $this->resolveOptionalDependencies($collection);
        $collection->addHelper('mock', 'utest\templating\mock\helper\MockViewHelper');

        $adapter->addHelperCollection('test', $collection);

        $this->view->setExtensionAdapter($adapter);

        $response = $this->view->render('helper', []);

        $this->assertEquals('Helper: mock', $response, 'Ожидается, что mock будет вызван.');
    }

    /**
     * Возвращает набор инструментов для работы с отображением.
     * @return ITemplatingTools
     */
    protected final function getViewTools()
    {
        return $this->toolkit()
            ->getToolbox(ITemplatingTools::ALIAS);
    }
}