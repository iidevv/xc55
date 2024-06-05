<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Messages\Admin;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Repo\Order;

class CustomerOrders extends \XLite\View\ItemsList\AItemsList
{

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyDir()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/messages/customer_orders';
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getPageBodyFile();
    }

    /**
     * @inheritdoc
     */
    protected function getEmptyListTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getEmptyListFile();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/messages/style.less';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $author = $this->getConversation()->getAuthor();
        $return = [];

        if ($author) {
            $cnd = new CommonCell();
            $cnd->{Order::P_PROFILE_ID} = $author->getProfileId();
            $return = Database::getRepo('XLite\Model\Order')->search($cnd, $countOnly);
        } else {
            $return = $countOnly ? 0 : [];
        }

        return $return;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }
}