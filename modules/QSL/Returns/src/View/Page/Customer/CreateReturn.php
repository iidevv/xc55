<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Page\Customer;

/**
 * Create return widget
 */
class CreateReturn extends \XLite\View\AView
{
    use \XLite\View\Base\ViewListsFallbackTrait;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/Returns/order/create_return/style.css';

        return $list;
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'create_return';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Returns/order/create_return/page.twig';
    }

    /**
     * Returns order items
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getOrderItems()
    {
        return $this->getOrder()->getItems();
    }
}
