<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\ItemsList\Model;

/**
 * Social accounts items list
 */
class ExternalProfile extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/OAuth2Client/social_accounts/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        return [
            'providerName' => [
                static::COLUMN_NAME  => \XLite\Core\Translation::lbl('Name'),
            ],
            'last_login_date' => [
                static::COLUMN_NAME  => \XLite\Core\Translation::lbl('Last login date'),
            ],
            'account' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Linked account'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return 'QSL\OAuth2Client\Model\ExternalProfile';
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return [$this->getSortBy(), $this->getSortOrder()];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' oauth2-client-ext-profiles';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'QSL\OAuth2Client\View\StickyPanel\ItemsList\ExternalProfile';
    }

    // {{{ Behaviors

    /**
     * @inheritdoc
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    // {{{ Search

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\QSL\OAuth2Client\Model\Repo\ExternalProfile::P_ORDER_BY} = ['provider.position', 'ASC'];
        $result->{\QSL\OAuth2Client\Model\Repo\ExternalProfile::SEARCH_PROFILE} = $this->getProfile();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Helpers

    /**
     * Get provider name
     *
     * @param \QSL\OAuth2Client\Model\ExternalProfile $entity Entity
     *
     * @return string
     */
    protected function getProviderNameColumnValue(\QSL\OAuth2Client\Model\ExternalProfile $entity)
    {
        return $entity->getProvider()->getName();
    }

    /**
     * Get account name
     *
     * @param \QSL\OAuth2Client\Model\ExternalProfile $entity Entity
     *
     * @return string
     */
    protected function getAccountColumnValue(\QSL\OAuth2Client\Model\ExternalProfile $entity)
    {
        return $entity->getName();
    }

    /**
     * Preprocess account name
     *
     * @param string                                               $value  Account name
     * @param array                                                $column Column data
     * @param \QSL\OAuth2Client\Model\ExternalProfile $entity Entity
     *
     * @return string
     */
    protected function preprocessAccount($value, array $column, \QSL\OAuth2Client\Model\ExternalProfile $entity)
    {
        return $entity->getAccountLink()
            ? '<a href="' . htmlspecialchars($entity->getAccountLink()) . '">' . $value . '</a>'
            : $value;
    }

    /**
     * Preprocess last login date
     *
     * @param string                                               $value  Account name
     * @param array                                                $column Column data
     * @param \QSL\OAuth2Client\Model\ExternalProfile $entity Entity
     *
     * @return string
     */
    protected function preprocessLastLoginDate($value, array $column, \QSL\OAuth2Client\Model\ExternalProfile $entity)
    {
        return $value ? $this->formatDate($value) : static::t('n/a');
    }

    // }}}
}
