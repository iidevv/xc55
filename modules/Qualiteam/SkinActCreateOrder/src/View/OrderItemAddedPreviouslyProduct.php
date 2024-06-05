<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="itemsList.orderItem.footer", zone="admin", weight="10")
 */
class OrderItemAddedPreviouslyProduct extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/order_item_added_previously_product_link.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCreateOrder/order_item/added_previously_product/style.less';

        return $list;
    }

    protected $order;
    protected $profile;

    /**
     * @return null|\XLite\Model\Order
     */
    protected function getOrder()
    {
        if ($this->order === null) {
            $orderNumber = \XLite\Core\Request::getInstance()->order_number ?? null;

            if ($orderNumber) {
                $this->order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->findOneByOrderNumber(\XLite\Core\Request::getInstance()->order_number);
            }
        }

        return $this->order;
    }

    /**
     * @return null|\XLite\Model\Profile
     */
    protected function getProfile()
    {
        if ($this->profile === null) {
            $this->profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['profile_id' => $this->getOrder()->getOrigProfile()]);
        }

        return $this->profile;
    }

    protected function getProfileOrdersCount()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->origProfileId = $this->getProfile()->getProfileId();

        return \XLite\Core\Database::getRepo('XLite\Model\Order')
            ->search($cnd, true);
    }

    protected function isProfileHasAnotherOrders()
    {
        return $this->getProfileOrdersCount() > 1;
    }

    protected function isShowPreviouslyLink()
    {
        return $this->getOrder()
            && $this->getProfile()
            && !$this->getProfile()->getAnonymous()
            && $this->isProfileHasAnotherOrders();
    }
}