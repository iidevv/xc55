<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated controller for the Image Upload dialog.
 * @Extender\Mixin
 */
class SelectFile extends \XLite\Controller\Admin\SelectFile
{
    /**
     * Common handler for brand images.
     *
     * @param string $methodToLoad Method to use for getting images
     * @param array  $paramsToLoad Parameters to use in image getter method
     */
    protected function doActionSelectBrandImage($methodToLoad, array $paramsToLoad)
    {
        $id = (int) \XLite\Core\Request::getInstance()->objectId;

        $brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find($id);

        $image = $brand->getImage();

        if (!$image) {
            $image = new \QSL\ShopByBrand\Model\Image\Brand\Image();
        }

        if (call_user_func_array([$image, $methodToLoad], $paramsToLoad)) {
            $image->setBrand($brand);

            $brand->setImage($image);

            \XLite\Core\Database::getEM()->persist($image);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo(
                'The brand image has been updated'
            );
        } else {
            \XLite\Core\TopMessage::addError(
                'Failed to update brand image'
            );
        }
    }

    /**
     * "Upload" handler for category images.
     */
    protected function doActionSelectUploadBrandImage()
    {
        $this->doActionSelectBrandImage('loadFromRequest', ['uploaded_file']);
    }

    /**
     * "URL" handler for brand images.
     */
    protected function doActionSelectUrlBrandImage()
    {
        $this->doActionSelectBrandImage(
            'loadFromURL',
            [
                \XLite\Core\Request::getInstance()->url,
                (bool) \XLite\Core\Request::getInstance()->url_copy_to_local,
            ]
        );
    }

    /**
     * "Local file" handler for brand images.
     */
    protected function doActionSelectLocalBrandImage()
    {
        $file = \XLite\View\BrowseServer::getNormalizedPath(\XLite\Core\Request::getInstance()->local_server_file);

        $this->doActionSelectBrandImage(
            'loadFromLocalFile',
            [$file]
        );
    }

    /**
     * Return parameters array for "Brand" target
     *
     * @return array
     */
    protected function getParamsObjectBrand()
    {
        return [
            'brand_id' => \XLite\Core\Request::getInstance()->objectId,
        ];
    }
}
