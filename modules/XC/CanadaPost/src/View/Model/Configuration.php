<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Model;

/**
 * Canada Post configuration form model
 *
 */
class Configuration extends \XLite\View\Model\AModel
{
    /**
     * Indexes in field schemas
     */
    public const SCHEMA_DISABLED_IN_PRODUCTION = 'disabledInProduction';
    public const SCHEMA_HIDDEN_IN_PRODUCTION   = 'hiddenInProduction';

    /**
     * Default form schema
     *
     * @var array
     */
    protected $schemaDefault = [

        /**
         * Authentication options
         */
        'sep_authentication' =>  [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Authentication options',
        ],
        'user' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'User',
            self::SCHEMA_DISABLED_IN_PRODUCTION => true,
        ],
        'password' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Secure',
            self::SCHEMA_LABEL    => 'Password',
            self::SCHEMA_DISABLED_IN_PRODUCTION => true,
        ],
        'developer_mode' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Developer mode',
            self::SCHEMA_HIDDEN_IN_PRODUCTION => true,
        ],
        'debug_enabled' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Log all communications between shopping cart and Canada Post server',
        ],

        /**
         * Other options
         */
        'sep_common' =>  [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Common options',
        ],
        'quote_type' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Select\QuoteType',
            self::SCHEMA_LABEL    => 'Quote type',
        ],
        'customer_number' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Customer number',
            self::SCHEMA_DISABLED_IN_PRODUCTION => true,
        ],
        'currency_rate' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Currency rate',
            self::SCHEMA_COMMENT  => 'Specify rate X, where 1 CAD = X in shop currency',
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_E => 4,
        ],

        /**
         * Contract shipping options
         */
        'sep_contract_options'    => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Contract shipping options'
        ],
        'contract_id'             => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Contract ID',
            self::SCHEMA_DISABLED_IN_PRODUCTION => true,
        ],
        'pick_up_type'            => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Select\PickUpType',
            self::SCHEMA_LABEL    => 'Shipments pick up type',
        ],
        'deposit_site_num' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Input\Text\DepositSiteNum',
            self::SCHEMA_LABEL    => 'Site number of the deposit location',
            self::SCHEMA_COMMENT  => 'Look up the site number using <a href="https://www.canadapost.ca/cpotools/apps/fdl/business/findDepositLocation?execution=e1s1">Find a Deposit Location</a>',
        ],
        'detailed_manifests'      => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\YesNo',
            self::SCHEMA_LABEL    => 'Render detailed manifest',
        ],
        'manifest_name'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Contact name for the manifest address',
        ],

        /**
         * Deliver to Post Office options
         */
        'sep_deliver_to_po'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Deliver to Post Office options',
        ],
        'deliver_to_po_enabled'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Enable "Deliver to Post Office" feature'
        ],
        'max_post_offices'        => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Maximum Post Offices that will be displayed',
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 1,
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MAX => 50,
        ],

        /**
         * Parcel characteristics
         */
        'sep_parcel_characteristics' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Parcel characteristics',
        ],
        'length' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Input\Text\Dimension',
            self::SCHEMA_LABEL    => 'Package length (cm)',
            self::SCHEMA_COMMENT  => 'Longest dimension. (3.1 digits e.g. 999.9 pattern)',
        ],
        'width' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Input\Text\Dimension',
            self::SCHEMA_LABEL    => 'Package width (cm)',
            self::SCHEMA_COMMENT  => 'Second longest dimension. (3.1 digits e.g. 999.9 pattern)',
        ],
        'height' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Input\Text\Dimension',
            self::SCHEMA_LABEL    => 'Package height (cm)',
            self::SCHEMA_COMMENT  => 'Shortest dimension. (3.1 digits e.g. 999.9 pattern)',
        ],
        'max_weight' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Package maximum weight (kg)',
            self::SCHEMA_COMMENT  => 'This value will be used to separate ordered products into several packages by weight',
        ],
        'document' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Document',
            self::SCHEMA_COMMENT  => 'Indicates whether the shipment is a document or not.',
        ],
        'unpackaged' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Unpackaged',
            self::SCHEMA_COMMENT  => 'Indicates whether a shipment is unpackaged or not. For example, auto tires may be an example of an unpackaged shipment.',
        ],
        'mailing_tube' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Mailing tube',
            self::SCHEMA_COMMENT  => 'Indicates whether a shipment is contained in a mailing tube. (e.g. a poster tube)',
        ],
        'oversized' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Oversized',
            self::SCHEMA_COMMENT  => 'Indicates whether the parcel is oversized or not.',
        ],

        /**
         * Parcel options
         */
        'sep_parcel_options' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Parcel options',
        ],
        'way_to_deliver' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Select\DeliveryWayType',
            self::SCHEMA_LABEL    => 'Way to deliver',
        ],
        'signature' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL    => 'Signature',
        ],
        'age_proof' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Select\AgeProofType',
            self::SCHEMA_LABEL    => 'Proof of age',
        ],
        'coverage' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Coverage',
            self::SCHEMA_COMMENT  => 'In percent of the order subtotal (0 - do not use coverage)',
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_E   => 2,
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 0,
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MAX => 100,
        ],
        'non_delivery' => [
            self::SCHEMA_CLASS    => 'XC\CanadaPost\View\FormField\Select\NonDeliveryType',
            self::SCHEMA_LABEL    => 'Non-delivery instructions',
        ],
    ];

    /**
     * Return list of form fields
     *
     * @param boolean $onlyNames Flag; return objects or only the indexes OPTIONAL
     *
     * @return array
     */
    protected function getFormFields($onlyNames = false)
    {
        if (!isset($this->formFields)) {
            // Prepare default form fields
            $this->prepareDefaultFormFields();
        }

        return parent::getFormFields($onlyNames);
    }

    /**
     * Prepare default form fields
     *
     * @return void
     */
    protected function prepareDefaultFormFields()
    {
        if (!$this->isStoreInDevelopmentMode()) {
            foreach ($this->schemaDefault as $name => &$value) {
                if (
                    isset($value[static::SCHEMA_DISABLED_IN_PRODUCTION])
                    && $value[static::SCHEMA_DISABLED_IN_PRODUCTION]
                ) {
                    // Disable fields
                    $value += [
                        \XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES => [
                            'disabled' => 'disabled',
                        ],
                    ];
                }

                if (
                    isset($value[static::SCHEMA_HIDDEN_IN_PRODUCTION])
                    && $value[static::SCHEMA_HIDDEN_IN_PRODUCTION]
                ) {
                    // Hide fields
                    unset($this->schemaDefault[$name]);
                }
            }
        }
    }

    /**
     * Check - is store in development mode
     *
     * @return boolean
     */
    protected function isStoreInDevelopmentMode()
    {
        return LC_DEVELOPER_MODE;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );
        $result['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            ]
        );

        return $result;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        return \XLite\Core\Config::getInstance()->XC->CanadaPost->{$name};
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XC\CanadaPost\View\Form\Configuration';
    }
}
