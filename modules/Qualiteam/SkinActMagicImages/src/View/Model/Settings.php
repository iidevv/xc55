<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Model;

use Qualiteam\SkinActMagicImages\Model\Config;
use Qualiteam\SkinActMagicImages\View\Form\Settings as SettingsForm;
use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\FormField\Input\Checkbox\Simple as SimpleCheckbox;
use XLite\View\FormField\Input\Text;
use XLite\View\FormField\Select\Country;
use XLite\View\FormField\Select\Currency;
use XLite\View\FormField\Select\State;
use XLite\View\FormField\Separator\Regular;
use XLite\View\FormField\Textarea\Simple;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\AModel
{
    /**
     * Indexes in field schemas
     *
     */
    const SCHEMA_STATUS = 'paramStatus';

    /**
     * Get form fields for default section
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        return $this->getFieldsBySchema($this->getSchemaFields());
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFields()
    {
        $list = [];

        foreach ($this->getOptions() as $option) {
            $cell = $this->getFormFieldByOption($option);
            if ($cell) {
                $list[$option->getName()] = $cell;
            }
        }

        return $list;
    }

    /**
     * Get form field by option
     *
     * @param \Qualiteam\SkinActMagicImages\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(Config $option)
    {
        $cell = null;

        $class = $this->detectFormFieldClassByOption($option);

        if ($class) {
            $cell = [
                self::SCHEMA_CLASS    => $class,
                self::SCHEMA_LABEL    => $option->getOptionName(),
                self::SCHEMA_HELP     => $option->getOptionComment(),
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_STATUS   => $option->getStatus(),
            ];

            if ($this->isOptionRequired($option)) {
                $cell[self::SCHEMA_REQUIRED] = true;
            }

            $parameters = $option->getWidgetParameters();
            if ($parameters && is_array($parameters)) {
                $cell += $parameters;
            }
        }

        return $cell;
    }

    /**
     * Detect form field class by option
     *
     * @param \Qualiteam\SkinActMagicImages\Model\Config $option Option
     *
     * @return string
     */
    protected function detectFormFieldClassByOption(Config $option)
    {
        $class = null;
        $type  = $option->getType() ?: 'text';

        switch ($type) {
            case 'textarea':
                $class = Simple::class;
                break;
            case 'checkbox':
                $class = SimpleCheckbox::class;
                break;
            case 'country':
                $class = Country::class;
                break;
            case 'state':
                $class = State::class;
                break;
            case 'currency':
                $class = Currency::class;
                break;
            case 'separator':
                $class = Regular::class;
                break;
            case 'text':
                $class = Text::class;
                break;
            case 'hidden':
                break;
            default:
                if (preg_match('/^\\\?Qualiteam\\\/Ss', $option->getType())) {
                    $class = $option->getType();
                }
        }

        return $class;
    }

    /**
     * Check - option is required or not
     *
     * @param \Qualiteam\SkinActMagicImages\Model\Config $option Option
     *
     * @return boolean
     */
    protected function isOptionRequired(Config $option)
    {
        return false;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = [];

        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL    => 'Save settings',
                AButton::PARAM_BTN_TYPE => 'regular-main-button',
                AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        return true;
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
        $value = null;

        foreach ($this->getOptions() as $option) {
            if ($option->getName() == $name) {
                $value = $option->getValue();
                break;
            }
        }

        return $value;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     * @throws \Exception
     */
    protected function setModelProperties(array $data)
    {
        $optionsToUpdate = [];

        foreach ($this->getEditableOptions() as $key => $option) {
            $name = $option->name;
            $type = $option->type;

            if ('checkbox' == $type) {
                $newValue = empty($data[$name]) ? 'N' : 'Y';
            } elseif ('serialized' == $type && isset($data[$name]) && is_array($data[$name])) {
                $newValue = serialize($data[$name]);
            } elseif ('text' == $type) {
                $newValue = isset($data[$name]) ? trim($data[$name]) : '';
            } elseif ('\Qualiteam\SkinActMagicImages\View\FormField\MagicToolboxText' == $type) {
                $newValue = isset($data[$name]) ? strtoupper(trim($data[$name])) : '';
            } else {
                $newValue = $data[$name] ?? '';
            }

            if (isset($data[$name])) {
                if ($option->value != $newValue) {
                    $option->value = $newValue;
                    if ($option->status == 0) {
                        $option->status = 1;
                    }
                    $optionsToUpdate[] = $option;

                    if ('default' == $option->profile) {
                        $foundOptions = Database::getRepo(Config::class)->findBy([
                            'name'   => $option->name,
                            'status' => 0,
                        ]);
                        foreach ($foundOptions as $foundOption) {
                            if ($foundOption->value != $option->value) {
                                $foundOption->value = $option->value;
                                $optionsToUpdate[]  = $foundOption;
                            }
                        }
                    }

                } elseif ($option->status == 0) {
                    $option->status    = 1;
                    $optionsToUpdate[] = $option;
                }
            } elseif ($option->status == 1) {
                $option->status = 0;

                //NOTE: set value to default if exists
                $default = Database::getRepo(Config::class)->findOneBy([
                    'profile' => 'default',
                    'name'    => $option->name,
                ]);
                if ($default) {
                    $option->value = $default->value;
                }

                $optionsToUpdate[] = $option;
            }

        }

        if (!empty($optionsToUpdate)) {
            Database::getEM()->flush();
        }
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $options = $this->getOptions();
        $exclude = ['separator', 'hidden'];
        foreach ($options as $key => $option) {
            if (in_array($option->type, $exclude)) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Return true if param value may contain anything
     *
     * @param string $name Param name
     *
     * @return boolean
     */
    protected function isParamTrusted($name)
    {
        $list = ['images', 'message'];

        return parent::isParamTrusted($name) || in_array($name, $list);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Profile|null
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
        return SettingsForm::class;
    }

    protected function addDataSavedTopMessage()
    {
        TopMessage::addInfo('Magic 360 module settings have been saved successfully');
    }
}
