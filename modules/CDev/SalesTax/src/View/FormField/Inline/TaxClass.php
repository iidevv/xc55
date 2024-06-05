<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\FormField\Inline;

class TaxClass extends \XLite\View\Taxes\Inline\TaxClass
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isDefinedTaxClasses()
            ? parent::getDefaultTemplate()
            : 'modules/CDev/SalesTax/form_field/add_tax_class.twig';
    }

    /**
     * @return mixed
     */
    protected function isDefinedTaxClasses()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(static function () {
            return (bool) \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->findAll();
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @return string
     */
    public function getTaxClassesLink()
    {
        return \XLite\Core\Converter::buildURL('tax_classes');
    }
}
