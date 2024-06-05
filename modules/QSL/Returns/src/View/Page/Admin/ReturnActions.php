<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Page\Admin;

use XLite\Model\Config;

/**
 * Return actions page view
 */
class ReturnActions extends \XLite\View\AView
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'return_actions';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/Returns/return_actions/body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/Returns/settings.less';

        return $list;
    }

    /**
     * @return Config
     */
    protected function getEnableActionsOption()
    {
        return $this->executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Config')
                ->findOneBy([
                    'name'     => 'enable_actions',
                    'category' => 'QSL\Returns',
                ]);
        });
    }

    /**
     * @return Config
     */
    protected function getHideOtherActionOption()
    {
        return $this->executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Config')
                ->findOneBy([
                    'name'     => 'hide_other_action',
                    'category' => 'QSL\Returns',
                ]);
        });
    }
}
