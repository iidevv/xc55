<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("Qualiteam\SkinActBannerAdvanced")
 */
class Banner extends \QSL\Banner\View\Model\Banner
{

    protected function postprocess()
    {
        $banner = $this->getModelObject();

        $allowedLocations = [
            'StandardTop',
            'StandardMiddle',
            'StandardBottom'
        ];

        if ($banner
            && $banner->getForMobileOnly()
            && !in_array($banner->getLocation(), $allowedLocations, true)
        ) {
            $banner->setLocation('StandardTop');
        }
    }

    protected function postprocessSuccessActionCreate()
    {
        parent::postprocessSuccessActionCreate();
        $this->postprocess();
    }

    protected function postprocessSuccessActionUpdate()
    {
        parent::postprocessSuccessActionUpdate();
        $this->postprocess();
    }

    protected function getFormFieldsForSectionDefault()
    {
        $fields = parent::getFormFieldsForSectionDefault();

        $banner = $this->getModelObject();

        if ($banner && $banner->getForMobileOnly()) {
            unset($fields['pages']);
        }

        return $fields;
    }

    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $newFields = [];

        foreach ($this->schemaDefault as $name => $data) {
            $newFields[$name] = $data;
            if ($name === 'products_pages') {
                $newFields['forMobileOnly'] = [
                    self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Enabled',
                    self::SCHEMA_LABEL => 'SkinActMobileAppBanners Mobile App only',
                    self::SCHEMA_REQUIRED => false,
                ];
            }
        }

        $banner = $this->getModelObject();

        if ($banner && $banner->getForMobileOnly()) {

            $unset = [
                'home_page',
                'navigation',
                'arrows',
                'parallax',
                'width',
                'height',
                'delay',
                'timeout',
                'effect',
                'position',
                'categories',
                'products_pages',
            ];

            foreach ($newFields as $name => $data) {
                if (in_array($name, $unset, true)) {
                    unset($newFields[$name]);
                }

            }

        }

        unset($newFields['mobile_position']);

        $this->schemaDefault = $newFields;
    }
}