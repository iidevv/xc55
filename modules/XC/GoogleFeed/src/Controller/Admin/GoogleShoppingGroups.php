<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Controller\Admin;

use XLite\Core\Database;

/**
 * Sitemap
 */
class GoogleShoppingGroups extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Feeds');
    }

    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        return Database::getRepo('XLite\Model\Config')
            ->findByCategoryAndVisible($this->getOptionsCategory());
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\GoogleFeed';
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: 'XC\GoogleFeed\View\ItemsList\Model\Attribute\ShoppingGroup';
    }

    /**
     * Do action assign group
     *
     * @throws \Exception
     */
    protected function doActionAssignGroup()
    {
        $select = array_filter(\XLite\Core\Request::getInstance()->select);
        $group = \XLite\Core\Request::getInstance()->groupToSet;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Attribute')->updateGroupInBatch(
                array_keys($select),
                $group
            );
            \XLite\Core\TopMessage::addInfo(
                'Attribute information has been successfully updated'
            );
        } elseif ($ids = $this->getActionIds()) {
            \XLite\Core\Database::getRepo('XLite\Model\Attribute')->updateGroupInBatch(
                $ids,
                $group
            );
            \XLite\Core\TopMessage::addInfo('Attribute information has been successfully updated');
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the attributes first');
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getActionIds()
    {
        $cnd = $this->getItemsList()->getActionSearchCondition();
        $ids = \XLite\Core\Database::getRepo('XLite\Model\Attribute')
            ->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_IDS);
        $ids = is_array($ids) ? array_unique(array_filter($ids)) : [];

        return $ids;
    }
}
