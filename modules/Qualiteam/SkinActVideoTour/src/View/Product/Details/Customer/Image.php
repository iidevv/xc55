<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Product\Details\Customer;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class image
 * @Extender\Mixin
 */
class Image extends \XLite\View\Product\Details\Customer\Image
{
    use VideoTourTrait;

    /**
     * Has video tour
     *
     * @return bool
     */
    public function hasVideoTour(): bool
    {
        return count($this->getProduct()->getVideoTours()) > 0;
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/product/details/parts/image-regular.twig';
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * The method must return the array of the CSS file paths:
     *
     * return array(
     *      'modules/Developer/Module/style.css',
     *      'styles/css/main.css',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getCSSFiles(),
     *      array(
     *          'modules/Developer/Module/style.css',
     *          'styles/css/main.css',
     *          ...
     *      )
     * );
     *
     * LESS resource usage:
     * You can also use the less resources along with the CSS ones.
     * The LESS resources will be compiled into CSS.
     * However you can merge your LESS resource with another one using 'merge' parameter.
     * 'merge' parameter must contain the file path to the parent LESS file.
     * In this case the resources will be linked into one LESS file with the '@import' LESS instruction.
     *
     * !Important note!
     * Right now only one parent is supported, so you cannot link the resources in LESS chain.
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its CSS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * The best practice is to merge LESS resources with 'bootstrap/css/bootstrap.less' file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/image/style.less';

        return $list;
    }
}