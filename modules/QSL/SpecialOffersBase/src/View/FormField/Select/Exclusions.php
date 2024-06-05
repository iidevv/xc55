<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\FormField\Select;

/**
 * Form field to choose offers that may not apply together.
 */
class Exclusions extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Widget param names
     */
    public const PARAM_CURRENT_SPECIAL_OFFER = 'offer';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CURRENT_SPECIAL_OFFER => new \XLite\Model\WidgetParam\TypeObject(
                'Special offer',
                null,
                false,
                '\QSL\SpecialOffersBase\Model\SpecialOffer'
            ),
        ];
    }

    /**
     * Get current special offer.
     *
     * @return integer
     */
    protected function getSpecialOffer()
    {
        return $this->getParam(self::PARAM_CURRENT_SPECIAL_OFFER);
    }

    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = [];

        foreach ($this->getSpecialOfferRepo()->findAll() as $offer) {
            $options[$offer->getOfferId()] = $offer->getName();
        }

        return $options;
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $currentId = $this->getSpecialOffer() ? $this->getSpecialOffer()->getOfferId() : 0;
        if ($currentId) {
            unset($options[$currentId]);
        }

        return $options;
    }

    /**
     * Returns the repository object for SpecialOffer model.
     *
     * @return \QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getSpecialOfferRepo()
    {
        return \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer');
    }
}
