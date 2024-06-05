<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Image
 *
 * @ListChild (list="product.details.page.image.photo", weight="10")
 * @ListChild (list="product.details.quicklook.image.photo", weight="100")
 */
class Image extends \XLite\View\Product\Details\Customer\ACustomer
{
    /**
     * Widget params names
     */

    // Cloud zoom layer maximum width
    public const PARAM_ZOOM_MAX_WIDTH = 'zoomMaxWidth';

    // Zoom coefficient
    public const PARAM_K_ZOOM = 'kZoom';

    // Relative horizontal position of the zoom box
    public const PARAM_ZOOM_ADJUST_X_PD = 'zoomAdjustXPD';
    public const PARAM_ZOOM_ADJUST_X_QL = 'zoomAdjustXQL';

    /**
     * Quicklook list name
     */
    public const QUICKLOOK_PAGE = 'product.details.quicklook.image';

    /**
     * Width and height values of the quicklook images
     */
    public const QUICKLOOK_IMAGE_WIDTH  = 300;
    public const QUICKLOOK_IMAGE_HEIGHT = 300;

    /**
     * Product has any image to ZOOM
     *
     * @var boolean
     */
    protected $isZoom;


    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['js'][] = 'cloud-zoom/cloud-zoom.js';
        $list['css'][] = 'cloud-zoom/cloud-zoom.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'product/details/controller.js';

        return $list;
    }

    /**
     * Returns cloud zoom mode (position)
     *
     * @return string|null
     */
    public function getCloudZoomClass()
    {
        return $this->getCloudZoomMode()
            ? 'cloud-zoom-' . $this->getCloudZoomMode()
            : '';
    }

    /**
     * Check if cloud zoom mode inside
     *
     * @return bool
     */
    public function isCloudZoomInside()
    {
        return $this->getCloudZoomMode() === \XLite\View\FormField\Select\CloudZoomMode::MODE_INSIDE;
    }

    /**
     * Returns cloud zoom mode (position)
     *
     * @return string|null
     */
    public function getCloudZoomMode()
    {
        return \XLite\Core\Layout::getInstance()->getCloudZoomMode();
    }

    /**
     * Returns cloud zoom mode (position)
     *
     * @return string|null
     */
    public function getPreparedCloudZoomMode()
    {
        $mode = $this->getCloudZoomMode();

        if ($mode === \XLite\View\FormField\Select\CloudZoomMode::MODE_OUTSIDE) {
            return null;
        }

        return "'" . $mode . "'";
    }

    /**
     * Return a relative horizontal position of the zoom box
     * depending on whether it is a quicklook popup, or not
     *
     * @return integer
     */
    public function getZoomAdjustX()
    {
        return strpos($this->viewListName, 'quicklook')
            ? $this->getParam(self::PARAM_ZOOM_ADJUST_X_QL)
            : $this->getParam(self::PARAM_ZOOM_ADJUST_X_PD);
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/parts/image-regular.twig';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return ($this->hasZoomImage()) ? $this->getZoomTemplate() : $this->getDefaultTemplate();
    }

    /**
     * Zoom template for quicklook and product widgets
     *
     * @return string
     */
    protected function getZoomTemplate()
    {
        return $this->getDir() . ($this->viewListName === static::QUICKLOOK_PAGE ? '/parts/image-zoom-quicklook.twig' : '/parts/image-zoom.twig');
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ZOOM_MAX_WIDTH   => new \XLite\Model\WidgetParam\TypeInt('Cloud zoom layer maximum width, px', 530),
            self::PARAM_K_ZOOM           => new \XLite\Model\WidgetParam\TypeFloat('Minimal zoom coefficient', 1.3),
            self::PARAM_ZOOM_ADJUST_X_PD => new \XLite\Model\WidgetParam\TypeInt('Relative horizontal position of the zoom box on the Product details page', 97),
            self::PARAM_ZOOM_ADJUST_X_QL => new \XLite\Model\WidgetParam\TypeInt('Relative horizontal position of the zoom box in the Quick look box', 32),
        ];
    }

    /**
     * Check if the product has any image to ZOOM
     *
     * @return boolean
     */
    protected function hasZoomImage()
    {
        if (!isset($this->isZoom)) {
            $layout = \XLite\Core\Layout::getInstance();
            $this->isZoom = $this->defineHasZoomImage() && $layout->isCloudZoomAllowed() && $layout->getCloudZoomEnabled();
        }

        return $this->isZoom;
    }

    /**
     * Define value for hasZoomImage() method
     *
     * @return boolean
     */
    protected function defineHasZoomImage()
    {
        $result = false;

        if ($this->getProduct()->hasImage()) {
            foreach ($this->getProduct()->getPublicImages() as $img) {
                if ($this->isImageZoomable($img)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Return true if image is zoomable
     *
     * @param $image \XLite\Model\Image\Product\Image
     *
     * @return boolean
     */
    protected function isImageZoomable($image)
    {
        return $image->getWidth() > $this->getParam(self::PARAM_K_ZOOM) * $this->getWidgetMaxWidth();
    }

    /**
     * Get zoom image
     *
     * @return string
     */
    protected function getZoomImageURL()
    {
        return $this->getProduct()->getImage()->getURL();
    }

    /**
     * Get zoom layer width
     *
     * @return integer
     */
    protected function getZoomWidth()
    {
        return min($this->getProduct()->getImage()->getWidth(), $this->getParam(self::PARAM_ZOOM_MAX_WIDTH));
    }

    /**
     * Get zoom layer height
     *
     * @return integer
     */
    protected function getZoomHeight()
    {
        $k = $this->getWidgetMaxHeight() / $this->getWidgetMaxWidth();

        return $this->getZoomWidth() * $k;
    }

    /**
     * Return the max image width depending on whether it is a quicklook popup, or not
     *
     * @return integer
     */
    protected function getWidgetMaxWidth()
    {
        return $this->viewListName == static::QUICKLOOK_PAGE
            ? static::QUICKLOOK_IMAGE_WIDTH
            : \XLite::getController()->getDefaultMaxImageSize(true);
    }

    /**
     * Get product image container max height
     *
     * @return boolean
     */
    protected function getWidgetMaxHeight()
    {
        return $this->viewListName == static::QUICKLOOK_PAGE
            ? static::QUICKLOOK_IMAGE_HEIGHT
            : \XLite::getController()->getDefaultMaxImageSize(false);
    }

    /**
     * Return data to send to JS
     *
     * @return array
     */
    protected function getJSData()
    {
        return [
            'kZoom' => $this->getParam(self::PARAM_K_ZOOM),
        ];
    }
}
