<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Check if current page is page with Cloud Storage settings
     *
     * @return boolean
     */
    protected function isEgoodsSettings()
    {
        return \XLite::getController() instanceof \XLite\Controller\Admin\Module
            && $this->getModule()
            && $this->getModule() === 'CDev-Egoods';
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($this->isEgoodsSettings()) {
            switch ($option->getName()) {
                case 'storage_type':
                case 'amazon_access':
                case 'amazon_secret':
                case 'bucket':
                    $cell[static::SCHEMA_REQUIRED] = true;
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_SHOW => [
                            'enable_signed_urls' => [true],
                        ],
                    ];
                    break;

                case 'bucket_region':
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_SHOW => [
                            'enable_signed_urls' => [true],
                            'storage_type' => 'as3',
                        ],
                    ];
                    break;

                case 'do_endpoint':
                    $cell[static::SCHEMA_REQUIRED] = true;
                    $cell[static::SCHEMA_PLACEHOLDER] = static::t('region.digitaloceanspaces.com');
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_SHOW => [
                            'enable_signed_urls' => [true],
                            'storage_type' => 'dos',
                        ],
                    ];
                    break;
            }
        }

        return $cell;
    }

    /**
     * @param \XLite\View\FormField\AFormField $field Form field object
     * @param array                            $data  List of all fields
     *
     * @return array
     */
    protected function validateFormFieldDoEndpointValue($field, $data)
    {
        $errorMessage = null;
        $endpoint     = $field->getValue();

        if ($this->isEgoodsSettings()) {
            if (!preg_match('/^[a-zA-Z0-9_-]+\.digitaloceanspaces\.com/', $endpoint)) {
                $errorMessage = static::t('The endpoint field value must contain the full path');
            }
        }

        return [empty($errorMessage), $errorMessage];
    }

    protected function setModelProperties(array $data)
    {
        if ($this->isEgoodsSettings()) {
            if (isset($data['do_endpoint'])) {
                if (!empty($this->getErrorMessages()['do_endpoint'])) {
                    $data['do_endpoint'] = '';
                }
            }
        }

        parent::setModelProperties($data);
    }
}
