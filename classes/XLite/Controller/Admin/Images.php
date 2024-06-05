<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Images page controller
 */
class Images extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Resize
     *
     * @var \XLite\Logic\ImageResize\Generator
     */
    protected $imageResizeGenerator = null;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isImageResizeNotFinished()) {
            return static::t('Resizing images...');
        }

        return static::t('Images settings');
    }

    /**
     * Do action 'Update'
     *
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        $request = \XLite\Core\Request::getInstance();

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Performance',
            'name'     => 'use_dynamic_image_resizing',
            'value'    => (bool)$request->use_dynamic_image_resizing,
        ]);

        if ($this->isShowUnsharpOption()) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Performance',
                'name'     => 'unsharp_mask_filter_on_resize',
                'value'    => (bool)$request->unsharp_mask_filter_on_resize,
            ]);
        }

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Performance',
            'name'     => 'resize_quality',
            'value'    => (int)$request->resize_quality,
        ]);

        if (isset($request->cloud_zoom)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Layout',
                'name'     => 'cloud_zoom',
                'value'    => (bool)$request->cloud_zoom,
            ]);
        }

        if (isset($request->cloud_zoom_mode)) {
            \XLite\Core\Layout::getInstance()->setCloudZoomMode($request->cloud_zoom_mode);
        }

        if (isset($request->use_lazy_load)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Performance',
                'name'     => 'use_lazy_load',
                'value'    => (bool)$request->use_lazy_load,
            ]);
        }

        $list = new \XLite\View\ItemsList\Model\ImagesSettings();
        $list->processQuick();

        $this->createResizedLogo();
    }

    /**
     * Create resized image for logo
     */
    public function createResizedLogo()
    {
        $logoImage = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
        \XLite\Logic\ImageResize\Generator::clearImageSizesCache();
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/logo');
        $logoImage->prepareSizes();
    }

    /**
     * Return "Use dynamic image resizing" setting value
     *
     * @return string
     */
    public function getUseDynamicImageResizingValue()
    {
        return \XLite\Core\Config::getInstance()->Performance->use_dynamic_image_resizing;
    }

    /**
     * @return bool
     */
    public function isShowUnsharpOption()
    {
        return \XLite\Core\ImageOperator::getEngineType() === \XLite\Core\ImageOperator::ENGINE_GD;
    }

    /**
     * Return "Unsharp mask filter on resize" setting value
     *
     * @return bool
     */
    public function getUnsharpMaskFilterOnResizeValue()
    {
        return (bool)\XLite\Core\Config::getInstance()->Performance->unsharp_mask_filter_on_resize;
    }

    /**
     * Return "Resize quality" setting value
     *
     * @return integer
     */
    public function getResizeQuality()
    {
        return (int)\XLite\Core\Config::getInstance()->Performance->resize_quality ?: 85;
    }

    /**
     * Return "Lazy load images" setting value
     *
     * @return string
     */
    public function getLazyLoadValue()
    {
        return \XLite\Core\Config::getInstance()->Performance->use_lazy_load;
    }

    // {{{ Image resize methods

    /**
     * Get resize
     *
     * @return \XLite\Logic\ImageResize\Generator
     */
    public function getImageResizeGenerator()
    {
        if (!isset($this->imageResizeGenerator)) {
            $eventName = \XLite\Logic\ImageResize\Generator::getEventName();
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);
            $this->imageResizeGenerator = ($state && isset($state['options']))
                ? new \XLite\Logic\ImageResize\Generator($state['options'])
                : false;
        }

        return $this->imageResizeGenerator;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return bool
     */
    public function isImageResizeNotFinished()
    {
        $eventName = \XLite\Logic\ImageResize\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS],
                true
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImageResizeCancelFlagVarName());
    }

    /**
     * Export action
     */
    protected function doActionImageResize()
    {
        if (\XLite\Core\ImageOperator::getEngineType() === \XLite\Core\ImageOperator::ENGINE_SIMPLE) {
            \XLite\Core\TopMessage::addError("Image resizing requires libraries");
        } else {
            \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/category');
            \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/product');
            \XLite\Logic\ImageResize\Generator::run($this->assembleImageResizeOptions());
        }
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleImageResizeOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return [
            'include' => $request->section,
        ];
    }

    /**
     * Cancel
     */
    protected function doActionImageResizeCancel()
    {
        \XLite\Logic\ImageResize\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('The generation of resized images has been stopped.');
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->resize_completed) {
            \XLite\Core\TopMessage::addInfo('The generation of resized images has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('images')
            );
        } elseif ($request->resize_failed) {
            \XLite\Core\TopMessage::addError('The generation of resized images has been stopped.');

            $this->setReturnURL(
                $this->buildURL('images')
            );
        }
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getImageResizeCancelFlagVarName()
    {
        return \XLite\Logic\ImageResize\Generator::getCancelFlagVarName();
    }

    // }}}

    // {{{ Cloud Zoom

    /**
     * Check if cloud zoom enabled
     *
     * @return bool
     */
    public function getCloudZoomEnabled()
    {
        return \XLite\Core\Layout::getInstance()->getCloudZoomEnabled();
    }

    /**
     * Return cloud zoom mode
     *
     * @return string
     */
    public function getCloudZoomMode()
    {
        return \XLite\Core\Layout::getInstance()->getCloudZoomMode();
    }

    /**
     * Check if cloud zoom supported by skin
     *
     * @return bool
     */
    public function isCloudZoomAllowed()
    {
        return \XLite\Core\Layout::getInstance()->isCloudZoomAllowed();
    }

    // }}}
}
