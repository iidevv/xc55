<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * bs related to user profile section
 * @Extender\Mixin
 */
class AdminProfile extends \XLite\View\Tabs\AdminProfile
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'social_accounts';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if ($this->getProfile() && !$this->getProfile()->isAdmin()) {
            /** @var \QSL\OAuth2Client\Model\Repo\Provider $repo */ #nolint
            $repo = \XLite\Core\Database::getRepo('QSL\OAuth2Client\Model\Provider');
            if ($repo->countActive() > 0) {
                $list['social_accounts'] = [
                    'title'    => 'Social accounts',
                    'template' => 'modules/QSL/OAuth2Client/page/social_accounts.twig',
                    'weight'   => 1000,
                ];
            }
        }

        return $list;
    }
}
