<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\View\Model\Review
{

    protected function getConfig()
    {
        $config = null;

        if ($config === null) {
            $config = Config::getInstance()->XC->Reviews;
        }

        return $config;
    }

    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        if ($this->getConfig()->display_review_title) {

            $this->schemaDefault['title'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews admin review title'),
                self::SCHEMA_REQUIRED => false,
            ];
        }

        if ($this->getConfig()->display_advantages_field) {

            $this->schemaDefault['advantages'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews admin review advantages'),
                self::SCHEMA_REQUIRED => false,
            ];
        }

        if ($this->getConfig()->display_disadvantages_field) {

            $this->schemaDefault['disadvantages'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews admin review disadvantages'),
                self::SCHEMA_REQUIRED => false,
            ];
        }


        if ($this->getConfig()->display_useful_to_you) {

            $this->schemaDefault['useful'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Integer',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews Yes count'),
                self::SCHEMA_REQUIRED => false,
            ];

            $this->schemaDefault['nonUseful'] = [
                self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Integer',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews No count'),
                self::SCHEMA_REQUIRED => false,
            ];
        }


        $isVis = $this->getConfig()->allow_upload_photos || $this->getConfig()->allow_upload_videos;

        if ($isVis
            && $this->getModelObject()->getFiles()->toArray()
        ) {

            $this->schemaDefault['files'] = [
                self::SCHEMA_CLASS => 'Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader\PhotoFileUploader',
                self::SCHEMA_LABEL => static::t('SkinActCustomerReviews admin review attachments'),
                self::SCHEMA_REQUIRED => false,
            ];
        }

    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->getModelObject()->getFiles()->toArray()) {
            $widget = new \Qualiteam\SkinActCustomerReviews\View\FormField\FileUploader\PhotoFileUploader();
            $js = $widget->getJSFiles();
            $list = array_merge($list, $js);
            $list[] = 'modules/Qualiteam/SkinActCustomerReviews/uploader/file_uploader/uploader.js';
        }

        return $list;
    }

    public function getDefaultFieldValue($name)
    {
        if ($name === 'files') {
            return $this->getModelObject()->getFiles()->toArray();
        }

        return parent::getDefaultFieldValue($name);
    }
}