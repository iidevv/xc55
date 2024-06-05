<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Controller\Admin;

/**
 * Special offer controller
 */
class SpecialOffer extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'offer_id', 'type_id'];

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getSpecialOffer();

        return ($model && $model->getOfferId())
            ? $model->getName()
            : \XLite\Core\Translation::getInstance()->lbl('Special Offer');
    }

    /**
     * Returns the name of the view class that renders the page.
     *
     * @return string
     */
    public function getPageWidgetClass()
    {
        return $this->getModelFormClass();
    }

    /**
     * Check if the view model class is available.
     *
     * @return bool
     */
    public function isOfferTypeEnabled()
    {
        return class_exists($this->getPageWidgetClass());
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'Special offers',
            $this->buildURL('special_offers')
        );
    }

    /**
     * Update model
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('special_offers'));
        }
    }

    /**
     * Get model form class name.
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        $type = $this->getOfferType();

        return $type ? $type->getViewModelClass() : '';
    }

    /**
     * Returns the model for the special offer being edited.
     *
     * @return \QSL\SpecialOffersBase\Model\SpecialOffer
     */
    protected function getSpecialOffer()
    {
        $id = (int) \XLite\Core\Request::getInstance()->offer_id;

        return $id
            ? \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer')->find($id)
            : null;
    }

    /**
     * Returns the offer type model for the special offer being edited.
     *
     * @return \QSL\SpecialOffersBase\Model\OfferType
     */
    protected function getOfferType()
    {
        $model = $this->getSpecialOffer();

        return $model
            ? $model->getOfferType()
            : \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType')->find(
                (int) \XLite\Core\Request::getInstance()->type_id
            );
    }
}
