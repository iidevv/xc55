<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Feed Settings Dialog widget.
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class FeedSettingsDialog extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets.
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'product_feed';

        return $list;
    }

    /**
     * Return title.
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return file name for the center part template.
     *
     * @return string
     */
    protected function getBody()
    {
        return 'modules/QSL/ProductFeeds/feed_settings/body.twig';
    }
}
