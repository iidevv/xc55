<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\FormField\Select;

/**
 * Form field to choose the special offer type.
 */
class OfferType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Set value.
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $options = $this->getDefaultOptions();
        if (!isset($options[$value])) {
            $value = key($options);
        }

        parent::setValue($value);
    }


    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = [];
        foreach ($this->getRepo()->findActiveOfferTypes() as $type) {
            $options[$type->getTypeId()] = $type->getName();
        }

        return $options;
    }

    /**
     * Returns the repository class used to retrieve offer types.
     *
     * @return \QSL\SpecialOffersBase\Model\Repo\OfferType
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType');
    }
}
