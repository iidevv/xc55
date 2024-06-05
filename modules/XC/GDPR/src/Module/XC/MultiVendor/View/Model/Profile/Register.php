<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\XC\MultiVendor\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class Register extends \XC\MultiVendor\View\Model\Profile\Register
{
    /**
     * @return array
     */
    protected function defineSchemaMain()
    {
        return array_merge(parent::defineSchemaMain(), [
            'gdpr_consent' => [
                self::SCHEMA_CLASS    => 'XC\GDPR\View\FormField\Input\GdprConsent',
                self::SCHEMA_LABEL    => 'I consent to the collection and processing of my personal data (register vendor form)',
                self::SCHEMA_REQUIRED => true
            ]
        ]);
    }

    /**
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $model = $this->getModelObject();

        if (isset($data['gdpr_consent'])) {
            $model->setGdprConsent($data['gdpr_consent']);
        }

        parent::setModelProperties($data);
    }
}
