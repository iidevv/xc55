<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActDocumentsTab\View\Product\Details\Customer\Page;

use Qualiteam\SkinActDocumentsTab\Trait\DocumentsTabTrait;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Abstract product page
 *
 * @Extender\Mixin
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    use DocumentsTabTrait;

    /**
     * Process global tab addition into list
     *
     * @param                                  $list
     * @param \XLite\Model\Product\IProductTab $tab
     */
    protected function applyStaticTabListValue(&$list, $tab)
    {
        parent::applyStaticTabListValue($list, $tab);

        if ($tab->getServiceName() === $this->getDocumentsTabLabel()
            && $this->hasDocuments()
        ) {
            $list[$tab->getServiceName()] = [
                'list'   => 'product.details.page.documents',
                'weight' => $tab->getPosition(),
            ];
        }
    }

    /**
     * Check - product has Documents tab or not
     *
     * @return boolean
     */
    protected function hasDocuments()
    {
        return 0 < $this->getProduct()->getAttachments()->count();
    }
}