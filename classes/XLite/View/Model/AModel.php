<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Abstract model widget
 */
abstract class AModel extends \XLite\View\Dialog
{
    /**
     * Widget param names
     */
    public const PARAM_MODEL_OBJECT      = 'modelObject';
    public const PARAM_USE_BODY_TEMPLATE = 'useBodyTemplate';

    /**
     * Indexes in field schemas
     *
     * FIXME: keep this list synchronized with the classes,
     * derived from the \XLite\View\FormField\AFormField
     */
    public const SCHEMA_CLASS           = 'class';
    public const SCHEMA_VALUE           = \XLite\View\FormField\AFormField::PARAM_VALUE;
    public const SCHEMA_REQUIRED        = \XLite\View\FormField\AFormField::PARAM_REQUIRED;
    public const SCHEMA_ATTRIBUTES      = \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES;
    public const SCHEMA_NAME            = \XLite\View\FormField\AFormField::PARAM_NAME;
    public const SCHEMA_LABEL           = \XLite\View\FormField\AFormField::PARAM_LABEL;
    public const SCHEMA_LABEL_PARAMS    = \XLite\View\FormField\AFormField::PARAM_LABEL_PARAMS;
    public const SCHEMA_FIELD_ONLY      = \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY;
    public const SCHEMA_PLACEHOLDER     = \XLite\View\FormField\Input\Base\StringInput::PARAM_PLACEHOLDER;
    public const SCHEMA_COMMENT         = \XLite\View\FormField\AFormField::PARAM_COMMENT;
    public const SCHEMA_HELP            = \XLite\View\FormField\AFormField::PARAM_HELP;
    public const SCHEMA_LINK_HREF       = \XLite\View\FormField\AFormField::PARAM_LINK_HREF;
    public const SCHEMA_LINK_TEXT       = \XLite\View\FormField\AFormField::PARAM_LINK_TEXT;
    public const SCHEMA_LINK_IMG        = \XLite\View\FormField\AFormField::PARAM_LINK_IMG;
    public const SCHEMA_TRUSTED         = \XLite\View\FormField\AFormField::PARAM_TRUSTED;
    public const SCHEMA_TRUSTED_PERMISSION = 'trustedPermission';

    public const SCHEMA_OPTIONS = \XLite\View\FormField\Select\ASelect::PARAM_OPTIONS;
    public const SCHEMA_IS_CHECKED = \XLite\View\FormField\Input\Checkbox::PARAM_IS_CHECKED;

    public const SCHEMA_MODEL_ATTRIBUTES = 'model_attributes';

    public const SCHEMA_DEPENDENCY = \XLite\View\FormField\AFormField::PARAM_DEPENDENCY;

    /**
     * Session cell to store form data
     */
    public const SAVED_FORMS       = 'savedForms';
    public const SAVED_FORM_DATA   = 'savedFormData';
    public const SAVED_FORM_ERRORS = 'savedFormErrors';

    /**
     * Form sections
     */
    // Title for this section will not be displayed
    public const SECTION_DEFAULT = 'default';
    // This section will not be displayed
    public const SECTION_HIDDEN  = 'hidden';

    /**
     * Indexes in the "formFields" array
     */
    public const SECTION_PARAM_WIDGET = 'sectionParamWidget';
    public const SECTION_PARAM_FIELDS = 'sectionParamFields';

    /**
     * Name prefix of the methods to handle actions
     */
    public const ACTION_HANDLER_PREFIX = 'performAction';

    /**
     * Dependency
     */
    public const DEPENDENCY_SHOW = 'show';
    public const DEPENDENCY_HIDE = 'hide';

    /**
     * Current form object
     *
     * @var \XLite\View\Model\AModel
     */
    protected static $currentForm = null;

    /**
     * List of form fields
     *
     * @var array
     */
    protected $formFields = null;

    /**
     * List of files form fields
     *
     * @var array
     */
    protected $filesFormFields;

    /**
     * Names of the form fields (hash)
     *
     * @var array
     */
    protected $formFieldNames = [];

    /**
     * Form error messages cache
     *
     * @var array
     */
    protected $errorMessages = null;

    /**
     * Form saved data cache
     *
     * @var array
     */
    protected $savedData = null;

    /**
     * Form saved errors
     *
     * @var array
     */
    protected $savedErrorMessages = null;

    /**
     * Available form sections
     *
     * @var array
     */
    protected $sections = [
        self::SECTION_DEFAULT => null,
        self::SECTION_HIDDEN  => null,
    ];

    /**
     * Current action
     *
     * @var string
     */
    protected $currentAction = null;

    /**
     * Data from request
     *
     * @var array
     */
    protected $requestData = null;

    /**
     * schemaDefault
     *
     * @var array
     */
    protected $schemaDefault = [];

    /**
     * schemaHidden
     *
     * @var array
     */
    protected $schemaHidden = [];

    /**
     * The list of fields (field names) that must be excluded from the array(data) for mapping to the object
     *
     * @var array
     */
    protected $excludedFields = [];

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function getDefaultModelObject();

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    abstract protected function getFormClass();

    /**
     * Return the array of form widget parameters
     *
     * @return array
     */
    protected function getFormWidgetParams()
    {
        return [];
    }

    /**
     * Get instance to the current form object
     *
     * @return \XLite\View\Model\AModel
     */
    public static function getCurrentForm()
    {
        return self::$currentForm;
    }

    /**
     * Save current form reference and sections list, and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [], array $sections = [])
    {
        if (!empty($sections)) {
            $this->sections = \Includes\Utils\ArrayManager::filterByKeys($this->sections, $sections);
        }

        parent::__construct($params);

        $this->startCurrentForm();
    }

    /**
     * Retrieve property from the request or from  model object
     *
     * @param string $name Field/property name
     *
     * @return mixed
     */
    public function getDefaultFieldValue($name)
    {
        $value = $this->getSavedData($name);

        if (!isset($value)) {
            $value = $this->getRequestData($name);

            if (!isset($value)) {
                // Check if $name is in fields list
                $fields = $this->getFormFields(true);
                if ($fields && in_array($name, $fields)) {
                    $value = $this->getModelObjectValue($name);
                }
            }
        }

        return $value;
    }

    /**
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        return !((bool) $this->getErrorMessages());
    }

    /**
     * Perform some action for the model object
     *
     * @param string $action Action to perform
     * @param array  $data   Form data OPTIONAL
     *
     * @return boolean
     */
    public function performAction($action, array $data = [])
    {
        // Save some data
        $this->currentAction = $action;
        $this->defineRequestData($data);

        $requestData = $this->prepareDataForMapping();

        // Map model object with the request data
        $this->setModelProperties($requestData);

        // Do not call "callActionHandler()" method if model object is not valid
        $result = $this->isValid() && $this->callActionHandler();

        if ($result) {
            $this->postprocessSuccessAction();
        } else {
            $this->rollbackModel();
            $this->saveFormData($requestData);
            $this->postprocessErrorAction();
        }

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/model.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/model.js';

        return $list;
    }

    /**
     * Return fields' saved values for current form (saved data itself)
     *
     * @param string $name Parameter name OPTIONAL
     *
     * @return array
     */
    public function getSavedData($name = null)
    {
        if (!isset($this->savedData)) {
            $this->savedData = $this->getSavedForm(self::SAVED_FORM_DATA);
        }

        return isset($name)
            ? ($this->savedData[$name] ?? null)
            : $this->savedData;
    }

    /**
     * getRequestData
     *
     * @param string $name Index in the request data OPTIONAL
     *
     * @return mixed
     */
    public function getRequestData($name = null)
    {
        if (!isset($this->requestData)) {
            $this->defineRequestData([], $name);
        }

        return isset($name)
            ? ($this->requestData[$name] ?? null)
            : $this->requestData;
    }

    /**
     * setRequestData
     *
     * @param string $name  Index in the request data
     * @param mixed  $value Value to set
     *
     * @return void
     */
    public function setRequestData($name, $value)
    {
        $this->requestData[$name] = $value;
    }

    /**
     * Return model object to use
     *
     * @return \XLite\Model\AEntity
     */
    public function getModelObject()
    {
        return $this->getParam(self::PARAM_MODEL_OBJECT);
    }


    /**
     * Check if current form is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return true;
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return $this->checkAccess() ? parent::getBodyTemplate() : 'access_denied.twig';
    }

    /**
     * getAccessDeniedMessage
     *
     * @return string
     */
    protected function getAccessDeniedMessage()
    {
        return 'Access denied';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'model';
    }

    /**
     * Return text for header
     *
     * @return string
     */
    protected function getHeaderText()
    {
        return null;
    }

    /**
     * getFormDir
     *
     * @param string $template Template file basename OPTIONAL
     *
     * @return string
     */
    protected function getFormDir($template = null)
    {
        return 'form';
    }

    /**
     * Return form templates directory name
     *
     * @param string $template Template file base name
     *
     * @return string
     */
    protected function getFormTemplate($template)
    {
        return $this->getFormDir($template) . '/' . $template . '.twig';
    }

    /**
     * Return list of form fields for certain section
     *
     * @param string $section Section name
     *
     * @return array
     */
    protected function getFormFieldsForSection($section)
    {
        $method = __FUNCTION__ . \Includes\Utils\Converter::convertToUpperCamelCase($section);

        // Return the method getFormFieldsForSection<SectionName>
        return method_exists($this, $method) ? $this->$method() : $this->translateSchema($section);
    }

    /**
     * Define form field classes and values
     *
     * @return void
     */
    protected function defineFormFields()
    {
        $this->formFields = [];

        foreach ($this->sections as $section => $label) {
            $this->formFields[$section] = [
                self::SECTION_PARAM_WIDGET => $this->defineSectionWidget($section, [self::SCHEMA_LABEL => $label]),
                self::SECTION_PARAM_FIELDS => $this->getFormFieldsForSection($section),
            ];
        }
    }

    /**
     * @param string $section
     * @param array  $params
     *
     * @return \XLite\View\AView
     */
    protected function defineSectionWidget($section, $params)
    {
        return new \XLite\View\FormField\Separator\Regular($params);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $object = $this->getDefaultModelObject();

        $this->widgetParams += [
            self::PARAM_MODEL_OBJECT => new \XLite\Model\WidgetParam\TypeObject(
                'Object',
                $object,
                false,
                $object ? get_class($object) : ''
            ),
            self::PARAM_USE_BODY_TEMPLATE => new \XLite\Model\WidgetParam\TypeBool(
                'Use default body template',
                false
            ),
        ];
    }

    /**
     * useBodyTemplate
     *
     * @return boolean
     */
    protected function useBodyTemplate()
    {
        return $this->getParam(self::PARAM_USE_BODY_TEMPLATE) ? true : parent::useBodyTemplate();
    }

    /**
     * Flag if the panel widget for buttons is used
     *
     * @return boolean
     */
    protected function useButtonPanel()
    {
        return $this->getButtonPanelClass() !== null;
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return \XLite::isAdminZone()
            ? \XLite\View\StickyPanel\Model\Model::class
            : null;
    }

    /**
     * Get button panel
     *
     * @return \XLite\View\StickyPanel\Model\AModel
     */
    protected function getButtonPanel()
    {
        $buttonPanel = null;

        if ($this->useButtonPanel()) {
            $class = $this->getButtonPanelClass();
            $buttonPanel = new $class();
            $buttons = $this->getFormButtons();

            if (
                $buttons
                && method_exists($buttonPanel, 'setButtons')
            ) {
                $buttonPanel->setButtons($buttons);
            }
        }

        return $buttonPanel;
    }

    /**
     * Add (if required) an additional part to the form name
     *
     * @param string $name Name to prepare
     *
     * @return string
     */
    protected function composeFieldName($name)
    {
        return $name;
    }

    /**
     * Return model field name for a provided form field name
     *
     * @param string $name Name of form field
     *
     * @return string
     */
    protected function getModelFieldName($name)
    {
        return $name;
    }

    /**
     * Return field mappings structure for the model
     *
     * @return array
     */
    protected function getFieldMappings()
    {
        if (!isset($this->fieldMappings)) {
            // Collect metadata for fields of class and its translation class if there is one.
            $metaData = \XLite\Core\Database::getEM()->getClassMetadata(get_class($this->getModelObject()));
            $this->fieldMappings = $metaData->fieldMappings;

            $metaDataTranslationClass = isset($metaData->associationMappings['translations'])
                ? $metaData->associationMappings['translations']['targetEntity']
                : false;

            if ($metaDataTranslationClass) {
                $metaDataTranslation = \XLite\Core\Database::getEM()->getClassMetadata($metaDataTranslationClass);
                $this->fieldMappings += $metaDataTranslation->fieldMappings;
            }
        }

        return $this->fieldMappings;
    }

    /**
     * Return field mapping info for a given $name key
     *
     * @param string $name Field name
     *
     * @return array
     */
    protected function getFieldMapping($name)
    {
        $fieldMappings = $this->getFieldMappings();
        $fieldName = $this->getModelFieldName($name);

        return $fieldMappings[$fieldName] ?? null;
    }

    /**
     * Return widget attributes that are collected from the model properties
     *
     * @param string $name Field name
     * @param array  $data Field info
     *
     * @return array
     */
    protected function getModelAttributes($name, array $data)
    {
        $fieldMapping = $this->getFieldMapping($name);

        $result = [];

        if ($fieldMapping) {
            foreach ($data[static::SCHEMA_MODEL_ATTRIBUTES] as $widgetAttribute => $modelAttribute) {
                if (isset($fieldMapping[$modelAttribute])) {
                    $result[$widgetAttribute] = $fieldMapping[$modelAttribute];
                }
            }
        }

        return $result;
    }

    /**
     * Perform some operations when creating fields list by schema
     *
     * @param string $name Node name
     * @param array  $data Field description
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        if (!isset($data[static::SCHEMA_NAME])) {
            $data[static::SCHEMA_NAME] = $this->composeFieldName($name);
        }

        $data[static::SCHEMA_VALUE] = $this->getDefaultFieldValue($name);

        $data[static::SCHEMA_ATTRIBUTES] = !empty($data[static::SCHEMA_ATTRIBUTES]) ? $data[static::SCHEMA_ATTRIBUTES] : [];
        $data[static::SCHEMA_ATTRIBUTES] += isset($data[static::SCHEMA_MODEL_ATTRIBUTES]) ? $this->getModelAttributes($name, $data) : [];

        $data[static::SCHEMA_DEPENDENCY] = $data[static::SCHEMA_DEPENDENCY] ?? [];

        if (isset($data[static::SCHEMA_TRUSTED_PERMISSION]) && $data[static::SCHEMA_TRUSTED_PERMISSION]) {
            $data[static::SCHEMA_TRUSTED] = $this->isContentTrustedByPermission($name, $data);
        }

        return $data;
    }

    /**
     * Return list of files form fields
     *
     * @return array
     */
    protected function getFilesFormFields()
    {
        if (!isset($this->filesFormFields)) {
            $this->filesFormFields = [];
            foreach ($this->formFields as $section) {
                foreach ($section[static::SECTION_PARAM_FIELDS] as $k => $v) {
                    if (is_subclass_of($v, \XLite\View\FormField\FileUploader\AFileUploader::class)) {
                        $this->filesFormFields[$k] = $v;
                    }
                }
            }
        }

        return $this->filesFormFields;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $filesData = [];
        if ($this->getFilesFormFields()) {
            foreach ($this->getFilesFormFields() as $k => $v) {
                if (isset($data[$k])) {
                    $filesData[$k] = $data[$k];
                    unset($data[$k]);
                }
            }
        }

        $data = $this->mapCleanURL($data);

        $model = $this->prepareObjectForMapping();

        foreach ($data as $name => $value) {
            // Correct data: remove fields which cannot be mapped to the model
            $method = 'set' . \Includes\Utils\Converter::convertToUpperCamelCase($name);

            $methodExists = method_exists($model, $method);

            // $method - assemble from 'set' + property name
            if (!$methodExists && !$model->isPropertyExists($name)) {
                unset($data[$name]);
            }
        }

        $model->map($data);

        if ($filesData) {
            $errors = $this->processFiles($filesData);

            if ($errors) {
                $this->processFileUploadErrors($errors);
            }
        }
    }

    /**
     * Process file upload errors.
     * $errors has format: array( array(<message>,<message params>), ... )
     *
     * @param array $errors Array of errors
     *
     * @return void
     */
    protected function processFileUploadErrors($errors)
    {
        foreach ($errors as $error) {
            \XLite\Core\TopMessage::addError(static::t($error[0], !empty($error[1]) ? $error[1] : []));
        }
    }

    /**
     * Process files
     *
     * @param array $data Data to save
     *
     * @return array
     */
    protected function processFiles(array $data)
    {
        $errors = [];

        $model = $this->getModelObject();

        foreach ($data as $field => $d) {
            $errors = array_merge($errors, $model->processFiles($field, $d));
        }

        return $errors;
    }

    /**
     * Process clean url data
     *
     * @param array $data
     *
     * @return array
     */
    protected function mapCleanURL($data)
    {
        /** @var \XLite\Model\Repo\CleanURL $cleanURLRepo */
        $cleanURLRepo = \XLite\Core\Database::getRepo(\XLite\Model\CleanURL::class);

        if (
            $this->getPostedData('autogenerateCleanURL')
            || (
                isset($data['cleanURL'])
                && empty($data['cleanURL'])
            )
        ) {
            $data['cleanURL'] = $cleanURLRepo->generateCleanURL(
                $this->getDefaultModelObject(),
                $data[$cleanURLRepo->getBaseFieldName($this->getDefaultModelObject())]
            );
        }

        if ($this->getPostedData('forceCleanURL')) {
            $conflictEntity = $cleanURLRepo->getConflict(
                $data['cleanURL'],
                $this->getDefaultModelObject(),
                $this->getModelId()
            );

            if ($conflictEntity && $data['cleanURL'] !== $conflictEntity->getCleanURL()) {
                /** @var \Doctrine\Common\Collections\Collection $cleanURLs */
                $cleanURLs = $conflictEntity->getCleanURLs();
                /** @var \XLite\Model\CleanURL $cleanURL */
                foreach ($cleanURLs as $cleanURL) {
                    if ($data['cleanURL'] === $cleanURL->getCleanURL()) {
                        $cleanURLs->removeElement($cleanURL);
                        \XLite\Core\Database::getEM()->remove($cleanURL);
                        $this->getModelObject()->setCleanURL($data['cleanURL'], true);
                        unset($data['cleanURL']);

                        break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Return true if form errors should be shown via top message
     *
     * @return boolean
     */
    public function showErrorsViaTopMessage()
    {
        return true;
    }

    /**
     * Fetch saved forms data from session
     *
     * @return array
     */
    protected function getSavedForms()
    {
        return \XLite\Core\Session::getInstance()->get(self::SAVED_FORMS);
    }

    /**
     * Return saved data for current form (all or certain field(s))
     *
     * @param string $field Data field to return OPTIONAL
     *
     * @return array
     */
    protected function getSavedForm($field = null)
    {
        $data = $this->getSavedForms();
        $name = $this->getFormName();

        $data = $data[$name] ?? [];

        if (isset($field) && isset($data[$field])) {
            $data = $data[$field];
        }

        return $data;
    }

    /**
     * Save form fields in session
     *
     * @param mixed $data Data to save
     *
     * @return void
     */
    protected function saveFormData($data)
    {
        $savedData = $this->getSavedForms();
        $formName = $this->getFormName();

        if (isset($data)) {
            $savedData[$formName] = [
                self::SAVED_FORM_DATA   => $data,
                self::SAVED_FORM_ERRORS => $this->getErrorMessages(),
            ];
        } else {
            $savedData[$formName] = [];
        }

        \XLite\Core\Session::getInstance()->set(
            self::SAVED_FORMS,
            $savedData ?: null
        );
    }

    /**
     * Clear form fields in session
     *
     * @return void
     */
    protected function clearFormData()
    {
        $this->saveFormData(null);
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataDeletedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('Data have been deleted successfully');
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionCreate()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionUpdate()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionModify()
    {
        $this->addDataSavedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessActionDelete()
    {
        $this->addDataDeletedTopMessage();
    }

    /**
     * Perform some actions on success
     *
     * @return void
     */
    protected function postprocessSuccessAction()
    {
        $method = __FUNCTION__ . \Includes\Utils\Converter::convertToUpperCamelCase($this->currentAction);

        if (method_exists($this, $method)) {
            // Run the corresponded function
            $this->$method();
        }

        $this->setActionSuccess();
    }

    /**
     * Perform some actions on error
     *
     * @return void
     */
    protected function postprocessErrorAction()
    {
        if ($this->showErrorsViaTopMessage()) {
            \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);
        }

        $method = __FUNCTION__ . \Includes\Utils\Converter::convertToUpperCamelCase($this->currentAction);

        if (method_exists($this, $method)) {
            // Run corresponded function
            $this->$method();
        }

        $this->setActionError();
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $em = \XLite\Core\Database::getEM();
        $model = $this->getModelObject();
        if (is_object($model) && $em->contains($model)) {
            $em->refresh($model);
        }
    }

    /**
     * Save reference to the current form
     *
     * @return void
     */
    protected function startCurrentForm()
    {
        self::$currentForm = $this;
    }

    /**
     * @inheritDoc
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        $this->clearFormData();
    }

    /**
     * getFieldBySchema
     * TODO - should use the Factory class
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        $result = null;

        $class = $data[self::SCHEMA_CLASS];

        if (class_exists($class)) {
            $method = 'prepareFieldParams' . \Includes\Utils\Converter::convertToUpperCamelCase($name);

            if (method_exists($this, $method)) {
                // Call the corresponded method
                $data = $this->$method($data);
            }

            $result = new $class($this->getFieldSchemaArgs($name, $data));
        }

        return $result;
    }

    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        $result = [];

        foreach ($schema as $name => $data) {
            $field = $this->getFieldBySchema($name, $data);
            if ($field) {
                $result[$name] = $field;
            }
        }

        return $result;
    }

    /**
     * Remove empty sections
     *
     * @return void
     */
    protected function filterFormFields()
    {
        // First dimension - sections list
        foreach ($this->formFields as $section => &$data) {
            // Second dimension - fields
            foreach ($data[self::SECTION_PARAM_FIELDS] as $index => $field) {
                if (!$field->checkVisibility()) {
                    // Exclude field from list if it's not visible
                    unset($data[self::SECTION_PARAM_FIELDS][$index]);
                } else {
                    // Else include this field into the list of available fields
                    $this->formFieldNames[] = $field->getName();
                }
            }

            // Remove whole section if it's empty
            if (empty($data[self::SECTION_PARAM_FIELDS])) {
                unset($this->formFields[$section]);
            }
        }
    }

    /**
     * Wrapper for the "getFieldsBySchema()" method
     *
     * @param string $name Schema short name
     *
     * @return array
     */
    protected function translateSchema($name)
    {
        $schema = 'schema' . ucfirst($name);

        return property_exists($this, $schema) ? $this->getFieldsBySchema($this->$schema) : [];
    }

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
            $this->defineFormFields();
            $this->filterFormFields();
        }

        return $onlyNames ? $this->formFieldNames : $this->formFields;
    }

    /**
     * Return certain form field
     *
     * @param string  $section        Section where the field located
     * @param string  $name           Field name
     * @param boolean $preprocessName Flag; prepare field name or not OPTIONAL
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFormField($section, $name, $preprocessName = true)
    {
        $result = null;
        $fields = $this->getFormFields();

        if ($preprocessName) {
            $name = $this->composeFieldName($name);
        }

        if (isset($fields[$section][self::SECTION_PARAM_FIELDS][$name])) {
            $result = $fields[$section][self::SECTION_PARAM_FIELDS][$name];
        }

        return $result;
    }

    /**
     * Return list of form fields to display
     *
     * @return array
     */
    protected function getFormFieldsForDisplay()
    {
        $result = $this->getFormFields();
        unset($result[self::SECTION_HIDDEN]);

        return $result;
    }

    /**
     * Display section header or not
     *
     * @param string $section Name of section to check
     *
     * @return boolean
     */
    protected function isShowSectionHeader($section)
    {
        return !in_array($section, [self::SECTION_DEFAULT, self::SECTION_HIDDEN]);
    }

    /**
     * prepareRequestData
     *
     * @param array $data Request data
     *
     * @return array
     */
    protected function prepareRequestData(array $data)
    {
        return $data;
    }

    /**
     * Prepare and save passed data
     *
     * @param array       $data Passed data OPTIONAL
     * @param string|null $name Index in request data array (optional) OPTIONAL
     *
     * @return void
     */
    protected function defineRequestData(array $data = [], $name = null)
    {
        if (empty($data)) {
            $data = $this->prepareRequestParamsList();
        }
        // FIXME: check if there is the way to avoid this
        $this->formFields = null;

        // TODO: check if there is more convenient way to do this
        $this->requestData = $this->prepareRequestData($data);
        $this->requestData = \Includes\Utils\ArrayManager::filterByKeys(
            $this->requestData,
            $this->getFormFields(true)
        );

        $this->requestData = $this->prepareRequestDataByFormFields($this->requestData);
    }

    /**
     * Prepare request data by form fields (typecasting)
     *
     * @param array $requestData Request data
     *
     * @return array
     */
    protected function prepareRequestDataByFormFields($requestData)
    {
        $schemas = $this->getAllSchemaCells();
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        foreach ($requestData as $name => $value) {
            $formField = $this->getFormFieldsByName($name);

            if (
                isset($formField)
                && is_object($formField)
                && method_exists($formField, 'prepareRequestData')
            ) {
                if ($formField->isTrusted() || !empty($schemas[$name][static::SCHEMA_TRUSTED])) {
                    // Formfield value is trusted
                    $value = $nonFilteredData[$name];
                }

                // prepare request data (typecasting)
                $requestData[$name] = $formField->prepareRequestData($value);
            }
        }

        return $requestData;
    }

    /**
     * Get all schemas data
     *
     * @return array
     */
    protected function getAllSchemaCells()
    {
        $result = [];

        if (method_exists($this, 'getSchemaFields')) {
            // Some classes define schema fields by method getSchemaFields()
            $result = $this->getSchemaFields();
        } else {
            // Get schema fields from properties schemaSectionName if defined
            foreach ($this->sections as $section => $label) {
                $schema = 'schema' . ucfirst($section);

                if (isset($this->$schema) && is_array($this->$schema)) {
                    $result = array_merge($result, $this->$schema);
                }
            }
        }

        return $result;
    }

    /**
     * Get form field by name
     *
     * @param string $name Field name
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFormFieldsByName($name)
    {
        $result = null;

        foreach ($this->getFormFields() as $formFields) {
            if (isset($formFields[static::SECTION_PARAM_FIELDS][$name])) {
                $result = $formFields[static::SECTION_PARAM_FIELDS][$name];

                break;
            }
        }

        return $result;
    }

    /**
     * Return list of the "Button" widgets
     * Do not use this method if you want sticky buttons panel.
     * The sticky buttons panel class has the buttons definition already.
     *
     * @return array
     */
    protected function getFormButtons()
    {
        return [];
    }

    /**
     * Prepare error message before display
     *
     * @param string $message Message itself
     * @param array  $data    Current section data
     *
     * @return string
     */
    protected function prepareErrorMessage($message, array $data)
    {
        if (isset($data[self::SECTION_PARAM_WIDGET])) {
            $sectionTitle = $data[self::SECTION_PARAM_WIDGET]->getLabel();
        }

        if (!empty($sectionTitle)) {
            $message = $sectionTitle . ': ' . $message;
        }

        return $message;
    }

    /**
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        foreach ($data[self::SECTION_PARAM_FIELDS] as $field) {
            if ($this->checkDependency($field)) {
                [$flag, $message] = $field->validate();
                if (!$flag) {
                    $this->addErrorMessage($field->getName(), $message, $data);
                }
            }
        }
    }

    /**
     * Validate form field.
     * This method is called from FormField object to perform additional validation on the form side.
     *
     * @param \XLite\View\FormField\AFormField $field Form field object
     *
     * @return array
     */
    public function validateFormField($field)
    {
        $result = [true, null];

        $name = $field->getName();

        if (in_array($name, $this->getFormFields(true))) {
            $method = 'validateFormField' . \Includes\Utils\Converter::convertToUpperCamelCase($name) . 'Value';
            if (method_exists($this, $method)) {
                $result = $this->$method($field, $this->getFormFields());
            }
        }

        return $result;
    }

    /**
     * Return list of form error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        if (!isset($this->errorMessages)) {
            $this->errorMessages = [];

            foreach ($this->getFormFields() as $section => $data) {
                $this->validateFields($data, $section);
            }
        }

        return $this->errorMessages;
    }

    /**
     * addErrorMessage
     *
     * @param string $name    Error name
     * @param string $message Error message
     * @param array  $data    Section data OPTIONAL
     *
     * @return void
     */
    protected function addErrorMessage($name, $message, array $data = [])
    {
        $this->errorMessages[$name] = $this->prepareErrorMessage($message, $data);
    }

    /**
     * Return list of saved form error messages
     *
     * @return array
     */
    protected function getSavedErrorMessages()
    {
        if (!isset($this->savedErrorMessages)) {
            $this->savedErrorMessages = $this->getSavedForm(self::SAVED_FORM_ERRORS);
        }

        return $this->savedErrorMessages;
    }

    /**
     * Return saved error message for the field
     *
     * @param string $fieldName
     *
     * @return \XLite\Core\Translation\Label
     */
    public function getSavedErrorMessage($fieldName)
    {
        $savedErrorMessages = $this->getSavedErrorMessages();

        return $savedErrorMessages[$fieldName] ?? null;
    }

    /**
     * Some JavaScript code to insert at the begin of form page
     *
     * @return string
     */
    protected function getTopInlineJSCode()
    {
        return null;
    }

    /**
     * Some JavaScript code to insert at the end of form page
     *
     * @return string
     */
    protected function getBottomInlineJSCode()
    {
        return null;
    }

    /**
     * Call the corresponded method for current action
     *
     * @param string $action Action name OPTIONAL
     *
     * @return boolean
     */
    protected function callActionHandler($action = null)
    {
        $action = self::ACTION_HANDLER_PREFIX . ucfirst($action ?: $this->currentAction);

        // Run the corresponded method
        return $this->$action();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionCreate()
    {
        return $this->getModelObject()->create();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionUpdate()
    {
        return $this->getModelObject()->update();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionModify()
    {
        if ($this->getModelObject()->isPersistent()) {
            $this->currentAction = 'update';
            $result = $this->callActionHandler('update');
        } else {
            $this->currentAction = 'create';
            $result = $this->callActionHandler('create');
        }

        return $result;
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean
     */
    protected function performActionDelete()
    {
        return $this->getModelObject()->delete();
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
        $model = $this->getModelObject();

        $result = null;
        if (is_object($model)) {
            $result = $this->getModelValue($model, $name);
        }

        return $result;
    }

    /**
     * Check if saved value different from value in object
     *
     * @param mixed $name Field/property name
     *
     * @return boolean
     */
    public function hasDifferentSavedValue($name)
    {
        return $this->getSavedData($name) !== null
            && $this->getSavedData($name) !== $this->getModelObjectValue($name);
    }

    /**
     * Get model value by name
     *
     * @param \XLite\Model\AEntity $model Model object
     * @param string               $name  Property name
     *
     * @return mixed
     */
    protected function getModelValue($model, $name)
    {
        $method = 'get' . \Includes\Utils\Converter::convertToUpperCamelCase($name);
        // $method - assemble from 'get' + property name
        return method_exists($model, $method)
            ? $model->$method()
            : ($model->isPropertyExists($name) ? $model->getterProperty($name) : null);
    }

    /**
     * Add field into the list of excluded fields
     *
     * @param string $fieldName Field name
     *
     * @return void
     */
    protected function excludeField($fieldName)
    {
        $this->excludedFields[] = $fieldName;
    }

    /**
     * Prepare request data for mapping into model object.
     * Model object is provided with methods:
     * prepareObjectForMapping <- getModelObject <- getDefaultModelObject (or getParam(self::PARAM_MODEL_OBJECT))
     *
     * Use $this->excludeField($fieldName) method to remove unnecessary data from request.
     *
     * Call $this->excludeField() method in "performAction*" methods before parent::performAction* call.
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = $this->getRequestData();

        // Remove fields in the $excludedFields list from the data for mapping
        if (!empty($this->excludedFields)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->excludedFields)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Prepare object for mapping
     *
     * @return \XLite\Model\AEntity
     */
    protected function prepareObjectForMapping()
    {
        return $this->getModelObject();
    }

    /**
     * Return name of the current form
     *
     * @return string
     */
    protected function getFormName()
    {
        return get_class($this);
    }

    /**
     * Display view sublist
     *
     * @param string $suffix    List suffix
     * @param array  $arguments List arguments OPTIONAL
     *
     * @return void
     */
    protected function displayViewSubList($suffix, array $arguments = [])
    {
        $class = preg_replace('/^.+\\\View\\\Model\\\/Ss', '', static::class);
        $class = str_replace('\\', '.', $class);
        if (preg_match('/^(a-z0-9+)\\\(a-z0-9+)\\\View\\\Model\\\/Sis', static::class, $match)) {
            $class = $match[1] . '.' . $match[2] . '.' . $class;
        }

        $class = strtolower($class);

        $list = 'crud.' . $class . '.' . $suffix;

        $arguments = $this->assembleViewSubListArguments($suffix, $arguments);

        $this->displayViewListContent($list, $arguments);
    }

    /**
     * Assemble view sublist arguments
     *
     * @param string $suffix    List suffix
     * @param array  $arguments Arguments
     *
     * @return array
     */
    protected function assembleViewSubListArguments($suffix, array $arguments)
    {
        $arguments['model'] = $this;
        $arguments['useBodyTemplate'] = false;

        return $arguments;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'model-properties';
    }

    /**
     * Get item class
     *
     * @param integer                          $index  Item index
     * @param integer                          $length Items list length
     * @param \XLite\View\FormField\AFormField $field  Current item
     *
     * @return string
     */
    protected function getItemClass($index, $length, \XLite\View\FormField\AFormField $field)
    {
        $classes = preg_grep('/.+/Ss', array_map('trim', explode(' ', $field->getWrapperClass())));

        if ($index === 1) {
            $classes[] = 'first';
        }

        if ($length == $index) {
            $classes[] = 'last';
        }

        if ($field->getParam(static::SCHEMA_DEPENDENCY)) {
            $classes[] = 'has-dependency';
        }

        if (
            !$this->showErrorsViaTopMessage()
            && $this->getSavedErrorMessage($field->getName())
        ) {
            $classes[] = 'has-error';
        }

        return implode(' ', $classes);
    }

    /**
     * Get field commented data
     *
     * @param \XLite\View\FormField\AFormField $filed Field
     *
     * @return array
     */
    protected function getFieldCommentedData($filed)
    {
        $commentedData = [];

        if ($filed->getParam(static::SCHEMA_DEPENDENCY)) {
            $commentedData['dependency'] = $filed->getParam(static::SCHEMA_DEPENDENCY);
        }

        return $commentedData;
    }

    /**
     * Check dependency
     *
     * @param \XLite\View\FormField\AFormField $field Field
     *
     * @return boolean
     */
    protected function checkDependency($field)
    {
        $dependency = $field->getParam(\XLite\View\FormField\AFormField::PARAM_DEPENDENCY);
        $result = true;

        foreach ($dependency as $depType => $dependencies) {
            foreach ($dependencies as $depField => $depValue) {
                if ($depType == static::DEPENDENCY_SHOW) {
                    if ($this->checkRequestHasExpectedValue($depField, $depValue)) {
                        if ($result !== false) {
                            $result = true;
                        }
                    } else {
                        $result = false;
                    }
                } else {
                    if ($this->checkRequestHasExpectedValue($depField, $depValue)) {
                        $result = false;
                    } else {
                        if ($result !== false) {
                            $result = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Check request has expected value
     *
     * @param string $fieldName     FieldName
     * @param mixed  $expectedValue Expected value (may be array of values)
     *
     * @return boolean
     */
    protected function checkRequestHasExpectedValue($fieldName, $expectedValue)
    {
        return $this->getRequestData($fieldName) == $expectedValue
            || (is_array($expectedValue) && in_array($this->getRequestData($fieldName), $expectedValue));
    }

    /**
     * @param string $fieldName
     * @param array $fieldData
     * @return bool
     */
    protected function isContentTrustedByPermission($fieldName, array $fieldData)
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('unfiltered html');
    }

    /**
     * Return true if specific section is collapsible
     *
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsible($section)
    {
        return false;
    }

    /**
     * Return true if specific section is collapsed
     *
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsed($section)
    {
        return false;
    }
}
