<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

use XCart\Extender\Mapping\ListChild;
use XLite\View\ItemsList\Model\Order\Admin\Search;

/**
 * @ListChild (list="page.tabs.after", zone="admin", weight="200")
 */
class NextPrevious extends \XLite\View\AView
{
    protected $nextOrder;
    protected $previousOrder;

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'order';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'order/page/next-previous.css';

        return $list;
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getNextOrder()
    {
        if ($this->nextOrder === null) {
            $this->nextOrder = \XLite\Core\Database::getRepo('XLite\Model\Order')->search(
                $this->getSearchCnd(),
                \XLite\Model\Repo\Order::SEARCH_MODE_NEXT_ORDER
            );
        }

        return $this->nextOrder;
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getPreviousOrder()
    {
        if ($this->previousOrder === null) {
            $this->previousOrder = \XLite\Core\Database::getRepo('XLite\Model\Order')->search(
                $this->getSearchCnd(),
                \XLite\Model\Repo\Order::SEARCH_MODE_PREV_ORDER
            );
        }

        return $this->previousOrder;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCnd()
    {
        $conditions = Search::getInstance()->getConditionForNexPrevious();
        $conditions->setData([
            'order' => $this->getOrder()
        ]);

        return $conditions;
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder()
            && ($this->getNextOrder() || $this->getPreviousOrder());
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/next-previous.twig';
    }
}
