<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Contact
 * @Extender\Mixin
 */
class Contact extends \CDev\ContactUs\View\Model\Contact
{

    /**
     * @inheritdoc
     */
    public function __construct($params = [], $sections = [])
    {
        parent::__construct($params, $sections);

        $this->schemaDefault = [
            'company'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Company',
                self::SCHEMA_PLACEHOLDER => static::t('Company'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'firstname'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Firstname',
                self::SCHEMA_PLACEHOLDER => static::t('Firstname'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'lastname'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Lastname',
                self::SCHEMA_PLACEHOLDER => static::t('Lastname'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'street'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Address',
                self::SCHEMA_PLACEHOLDER => static::t('Address'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'street2'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Address (line 2)',
                self::SCHEMA_PLACEHOLDER => static::t('Address (line 2)'),
                self::SCHEMA_REQUIRED    => false,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'city'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'City',
                self::SCHEMA_PLACEHOLDER => static::t('City'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'country'      => [
                self::SCHEMA_CLASS       => 'XLite\View\FormField\Select\Country',
                self::SCHEMA_LABEL       => 'Country',
                self::SCHEMA_PLACEHOLDER => static::t('Country'),
                self::SCHEMA_REQUIRED    => true,
                \XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID  => 'stateSelectorId',
                \XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID     => 'stateBoxId',
            ],
            'state'      => [
                self::SCHEMA_CLASS       => 'XLite\View\FormField\Select\State',
                self::SCHEMA_LABEL       => 'State',
                self::SCHEMA_PLACEHOLDER => static::t('State'),
                self::SCHEMA_REQUIRED    => true,
                \XLite\View\FormField\AFormField::PARAM_ID           => 'stateSelectorId',
                \XLite\View\FormField\Select\State::PARAM_SELECT_ONE => true,
            ],
            'zipcode'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Zip/postal code',
                self::SCHEMA_PLACEHOLDER => static::t('Zip/postal code'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'phone'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text\Phone',
                self::SCHEMA_LABEL       => 'Phone',
                self::SCHEMA_PLACEHOLDER => static::t('Phone'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'email'     => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text\Email',
                self::SCHEMA_LABEL       => 'E-mail',
                self::SCHEMA_PLACEHOLDER => static::t('Email'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'fax'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Fax',
                self::SCHEMA_PLACEHOLDER => static::t('Fax'),
                self::SCHEMA_REQUIRED    => false,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'site'      => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Web site',
                self::SCHEMA_PLACEHOLDER => static::t('http://'),
                self::SCHEMA_REQUIRED    => false,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'department'      => [
                self::SCHEMA_CLASS       => '\Qualiteam\SkinActContactUsPage\View\FormField\Select\DepartmentSelect',
                self::SCHEMA_LABEL       => 'Department',
                self::SCHEMA_PLACEHOLDER => static::t('SkinActContactUsPage Department'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'subject'   => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Subject',
                self::SCHEMA_PLACEHOLDER => static::t('Subject'),
                self::SCHEMA_REQUIRED    => true,
                self::SCHEMA_ATTRIBUTES  => [
                    'class' => "show-valid-state"
                ],
            ],
            'message'   => [
                self::SCHEMA_CLASS       => '\XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL       => 'Message',
                self::SCHEMA_PLACEHOLDER => static::t('Your Message'),
                self::SCHEMA_REQUIRED    => true
            ],
            'recaptcha' => [
                self::SCHEMA_CLASS => '\CDev\ContactUs\View\FormField\Captcha',
            ],
        ];

    }

    /**
     * @inheritdoc
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        if (!$value) {
            $auth = \XLite\Core\Auth::getInstance();
            if ($auth->isLogged() && $auth->getProfile()->getAddresses()->count()) {
                $address = $auth->getProfile()->getAddresses()->first();

                $getterMethod = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($name);

                if ($name === 'site') {
                    $name = 'url';
                }

                if (method_exists($address, $getterMethod)) {
                    $value = $address->$getterMethod();
                } else {
                    // Get address property via common setterProperty() method
                    $value = $address->getterProperty($name);
                }
            }
        }

        return $value;
    }

    /**
     * Check if fields are valid
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        $this->prepareDataToValidate($data);

        parent::validateFields($data, $section);
    }

    /**
     * Prepare section data for validation
     *
     * @param array $data Current section data
     *
     * @return void
     */
    protected function prepareDataToValidate($data)
    {
        if (
            isset($data[self::SECTION_PARAM_FIELDS]['state'])
            && isset($data[self::SECTION_PARAM_FIELDS]['country'])
        ) {
            $stateField = $data[self::SECTION_PARAM_FIELDS]['state'];

            if ($stateField->getValue() == '') {
                $countryField = $data[self::SECTION_PARAM_FIELDS]['country'];

                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryField->getValue());

                // Disable state field required flag if selected country hasn't states
                if (!$country->hasStates()) {
                    $stateField->getWidgetParams(\XLite\View\FormField\AFormField::PARAM_REQUIRED)->setValue(false);
                }
            }
        }
    }
}