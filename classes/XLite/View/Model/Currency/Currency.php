<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\Currency;

/**
 * Currency model widget
 */
class Currency extends \XLite\View\Model\AModel
{
    /**
     * Default currency to use if no currency in request is provided
     */
    public const DEFAULT_CURRENCY = 'USD';

    /**
     * Schema of the currency section
     *
     * @var array
     */
    protected $currencySchema = [
        'trailing_zeroes' => [
            self::SCHEMA_CLASS      => '\XLite\View\FormField\Input\Checkbox',
            self::SCHEMA_LABEL      => 'Hide trailing zeros in fractional part',
            self::SCHEMA_REQUIRED   => false,
        ],
        'currency_id' => [
            self::SCHEMA_CLASS      => '\XLite\View\FormField\Select\CurrencyRich',
            self::SCHEMA_LABEL      => 'Store currency',
            self::SCHEMA_REQUIRED   => false,
            self::SCHEMA_ATTRIBUTES => ['data-filter' => '1'],
        ],
        'format' => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Select\FloatFormat',
            self::SCHEMA_LABEL    => 'Format',
            self::SCHEMA_REQUIRED => false,
        ],
//        'roundUp' => array(
//            self::SCHEMA_CLASS    => '\XLite\View\FormField\Select\RoundUp',
//            self::SCHEMA_LABEL    => 'RoundUp',
//            self::SCHEMA_REQUIRED => false,
//        ),
        'prefix' => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Prefix',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\Input\Text::PARAM_DO_NOT_TRIM => true,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'suffix' => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Suffix',
            self::SCHEMA_REQUIRED => false,
            \XLite\View\FormField\Input\Text::PARAM_DO_NOT_TRIM => true,
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
    ];

    /**
     * Currency (cache)
     *
     * @var \XLite\Model\Currency
     */
    protected $currency = null;

    /**
     * getCurrencySchema
     *
     * @return array
     */
    public function getCurrencySchema()
    {
        $e = $this->getDefaultModelObject()->getE();

        $this->currencySchema['format'][\XLite\View\FormField\Select\FloatFormat::PARAM_E] = $e;

        $this->currencySchema['format'][static::SCHEMA_ATTRIBUTES] = [
            'data-e'            => $e,
            'data-thousandpart' => \XLite\View\FormField\Select\FloatFormat::THOUSAND_PART,
            'data-hundredspart' => \XLite\View\FormField\Select\FloatFormat::HUNDRENDS_PART,
            'data-delimiter'    => \XLite\View\FormField\Select\FloatFormat::FORMAT_DELIMITER,
        ];

        $this->currencySchema['trailing_zeroes'][\XLite\View\FormField\Input\Checkbox::PARAM_IS_CHECKED]
            = (\XLite\Core\Config::getInstance()->General->trailing_zeroes == 1);

        return $this->currencySchema;
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    public function getFormFieldsForSectionDefault()
    {
        return $this->getFieldsBySchema($this->getCurrencySchema());
    }

    /**
     * getDefaultFieldValue
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        switch ($name) {
            case 'trailing_zeroes':
                $value = 1;
                break;

            default:
        }

        return $value;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Currency
     */
    protected function getDefaultModelObject()
    {
        if (!isset($this->currency)) {
            $this->currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->find(\XLite\Core\Config::getInstance()->General->shop_currency);
        }

        return $this->currency;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Currency\Currency';
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return parent::getDialogCSSClass() . ' currency-model-form-dialog';
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return 'XLite\View\StickyPanel\Model\Currency';
    }

    /**
     * Prepare request data for mapping into model object.
     * Model object is provided with methods:
     * prepareObjectForMapping <- getModelObject <- getDefaultModelObject (or getParam(self::PARAM_MODEL_OBJECT))
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        // Update trailing zeroes config option value
        // TODO? move it to separated method ?
        $trailingZeroes = \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findOneBy(['name' => 'trailing_zeroes', 'category' => 'General']);

        \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
            $trailingZeroes,
            ['value' => $data['trailing_zeroes'] == '1']
        );

        // We do not map "trailing zeroes" - it is a config option.
        unset($data['trailing_zeroes']);

        if (isset($data['format'])) {
            // Data format is divided into thousand and decimal separator (or any other if it would be necessary)
            $data = $data + $this->getFormatInfo($data);

            unset($data['format']);
        }

        return $data;
    }

    /**
     * Return format value of currency for format selector (depends on thousand and decimal delimiters)
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function getFormatInfo(array $data)
    {
        $result = \XLite\View\FormField\Select\FloatFormat::getDelimiters($data['format']);

        return [
            'thousandDelimiter' => $result[0],
            'decimalDelimiter'  => $result[1],
        ];
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
        $value = parent::getModelObjectValue($name);

        if ($name == 'format') {
            $value = \XLite\View\FormField\Select\FloatFormat::getFormat(
                $this->getModelObjectValue('thousandDelimiter'),
                $this->getModelObjectValue('decimalDelimiter')
            );
        } elseif ($name == 'trailing_zeroes') {
            $value = (\XLite\Core\Config::getInstance()->General->trailing_zeroes !== '');
        }

        return $value;
    }
}
