<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Controller\Customer;

/**
 * Individual special offer page.
 */
class SpecialOffer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'offer_id';
    }

    /**
     * Check if current page is accessible.
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return $this->getOffer() && parent::checkAccess();
    }

    /**
     * Get the special offer that we are rendering the page for.
     *
     * @return \QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getOffer()
    {
        return $this->getOfferId()
            ? \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer')->find($this->getOfferId())
            : null;
    }

    /**
     * Return the page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->checkAccess()
            ? $this->getOffer()->getTitle()
            : '';
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $entity = $this->getOffer();

        return $entity
            ? strip_tags(str_replace(['&nbsp;', "\n"], ' ', $entity->getDescription()))
            : '';
    }

    /**
     * Return the model that we are rendering the page for.
     *
     * @return \QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getModelObject()
    {
        return $this->getOffer();
    }

    /**
     * Common method to determine current location.
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->checkAccess()
            ? $this->getOffer()->getTitle()
            : 'Page not found';
    }

    /**
     * Return the ID of the special offer that we are rendering the page for.
     *
     * @return integer
     */
    protected function getOfferId()
    {
        return intval(\XLite\Core\Request::getInstance()->offer_id);
    }
}
