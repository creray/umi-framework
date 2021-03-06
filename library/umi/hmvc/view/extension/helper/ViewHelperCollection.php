<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\view\extension\helper;

use umi\hmvc\context\IContextAware;
use umi\hmvc\context\TContextInjectorAware;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\templating\extension\helper\collection\HelperCollection;
use umi\templating\extension\helper\IHelperFactory;

/**
 * Class ViewHelperCollection
 */
class ViewHelperCollection extends HelperCollection implements IModelAware, IContextAware
{
    use TContextInjectorAware;

    /**
     * @var IModelFactory $contextModelFactory
     */
    private $contextModelFactory;

    /**
     * {@inheritdoc}
     */
    public function setModelFactory(IModelFactory $modelFactory)
    {
        $this->contextModelFactory = $modelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplatingHelperFactory(IHelperFactory $factory)
    {
        // todo: FIX IT!!!!
        if ($factory instanceof IModelAware && $this->contextModelFactory) {
            $factory->setModelFactory($this->contextModelFactory);
        }

        parent::setTemplatingHelperFactory($factory);
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable($name)
    {
        $callable = parent::getCallable($name);

        // Should inject here, because context can be changed
        // during component lifetime.
        if (is_object($callable)) {
            $this->injectContext($callable);
        }

        return $callable;
    }
}