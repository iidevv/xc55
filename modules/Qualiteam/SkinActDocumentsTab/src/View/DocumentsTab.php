<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActDocumentsTab\View;

use Qualiteam\SkinActDocumentsTab\Trait\DocumentsTabTrait;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Database;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeObject;

/**
 * Class documents tab
 *
 * @ListChild (list="product.details.page.documents")
 */
class DocumentsTab extends \XLite\View\AView
{
    use DocumentsTabTrait;

    /**
     * Widget param names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new TypeObject(
                'Product',
                null,
                false,
                Product::class
            ),
        ];
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/tabs/body.twig';
    }

    /**
     * getProduct
     *
     * @return \XLite\Model\Product|null
     */
    protected function getProduct(): ?Product
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        return $this->getProduct()->getFilteredAttachments(\XLite\Core\Auth::getInstance()->getProfile())->toArray();
    }
}