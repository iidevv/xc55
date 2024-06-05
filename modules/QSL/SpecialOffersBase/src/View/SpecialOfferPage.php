<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Brand page widget.
 *
 * @ListChild (list="center", zone="customer")
 */
class SpecialOfferPage extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'special_offer';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/SpecialOffersBase/offer_page/styles.css';

        return $list;
    }

    /**
     * Check if the special offer has the image.
     *
     * @return boolean
     */
    public function hasImage()
    {
        return !is_null($this->getImage());
    }

    /**
     * Get the special offer image.
     *
     * @return \XLite\Model\Image
     */
    public function getImage()
    {
        return $this->getOffer()->getImage();
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getOffer()->getDescription();
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getOffer()->getTitle();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/SpecialOffersBase/offer_page/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getOffer();
    }
}
