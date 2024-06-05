<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;

class AddedPreviouslyProduct extends \XLite\Controller\Admin\ProductSelections
{
    protected $order;
    protected $profile;

    public function getOrder()
    {
        if ($this->order === null && \XLite\Core\Request::getInstance()->order_number) {
            $this->order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                ->findOneByOrderNumber(\XLite\Core\Request::getInstance()->order_number);
        }

        return $this->order;
    }

    public function isExcludedProductId($productId)
    {
        $productIds = \XLite\Core\Request::getInstance()->productIds;

        if ($productIds) {
            $productIds = array_map('intval', explode(',', $productIds));
            return in_array($productId, $productIds, true);
        }

        return false;
    }

    /**
     * Specific title for the excluded product
     * By default it is 'Already added'
     *
     * @param integer $productId Product ID
     *
     * @return string
     */
    public function getTitleExcludedProduct($productId)
    {
        return static::t('Already added');
    }

    /**
     * Specific CSS class for the image of the excluded product.
     * You can check the Font Awesome CSS library if you want some specific icons
     *
     * @param integer $productId
     *
     * @return string
     */
    public function getStyleExcludedProduct($productId)
    {
        return 'fa-selected';
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass()
            ?: '\Qualiteam\SkinActCreateOrder\View\ItemsList\Model\AddedPreviouslyProduct';
    }

    protected function getSearchParams()
    {
        $searchParams = parent::getSearchParams();

        $origProfileId = \XLite\Core\Request::getInstance()->origProfileId ?? 0;

        if ($origProfileId) {
            $searchParams['origProfileId'] = $origProfileId;
        }

        return $searchParams;
    }

    /**
     * @return object
     */
    protected function getProfileInfo($profileId)
    {
        if ($this->profile === null) {
            $this->profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['profile_id' => $profileId]);
        }

        return $this->profile;
    }

    protected function getProfileOrdersCount()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->origProfileId = $this->profile->getProfileId();

        return \XLite\Core\Database::getRepo('XLite\Model\Order')
            ->search($cnd, true);
    }

    protected function isProfileHasAnotherOrders()
    {
        return $this->getProfileOrdersCount() > 1;
    }

    protected function doActionIsShowPreviouslyLink()
    {
        $profileId = (int) \XLite\Core\Request::getInstance()->profile_id;
        $profile = $profileId ? $this->getProfileInfo($profileId) : null;
        $result['success'] = false;

        if ($profileId
            && $profile
            && !$profile->getAnonymous()
            && $this->isProfileHasAnotherOrders()
        ) {
            $result['success'] = true;
        }

        $this->printAJAX($result);
    }
}
