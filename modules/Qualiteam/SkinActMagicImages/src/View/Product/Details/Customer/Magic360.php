<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Product\Details\Customer;

use Qualiteam\SkinActMagicImages\Classes\Helper;
use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite;
use XLite\Core\Converter;
use XLite\Core\Layout;
use XLite\Core\Request;

/**
 * Magic360
 *
 */
class Magic360 extends \XLite\View\Product\Details\Customer\Widget
{
    use MagicImagesTrait;

    /**
     * Product unique ID
     *
     * @var   integer
     *
     */
    protected $currentProductId = null;
    /**
     * Rendered HTML
     *
     * @var   string
     */
    protected $renderedHTML = '';
    /**
     * Additional classes
     *
     * @var   string
     *
     */
    protected $additionalClasses = '';
    /**
     * Scroll options
     *
     * @var   string
     *
     */
    protected $scrollOptions = '';

    public function getFingerprint()
    {
        return 'widget-fingerprint-magic360';
    }

    /**
     * Get JS files list
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $tool = Helper::getInstance()->getPrimaryTool();
        if ($tool->params->checkValue('enable-effect', 'Yes', 'product')) {
            if (static::hasProductSpin($this->getProduct())) {
                $list[] = $this->getModulePath() . '/js/controller.js';
            }
        }

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/product/details/style.less';

        return $list;
    }

    /**
     * Method to get rendered HTML
     *
     * @return string
     */
    public function getHTML()
    {
        return $this->renderedHTML;
    }

    /**
     * Method to get ID
     *
     * @return integer|string
     */
    public function getPid()
    {
        return ($this->currentProductId !== null) ? $this->currentProductId : '';
    }

    /**
     * Method to get additional classes
     *
     * @return string
     */
    public function getAdditionalClasses()
    {
        return $this->additionalClasses;
    }

    /**
     * Method to get scroll options
     *
     * @return string
     */
    public function getScrollOptions()
    {
        return $this->scrollOptions;
    }

    /**
     * Method to get option value
     *
     * @param string $option Option name
     *
     * @return mixed
     */
    public function getOptionValue($option)
    {
        $tool = Helper::getInstance()->getPrimaryTool();
        $tool->params->setProfile('product');

        return $tool->params->getValue($option);
    }

    /**
     * Method to render Magic360 HTML
     *
     * @return boolean
     */
    public function renderTemplate()
    {
        $helper = Helper::getInstance();
        $tool   = $helper->getPrimaryTool();
        $tool->params->setProfile('product');
        if ($tool->params->checkValue('enable-effect', 'No')) {
            return false;
        }

        $thumbMaxWidth  = intval($tool->params->getValue('thumb-max-width', 'product'));
        $thumbMaxHeight = intval($tool->params->getValue('thumb-max-height', 'product'));

        $product                = $this->getProduct();
        $magicSwatchesSet       = $this->getSwatchMagicImages();
        $this->currentProductId = $product->getId();
        $images                 = $magicSwatchesSet ? $magicSwatchesSet->getImages()->toArray() : [];
        $imagesCount            = count($images);
        if ($imagesCount) {
            $tool->params->setValue('columns', $magicSwatchesSet->getSpinColumns());
        } else {
            //NOTE: old way
            $images = $magicSwatchesSet ? $magicSwatchesSet->getImages()->toArray() : [];
        }

        $toolData = [];

        foreach ($images as $index => $image) {

            $img = $image->getURL();
            [$width, $height, $thumb] = $image->doResize($thumbMaxWidth, $thumbMaxHeight, false);

            $toolData[] = [
                'medium' => $thumb,
                'img'    => $img,
            ];

        }

        $this->renderedHTML = $tool->getMainTemplate($toolData, ['id' => 'productMagic360']);
        $this->renderedHTML = '<div class="MagicToolboxContainer widget-fingerprint-magic360">' . $this->renderedHTML . '</div>';

        return true;
    }

    /**
     * @return \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet|null
     */
    protected function getSwatchMagicImages(): MagicSwatchesSet|null
    {
        $magicSetImages = $this->getProduct()->getMagicSwatchesSet();


        /** @var \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet $set */
        foreach ($magicSetImages as $set) {
            if (!$set->getAttributeValue()) {
                return $set;
            }

            if (in_array($set->getAttributeValue()->getId(), $this->getAttributeValuesInRequest())) {
                return $set;
            }
        }

        return $magicSetImages ? $magicSetImages[0] : null;
    }

    /**
     * @return array
     */
    protected function getAttributeValuesInRequest(): array
    {
        $attributeValuesArr = $this->prepareAttributeValuesByRequest();
        $values             = [];

        foreach ($attributeValuesArr as $value) {
            $result             = explode('_', $value);
            $values[$result[0]] = $result[1];
        }

        return $values;
    }

    /**
     * @return array
     */
    protected function prepareAttributeValuesByRequest(): array
    {
        $attributeValuesArr = explode(',', Request::getInstance()->attribute_values);

        return array_filter($attributeValuesArr, function ($item) {
            return !empty($item);
        });
    }

    /**
     * Method to get default image URL
     *
     * @return string
     */
    public function getDefaultImageURL()
    {
        $url = null;

        $url = XLite::getInstance()->getOptions(['images', 'default_image']);

        if (!Converter::isURL($url)) {
            $url = Layout::getInstance()->getResourceWebPath(
                $url,
                Layout::WEB_PATH_OUTPUT_URL
            );
        }

        $url = str_replace(['http://', 'https://'], '//', $url);

        return $url;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/templates/magic360.twig';
    }
}
