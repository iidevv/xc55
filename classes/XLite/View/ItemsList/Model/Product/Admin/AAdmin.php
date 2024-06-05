<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Abstarct admin-interface products list
 */
abstract class AAdmin extends \XLite\View\ItemsList\Model\Product\AProduct
{
    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X product(s) has been removed', ['count' => $count]);
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X product(s) has been created', ['count' => $count]);
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.product.blank');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    /**
     * Get search condition for update all found actions
     *
     * @return \XLite\Core\CommonCell
     */
    public function getActionSearchCondition()
    {
        $cnd = $this->getSearchCondition();
        unset($cnd->orderBy);

        return $cnd;
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        return array_merge(parent::getFormOptions(), [
            \XLite\View\Form\AForm::PARAM_CONFIRM_REMOVE => true
        ]);
    }
}
