<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Controller\Admin;

class Tags extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Classes & attributes');
    }

    // {{{ Actions

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $list = new \XC\ProductTags\View\ItemsList\Model\Tag();
        $list->processQuick();
    }

    /**
     * Do action 'delete'
     */
    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo(
                'Selected tags have been deleted'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the tags first');
        }
    }

    // }}}
}
