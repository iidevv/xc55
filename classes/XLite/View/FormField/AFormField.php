<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Abstract form field
 */
abstract class AFormField extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_VALUE      = 'value';
    public const PARAM_REQUIRED   = 'required';
    public const PARAM_ATTRIBUTES = 'attributes';
    public const PARAM_NAME       = 'fieldName';
    public const PARAM_ID         = 'fieldId';
    public const PARAM_LABEL      = 'label';
    public const PARAM_LABEL_PARAMS = 'labelParams';
    public const PARAM_COMMENT    = 'comment';
    public const PARAM_HELP       = 'help';
    public const PARAM_HELP_WIDGET       = 'helpWidget';
    public const PARAM_LABEL_HELP        = 'labelHelp';
    public const PARAM_LABEL_HELP_WIDGET = 'labelHelpWidget';
    public const PARAM_FIELD_ONLY      = 'fieldOnly';
    public const PARAM_WRAPPER_CLASS   = 'wrapperClass';
    public const PARAM_FORM_CONTROL   = 'formControl';
    public const PARAM_LINK_HREF       = 'linkHref';
    public const PARAM_LINK_TEXT       = 'linkText';
    public const PARAM_LINK_IMG        = 'linkImg';
    public const PARAM_TRUSTED         = 'trusted';
    public const PARAM_NO_PARENT_FORM  = 'noParentForm';

    public const PARAM_IS_ALLOWED_FOR_CUSTOMER = 'isAllowedForCustomer';

    public const PARAM_DEPENDENCY = 'dependency';

    public const PARAM_EDIT_ON_CLICK = 'editOnClick';

    /**
     * Available field types
     */
    public const FIELD_TYPE_LABEL      = 'label';
    public const FIELD_TYPE_TEXT       = 'text';
    public const FIELD_TYPE_PASSWORD   = 'password';
    public const FIELD_TYPE_SELECT     = 'select';
    public const FIELD_TYPE_CHECKBOX   = 'checkbox';
    public const FIELD_TYPE_RADIO      = 'radio';
    public const FIELD_TYPE_TEXTAREA   = 'textarea';
    public const FIELD_TYPE_SEPARATOR  = 'separator';
    public const FIELD_TYPE_ITEMS_LIST = 'itemsList';
    public const FIELD_TYPE_HIDDEN     = 'hidden';
    public const FIELD_TYPE_LISTBOX    = 'listbox';
    public const FIELD_TYPE_FILE       = 'file';
    public const FIELD_TYPE_COMPLEX    = 'complex';

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * validityFlag
     *
     * @var boolean
     */
    protected $validityFlag;

    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = true;

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * Name-to-ID translation table
     *
     * @var array
     */
    protected $nameTranslation = [
        '[' => '-',
        ']' => '',
        '_' => '-',
    ];

    /**
     * Return field type
     *
     * @return string
     */
    abstract public function getFieldType();

    /**
     * Return field template
     *
     * @return string
     */
    abstract protected function getFieldTemplate();

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->getParam(self::PARAM_EDIT_ON_CLICK)) {
            $list[] = 'form_field/edit_on_click/controller.js';
        }

        return $list;
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getParam(self::PARAM_NAME);
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getParam(self::PARAM_VALUE);
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->getWidgetParams(self::PARAM_VALUE)->setValue($value);
    }

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getParam(self::PARAM_LABEL);
    }

    /**
     * getLabel
     *
     * @return array
     */
    public function getLabelParams()
    {
        return $this->getParam(self::PARAM_LABEL_PARAMS);
    }

    /**
     * Get formatted label
     *
     * @return string
     */
    public function getFormattedLabel()
    {
        return static::t($this->getLabel(), $this->getLabelParams());
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        return $this->getParam(self::PARAM_ID) ?: strtolower(strtr($this->getName(), $this->nameTranslation));
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return $this->getParam(static::PARAM_TRUSTED) ?: false;
    }

    /**
     * Validate field value
     *
     * @return mixed
     */
    public function validate()
    {
        $this->setValue($this->sanitize());

        return [
            $this->getValidityFlag(),
            $this->getValidityFlag() ? null : $this->errorMessage
        ];
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/form_field.css';

        return $list;
    }

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (isset($params[self::PARAM_NAME])) {
            $this->name = $params[self::PARAM_NAME];
        }

        parent::__construct($params);
    }

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return $this->getParam(self::PARAM_WRAPPER_CLASS);
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        // todo: Check if NULL is mining field value so we must use array_key_exists instead of isset
        if (isset($params['value'])) {
            $this->setValue($params['value']);
        }
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return $value;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/form_field.twig';
    }

    /**
     * @return string
     */
    protected function getFieldLabelTemplate()
    {
        return 'form_field/form_field_label.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'form_field';
    }

    /**
     * checkSavedValue
     *
     * @return boolean
     */
    protected function checkSavedValue()
    {
        return $this->callFormMethod('getSavedData', [$this->getName()]) !== null;
    }

    /**
     * Get validity flag (and run field validation procedure)
     *
     * @return boolean
     */
    protected function getValidityFlag()
    {
        if ($this->validityFlag === null) {
            $this->validityFlag = $this->checkFieldValidity();
        }

        return $this->validityFlag;
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return $this->getValidityFlag() ? null : $this->errorMessage;
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return $this->getValue();
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        return [
            'id'   => $this->getFieldId(),
            'name' => $this->getName(),
        ];
    }

    /**
     * setCommonAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        foreach ($this->getCommonAttributes() as $name => $value) {
            if (!isset($attrs[$name])) {
                $attrs[$name] = $value;
            }
        }

        if (!isset($attrs['class'])) {
            $attrs['class'] = '';
        }
        $classes = preg_grep('/.+/S', array_map('trim', explode(' ', $attrs['class'])));
        $classes = $this->assembleClasses($classes);
        $attrs['class'] = implode(' ', $classes);

        return $attrs;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $validationRules = $this->assembleValidationRules();
        if ($validationRules) {
            $classes[] = 'validate[' . implode(',', $validationRules) . ']';
        }

        $classes[] = $this->isFormControl() ? 'form-control' : '';

        return $classes;
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return $this->getParam(self::PARAM_FORM_CONTROL);
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        return $this->isRequired() ? ['required'] : [];
    }

    /**
     * prepareAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        if (!$this->getValidityFlag() && $this->checkSavedValue()) {
            $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . 'form_field_error';
        }

        return $this->setCommonAttributes($attrs);
    }

    /**
     * Check if field is required
     *
     * @return boolean
     */
    protected function isRequired()
    {
        return $this->getParam(self::PARAM_REQUIRED);
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return $this->prepareAttributes($this->getParam(self::PARAM_ATTRIBUTES));
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getAttributesCode()
    {
        return ' ' . static::convertToHtmlAttributeString($this->getAttributes());
    }

    /**
     * Some JavaScript code to insert
     *
     * @todo   Remove it. Use getFormFieldJSData method instead.
     * @return string
     */
    protected function getInlineJSCode()
    {
        return null;
    }

    /**
     * getDefaultName
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return null;
    }

    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return $this->name !== null ? $this->callFormMethod('getDefaultFieldValue', [$this->name]) : null;
    }

    /**
     * Validate field on form side
     *
     * @return array
     */
    protected function validateFormField()
    {
        $isValid = true;
        $errorMessage = null;

        if ($this->name !== null) {
            $result = $this->callFormMethod('validateFormField', [$this]);
            if (is_array($result)) {
                [$isValid, $errorMessage] = $result;
            }
        }

        return [$isValid, $errorMessage];
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return null;
    }

    /**
     * getDefaultLabelParams
     *
     * @return array
     */
    protected function getDefaultLabelParams()
    {
        return [];
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return [];
    }

    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return false;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ID             => new \XLite\Model\WidgetParam\TypeString('Id', ''),
            self::PARAM_NAME           => new \XLite\Model\WidgetParam\TypeString('Name', $this->getDefaultName()),
            self::PARAM_VALUE          => new \XLite\Model\WidgetParam\TypeString('Value', $this->getDefaultValue()),
            self::PARAM_LABEL          => new \XLite\Model\WidgetParam\TypeString('Label', $this->getDefaultLabel()),
            self::PARAM_LABEL_PARAMS   => new \XLite\Model\WidgetParam\TypeCollection('Label params', $this->getDefaultLabelParams()),
            self::PARAM_REQUIRED       => new \XLite\Model\WidgetParam\TypeBool('Required', false),
            self::PARAM_COMMENT        => new \XLite\Model\WidgetParam\TypeString('Comment', null),
            self::PARAM_HELP           => new \XLite\Model\WidgetParam\TypeString('Help', null),
            self::PARAM_HELP_WIDGET    => new \XLite\Model\WidgetParam\TypeString('Help widget class name', null),
            self::PARAM_LABEL_HELP     => new \XLite\Model\WidgetParam\TypeString('Label help', null),
            self::PARAM_LABEL_HELP_WIDGET => new \XLite\Model\WidgetParam\TypeString('Label help widget class name', null),
            self::PARAM_ATTRIBUTES => new \XLite\Model\WidgetParam\TypeCollection('Attributes', $this->getDefaultAttributes()),
            self::PARAM_WRAPPER_CLASS => new \XLite\Model\WidgetParam\TypeString('Wrapper class', $this->getDefaultWrapperClass()),
            self::PARAM_FORM_CONTROL   => new \XLite\Model\WidgetParam\TypeBool('Is form control', true),

            self::PARAM_LINK_HREF     => new \XLite\Model\WidgetParam\TypeString('Link href', ''),
            self::PARAM_LINK_TEXT     => new \XLite\Model\WidgetParam\TypeString('Link text', ''),
            self::PARAM_LINK_IMG      => new \XLite\Model\WidgetParam\TypeString('Link img', ''),
            self::PARAM_NO_PARENT_FORM => new \XLite\Model\WidgetParam\TypeBool('Form field has no parent form', false),

            self::PARAM_IS_ALLOWED_FOR_CUSTOMER => new \XLite\Model\WidgetParam\TypeBool(
                'Is allowed for customer',
                $this->isAllowedForCustomer
            ),
            self::PARAM_FIELD_ONLY              => new \XLite\Model\WidgetParam\TypeBool(
                'Skip wrapping with label and required flag, display just a field itself',
                $this->getDefaultParamFieldOnly()
            ),
            self::PARAM_DEPENDENCY              => new \XLite\Model\WidgetParam\TypeCollection('Dependency', []),
            self::PARAM_TRUSTED                 => new \XLite\Model\WidgetParam\TypeBool('Trusted (value may contain anything)', false),
            self::PARAM_EDIT_ON_CLICK           => new \XLite\Model\WidgetParam\TypeBool('Edit on click', false),
        ];
    }

    /**
     * Check field value validity
     *
     * @return boolean
     */
    protected function checkFieldValue()
    {
        return $this->getValue() !== '';
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $this->errorMessage = null;

        if ($this->isRequired() && !$this->checkFieldValue()) {
            $this->errorMessage = $this->getRequiredFieldErrorMessage();
            $result = false;
        } else {
            [$result, $this->errorMessage] = $this->validateFormField();
        }

        return $result;
    }

    /**
     * Get required field error message
     *
     * @return string
     */
    protected function getRequiredFieldErrorMessage()
    {
        return \XLite\Core\Translation::lbl('The X field is empty', ['name' => $this->getLabel()]);
    }

    /**
     * checkFieldAccessability
     *
     * @return boolean
     */
    protected function checkFieldAccessability()
    {
        return $this->getParam(self::PARAM_IS_ALLOWED_FOR_CUSTOMER) || \XLite::isAdminZone();
    }

    /**
     * callFormMethod
     *
     * @param string $method Class method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    protected function callFormMethod($method, array $args = [])
    {
        $result = null;

        if (!$this->getParam(static::PARAM_NO_PARENT_FORM)) {
            $form = \XLite\View\Model\AModel::getCurrentForm();

            $result = $form && method_exists($form, $method)
                ? call_user_func_array([$form, $method], $args)
                : null;
        }

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->checkFieldAccessability();
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        $suffix = preg_replace(
            [
                '/^(?:\\\\)?XLite\\\\View\\\\FormField\\\\(.+)$/Ss',
                '/^(?:\\\\)?(?:[a-zA-Z0-9]+\\\\[a-zA-Z0-9]+\\\\)View\\\\FormField\\\\(.+)$/Ss'
            ],
            '$1',
            static::class
        );
        $suffix = str_replace('\\', '-', strtolower($suffix));

        return 'input ' . $suffix;
    }

    /**
     * Get label container class
     *
     * @return string
     */
    protected function getLabelContainerClass()
    {
        $class = 'table-label ' . $this->getFieldId() . '-label';

        if ($this->isRequired()) {
            $class .= ' table-label-required';
        }

        return $class;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = 'table-value ' . $this->getFieldId() . '-value';

        if ($this->isRequired()) {
            $class .= ' table-value-required';
        }

        if ($this->hasDifferentSavedValue()) {
            $class .= ' has-diff-saved-value';
        }

        return $class;
    }

    /**
     * @return bool
     */
    protected function hasDifferentSavedValue()
    {
        return $this->callFormMethod('hasDifferentSavedValue', [$this->getName()]);
    }

    /**
     * @return \XLite\Core\Translation|Label
     */
    protected function getSavedErrorMessage()
    {
        return $this->callFormMethod('getSavedErrorMessage', [$this->getName()]);
    }

    /**
     * @return boolean
     */
    protected function showErrorsViaTopMessage()
    {
        return $this->callFormMethod('showErrorsViaTopMessage');
    }

    /**
     * Return some data for JS external scripts if it is needed.
     *
     * @return array
     */
    protected function getFormFieldJSData()
    {
        return null;
    }

    /**
     * Check for label help present
     *
     * @return boolean
     */
    protected function hasLabelHelp()
    {
        return $this->getParam(static::PARAM_LABEL_HELP) || $this->getParam(static::PARAM_LABEL_HELP_WIDGET);
    }

    /**
     * Check for help present
     *
     * @return boolean
     */
    protected function hasHelp()
    {
        return $this->getParam(static::PARAM_HELP) || $this->getParam(static::PARAM_HELP_WIDGET);
    }

    // {{{ Edit on click

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return [
            'class'          => $this->getContainerClass(),
            'data-is-escape' => $this->isEscapeValue() ? '1' : '',
        ];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'edit-on-click-field editable has-view';
    }

    /**
     * Check - escape value or not
     *
     * @return boolean
     */
    protected function isEscapeValue()
    {
        return true;
    }

    /**
     * Get view container attributes
     *
     * @return array
     */
    protected function getViewContainerAttributes()
    {
        return [
            'class' => [
                'view',
                'editable',
            ],
        ];
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/edit_on_click/view.twig';
    }

    /**
     * Get view value
     *
     * @return mixed
     */
    protected function getViewValue()
    {
        return $this->getValue();
    }

    /**
     * Get field container attributes
     *
     * @return array
     */
    protected function getFieldContainerAttributes()
    {
        return [
            'class' => ['field'],
        ];
    }

    // }}}
}
