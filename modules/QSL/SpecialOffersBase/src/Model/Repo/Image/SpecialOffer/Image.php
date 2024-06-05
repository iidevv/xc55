<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model\Repo\Image\SpecialOffer;

/**
 * Brand image repository class.
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' brand where images are located.
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'special_offers';
    }
}
