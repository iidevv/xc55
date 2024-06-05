<?php


/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;

/**
 * @ListChild (list="review.add.fields", zone="customer",  weight="399")
 */
class ReviewAdditionalFields extends \XLite\View\AView
{

    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if ($this->isUploadsVisible()) {
            $widget = new \Qualiteam\SkinActCustomerReviews\View\FileUploader();
            $common = $widget->getCommonFiles();

            $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], $common[static::RESOURCE_JS]);
            $list[static::RESOURCE_CSS] = array_merge($list[static::RESOURCE_CSS], $common[static::RESOURCE_CSS]);
        }

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isUploadsVisible()) {
            $widget = new \Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader\PhotoFileUploader();
            $js = $widget->getJSFiles();
            $list = array_merge($list, $js);
            $list[] = 'modules/Qualiteam/SkinActCustomerReviews/uploader/file_uploader/uploader.js';
        }

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCustomerReviews/ReviewAdditionalFields.css';

        if ($this->isUploadsVisible()) {
            $widget = new \Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader\PhotoFileUploader();
            $css = $widget->getCSSFiles();
            $list = array_merge($list, $css);
        }

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReviewAdditionalFields.twig';
    }

    protected function getConfig()
    {
        $config = null;

        if ($config === null) {
            $config = Config::getInstance()->XC->Reviews;
        }

        return $config;
    }

    protected function isTitleVisible()
    {
        if ($this->getConfig()->display_review_title) {
            return true;
        }

        return false;
    }

    protected function getTitleWidget()
    {
        $review = $this->getReview();

        $widget = new \XLite\View\FormField\Input\Text([
            \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('SkinActCustomerReviews Review title PLACEHOLDER'),
            \XLite\View\FormField\AFormField::PARAM_NAME => 'title',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            \XLite\View\FormField\Input\AInput::PARAM_PLACEHOLDER => static::t('SkinActCustomerReviews Review title'),
            \XLite\View\FormField\AFormField::PARAM_VALUE => $review->getTitle()
        ]);

        return '<label for="title" class="title">'.static::t('SkinActCustomerReviews Review title PLACEHOLDER').'</label><br />'.
            $widget->getContent();
    }

    protected function isAdvantagesVisible()
    {
        if ($this->getConfig()->display_advantages_field) {
            return true;
        }

        return false;
    }

    protected function getAdvantagesWidget()
    {
        $review = $this->getReview();

        $widget = new \XLite\View\FormField\Textarea\Simple([
            \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('SkinActCustomerReviews Review advantages PLACEHOLDER'),
            \XLite\View\FormField\AFormField::PARAM_NAME => 'advantages',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            \XLite\View\FormField\Input\AInput::PARAM_PLACEHOLDER => static::t('SkinActCustomerReviews Review advantages'),
            \XLite\View\FormField\AFormField::PARAM_VALUE => $review->getAdvantages(),
            \XLite\View\FormField\Textarea\ATextarea::PARAM_MAX_LENGTH => 5000,
            \XLite\View\FormField\AFormField::PARAM_COMMENT => static::t('SkinActCustomerReviews Textarea maxLen 5000'),
        ]);

        return '  <label for="advantages" class="advantages">'.static::t('SkinActCustomerReviews Review advantages PLACEHOLDER').'</label><br />'.
            $widget->getContent();
    }

    protected function isDisadvantagesVisible()
    {
        if ($this->getConfig()->display_disadvantages_field) {
            return true;
        }

        return false;
    }

    protected function getDisadvantagesWidget()
    {
        $review = $this->getReview();

        $widget = new \XLite\View\FormField\Textarea\Simple([
            \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('SkinActCustomerReviews Review disadvantages PLACEHOLDER'),
            \XLite\View\FormField\AFormField::PARAM_NAME => 'disadvantages',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            \XLite\View\FormField\Input\AInput::PARAM_PLACEHOLDER => static::t('SkinActCustomerReviews Review disadvantages'),
            \XLite\View\FormField\AFormField::PARAM_VALUE => $review->getDisadvantages(),
            \XLite\View\FormField\Textarea\ATextarea::PARAM_MAX_LENGTH => 5000,
            \XLite\View\FormField\AFormField::PARAM_COMMENT => static::t('SkinActCustomerReviews Textarea maxLen 5000'),
        ]);

        return '  <label for="disadvantages" class="disadvantages">'.static::t('SkinActCustomerReviews Review disadvantages PLACEHOLDER').'</label><br />'.
            $widget->getContent();
    }

    protected function isUploadsVisible()
    {
        if ($this->getConfig()->allow_upload_photos) {
            return true;
        }

        if ($this->getConfig()->allow_upload_videos) {
            return true;
        }

        return false;
    }

    protected function getUploadsWidget()
    {
        $review = $this->getReview();
        $reviewFiles = $review->getFiles()->toArray();

        $maxSize = \Qualiteam\SkinActCustomerReviews\Core\MaxUploadSize::get();

        $widget = new \Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader\PhotoFileUploader([
            \XLite\View\FormField\AFormField::PARAM_NAME => 'review_files',
            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            \XLite\View\FormField\AFormField::PARAM_VALUE => $reviewFiles,
            \XLite\View\FormField\AFormField::PARAM_COMMENT => static::t('SkinActCustomerReviews uploader comment', ['maxSize' => $maxSize]),
        ]);

        return $widget->getContent();
    }

}