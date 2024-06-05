<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Controller\Admin;

/**
 * Update Feed Categories for selected controller
 */
class FeedCategories extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Update feed categories';
    }

    /**
     * Do the Update action.
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $form = new \QSL\ProductFeeds\View\Form\FeedCategoriesDialog();
        $form->getRequestData();

        if ($form->getValidationMessage()) {
            \XLite\Core\TopMessage::addError($form->getValidationMessage());
        } else {
            $updateInfo = $this->getUpdateInfo();
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($updateInfo);
            \XLite\Core\TopMessage::addInfo(
                static::t('Feed categories have been updated for X products', ['count' => count($updateInfo)])
            );
        }

        $this->setReturnURL($this->buildURL('product_list', '', ['mode' => 'search']));
    }

    /**
     * Return result array to update in batch list of products
     *
     * @return array
     */
    protected function getUpdateInfo()
    {
        return array_fill_keys(
            array_keys($this->getSelected()),
            $this->getUpdateInfoElement()
        );
    }

    /**
     * Return one element to update.
     *
     * @return array
     */
    protected function getUpdateInfoElement()
    {
        $data = [];
        foreach ($this->getPostedData() as $key => $value) {
            // Skip "Do not modify" entries
            if ($value != -1) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
