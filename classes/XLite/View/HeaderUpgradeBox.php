<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Upgrade top box
 *
 * @ListChild (list="admin.main.page.header.right", weight="900", zone="admin")
 */
class HeaderUpgradeBox extends \XLite\View\AView implements ProviderInterface
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'header/upgrade/controller.js';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'header/upgrade/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'header/upgrade/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    public function getPreloadedLanguageLabels(): array
    {
        return [
            'Updates are available (new core)'               => static::t('Updates are available (new core)'),
            'Updates are available (new core and one addon)' => static::t('Updates are available (new core and one addon)'),
            'Updates are available (new core and N addons)'  => static::t('Updates are available (new core and N addons)'),
            'Updates are available (one addon)'              => static::t('Updates are available (one addon)'),
            'Updates are available (N addons)'               => static::t('Updates are available (N addons)'),
            'Updates are available (N)'                      => static::t('Updates are available (N)')
        ];
    }
}
