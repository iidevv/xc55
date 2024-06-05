<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;

/**
 * List of admin profiles
 */
class AdminProfiles extends \XLite\Controller\Admin\AAdmin
{
    use SearchByFilterTrait;

    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return static::t('Admin users');
    }

    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: 'XLite\View\ItemsList\Model\AdminProfile';
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {
            // Clear stored filter within stored search conditions
            \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

            // Refresh search parameters from request
            $this->fillSearchValuesStorage();

            // Get ItemsList widget
            $widget = $this->getItemsList();

            // Search for single profile
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceof \XLite\Model\Profile) {
                // Prepare redirect to profile page
                $url = $this->buildURL('profile', '', ['profile_id' => $entity->getProfileId()]);
                $this->setReturnURL($url);
            }
        }
    }

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
}
