<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model\Repo\Image\Banner;

/**
 * Product image
 *
 */
class Image extends \XLite\Model\Repo\Base\Image
{
    /**
     * Returns the name of the directory within 'root/images' where images stored
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'banner';
    }

    /**
     * Add prodct variant image to the list of storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $result = parent::defineStorageRepositories();

        $result[] = 'QSL\Banner\Model\Image\Banner\Image';

        return $result;
    }
}
