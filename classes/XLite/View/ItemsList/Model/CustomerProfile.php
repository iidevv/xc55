<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Profiles items list
 */
class CustomerProfile extends \XLite\View\ItemsList\Model\Profile
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['customer_profiles']);
    }

    /**
     * @return string
     */
    protected function getFormTarget()
    {
        return 'customer_profiles';
    }

    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'customer_profiles',
        ];
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\Profile\Admin\CustomerProfiles';
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\CustomerProfile';
    }

    /**
     * Get permitted user types
     *
     * @return array Array of ids
     */
    protected function getPermittedUserTypes()
    {
        return ['N', 'C'];
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $permittedUserTypes = $this->getPermittedUserTypes();

        if (
            isset($result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0])
            && $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE}[0] !== ''
        ) {
            $userTypes                                             = array_filter(
                $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE},
                static function ($type) use ($permittedUserTypes) {
                    return in_array($type, $permittedUserTypes);
                }
            );
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $userTypes;
        } else {
            $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = $permittedUserTypes;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'XLite\View\ItemsList\Model\CustomerProfile'
        ];
    }
}
