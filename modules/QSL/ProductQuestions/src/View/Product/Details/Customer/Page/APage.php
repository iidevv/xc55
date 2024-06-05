<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract product page
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

        if ($tab->getServiceName() === 'Questions') {
            $list[$tab->getServiceName()] = [
                'list'   => 'product.details.page.tab.questions',
                'weight' => $tab->getPosition(),
            ];
        }
    }
}
