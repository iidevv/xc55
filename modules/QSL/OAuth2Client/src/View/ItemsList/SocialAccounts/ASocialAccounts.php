<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\ItemsList\SocialAccounts;

/**
 * Abstract social accounts list
 */
abstract class ASocialAccounts extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Get current profile
     *
     * @return \XLite\Model\Profile
     */
    abstract protected function getProfile();

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
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getPageBodyDir() . '/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getPageBodyDir() . '/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' social-accounts';
    }

    /**
     * @inheritdoc
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyDir()
    {
        return 'modules/QSL/OAuth2Client/social_accounts';
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $condition->$modelParam = $paramValue;
            }
        }

        $condition->{\QSL\OAuth2Client\Model\Repo\Provider::P_ORDER_BY} = [
            'p.position',
            'asc',
        ];

        return $condition;
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        /** @var \QSL\OAuth2Client\Model\Repo\Provider $repo */ #nolint
        $repo = \XLite\Core\Database::getRepo('QSL\OAuth2Client\Model\Provider');

        return $repo->search($cnd, $countOnly);
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getPageBodyFile();
    }

    /**
     * @inheritdoc
     */
    protected function getEmptyListTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getEmptyListFile();
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    // {{{ Content helpers

    /**
     * Get line tag attributes
     *
     * @param \QSL\OAuth2Client\Model\Provider        $provider Provider
     * @param \QSL\OAuth2Client\Model\ExternalProfile $profile  External profile OPTIONAL
     *
     * @return array
     */
    protected function getLineTagAttributes(\QSL\OAuth2Client\Model\Provider $provider, \QSL\OAuth2Client\Model\ExternalProfile $profile = null)
    {
        return [
            'class' => [
                'social-account',
                $provider->getServiceName() . '-provider',
                ($profile ? 'linked' : 'unlinked'),
            ],
        ];
    }

    /**
     * Get auth. request URL
     *
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return string
     */
    protected function getAuthURL(\QSL\OAuth2Client\Model\Provider $provider)
    {
        return ($provider->getEnabled() && $provider->getWrapper()->isConfigured())
            ? $provider->getWrapper()->getRequestURL(\XLite::getController()->getURL())
            : null;
    }

    // }}}
}
