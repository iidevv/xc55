<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Process global tab addition into list
     *
     * @param                                  $list
     * @param \XLite\Model\Product\IProductTab $tab
     */
    protected function applyStaticTabListValue(&$list, $tab)
    {
        parent::applyStaticTabListValue($list, $tab);

        if (
            $tab->getServiceName() === 'Comments'
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_comments_use
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id
        ) {
            $list[$tab->getServiceName()] = [
                'list'   => 'product.details.page.tab.comments',
                'weight' => $tab->getPosition(),
            ];
        }
    }
}
