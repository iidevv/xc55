<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors;
use XLite\Core\TopMessage;

class QuickbooksSyncErrors extends QuickbooksSyncData
{
    use SearchByFilterTrait;
    
    /**
     * Reset all errors
     * 
     * @return void
     */
    public function doActionResetAllErrors()
    {
        \XLite\Core\Database::getRepo(QuickbooksOrderErrors::class)
            ->deleteAllOrdersErrors();
        
        TopMessage::addInfo(static::t('All QuickBooks errors have been reset'));
    }
    
    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncErrors';
    }
    
    // {{{ Search
    /**
     * Save search conditions
     */
    protected function doActionSearchItemsList()
    {
        // Clear stored filter within stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getSessionSearchConditions()
    {
        return $this->postProcessSearchParams(
            parent::getSessionSearchConditions()
        );
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return $this->postProcessSearchParams(
            parent::getSearchParams()
        );
    }

    /**
     * Postprocess search parameters
     *
     * @param array $params Array of search parameters
     *
     * @return array
     */
    protected function postProcessSearchParams($params)
    {
        if (empty($params[\XLite\View\ItemsList\Model\Profile::PARAM_COUNTRY])) {
            // Country value is empty: make state and custom state values are empty as well
            $params[\XLite\View\ItemsList\Model\Profile::PARAM_STATE]        = '';
            $params[\XLite\View\ItemsList\Model\Profile::PARAM_CUSTOM_STATE] = '';
        } else {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find(
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_COUNTRY]
            );
            if (!$country || !$country->hasStates()) {
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_STATE] = '';
            }
            if (!$country || $country->hasStates()) {
                $params[\XLite\View\ItemsList\Model\Profile::PARAM_CUSTOM_STATE] = '';
            }
        }

        return $params;
    }

    // }}}
}