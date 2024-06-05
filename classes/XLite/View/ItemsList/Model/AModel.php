<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract admin model-based items list
 */
abstract class AModel extends \XLite\View\ItemsList\AItemsList
{
    use ExecuteCachedTrait;

    /**
     * Sortable types
     */
    public const SORT_TYPE_NONE  = 0;
    public const SORT_TYPE_MOVE  = 1;
    public const SORT_TYPE_INPUT = 2;

    /**
     * Create inline position
     */
    public const CREATE_INLINE_NONE   = 0;
    public const CREATE_INLINE_TOP    = 1;
    public const CREATE_INLINE_BOTTOM = 2;

    /**
     * Session cell to store lines errors
     */
    public const SAVED_LINES_WITH_ERRORS = 'savedLinesWithErrors';


    /**
     * Highlight step
     *
     * @var integer
     */
    protected $hightlightStep = 2;

    /**
     * Error messages
     *
     * @var array
     */
    protected $errorMessages = [];

    /**
     * Warning messages
     *
     * @var array
     */
    protected $warningMessages = [];

    /**
     * Lines with errors
     *
     * @var array
     */
    protected $linesWithErrors = [];

    /**
     * Request data
     *
     * @var array
     */
    protected $requestData;

    /**
     * Dump entity
     *
     * @var \XLite\Model\AEntity
     */
    protected $dumpEntity;

    /**
     * Entities created by $this::processCreate
     *
     * @var array
     */
    protected $createdEntities = [];

    protected static $savedDataCache = null;

    // {{{ Fields

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'data';
    }

    /**
     * Get data prefix for remove cells
     *
     * @return string
     */
    public function getRemoveDataPrefix()
    {
        return 'delete';
    }

    /**
     * Get data prefix for select cells
     *
     * @return string
     */
    public function getSelectorDataPrefix()
    {
        return 'select';
    }

    /**
     * Get data prefix for new data
     *
     * @return string
     */
    public function getCreateDataPrefix()
    {
        return 'new';
    }

    /**
     * Return list of created entities
     *
     * @return array
     */
    public function getCreatedEntities()
    {
        return $this->createdEntities;
    }

    /**
     * Get self
     *
     * @return \XLite\View\ItemsList\Model\AModel
     */
    protected function getSelf()
    {
        return $this;
    }

    // }}}

    // {{{ Model processing

    /**
     * Get field objects list (only inline-based form fields)
     *
     * @return array
     */
    abstract protected function getFieldObjects();

    /** @todo: remove before commit */
    ///**
    // * Define repository name
    // *
    // * @return string
    // */
    //abstract protected function defineRepositoryName();

    /**
     * Quick process
     *
     * @param array $parameters Parameters OPTIONAL
     *
     * @return void
     */
    public function processQuick(array $parameters = [])
    {
        $this->setWidgetParams($parameters);
        $this->init();
        $this->process();
    }

    /**
     * Process
     *
     * @return void
     */
    public function process()
    {
        $data = $this->getRequestData();
        $dataPrefix = $this->getDataPrefix();

        if (isset($data[$dataPrefix])) {
            $this->setSavedData($data[$dataPrefix]);
        }

        $this->processRemove();

        if ($this->shouldRenameDuplicates()) {
            $this->processRenameDuplicates();
        }

        $this->processUpdate();
        $this->processCreate();

        if ($this->showLinesWithErrors()) {
            $this->saveLinesWithErrors();
        }

        \XLite\Core\Database::getEM()->flush();
    }

    // {{{ Create

    /**
     * Get create field classes
     *
     * @return array
     */
    protected function getCreateFieldClasses()
    {
        return [];
    }

    /**
     * Process create new entities
     *
     * @return void
     */
    protected function processCreate()
    {
        $errCount = 0;
        $count = 0;

        foreach ($this->getNewDataLine() as $key => $line) {
            if ($this->isNewLineSufficient($line, $key)) {
                $entity = $this->createEntity();
                $fields = $this->createInlineFields($line, $entity);

                if ($this->validateNewEntity($fields, $key)) {
                    $this->saveNewEntity($fields, $entity, $line);
                    if ($this->prevalidateNewEntity($entity)) {
                        $this->insertNewEntity($entity);
                        $this->postprocessInsertedEntity($entity, $line);
                        $this->createdEntities[] = $entity;
                        $count++;
                    } else {
                        $this->undoCreatedEntity($entity, true);
                        $errCount++;
                    }
                } else {
                    $this->undoCreatedEntity($entity, false);
                    $errCount++;
                }
            }
        }

        if (0 < $count) {
            $label = $this->getCreateMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }

        if (0 < $errCount) {
            $this->processCreateErrors();
        }

        $this->processCreateWarnings();
    }

    /**
     * Validate new entity
     *
     * @param array  $fields Fields list
     * @param string $key    Field key
     *
     * @return boolean
     */
    protected function validateNewEntity(array $fields, $key)
    {
        $validated = 0 < count($fields);
        foreach ($fields as $inline) {
            $validated = $this->validateCell($inline, $key) && $validated;
        }

        return $validated;
    }

    /**
     * Save new entity
     *
     * @param array                $fields Fields
     * @param \XLite\Model\AEntity $entity Entity object
     * @param array                $line   New entity data from request
     *
     * @return void
     */
    protected function saveNewEntity(array $fields, $entity, $line)
    {
        foreach ($fields as $inline) {
            $this->saveCell($inline);
        }
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        $entity->getRepository()->insert($entity);
    }

    /**
     * Postprocess inserted entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $line   Array of entity data from request
     *
     * @return boolean
     */
    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        return true;
    }

    /**
     * Undo created entity
     *
     * @param \XLite\Model\AEntity $entity Created entity
     *
     * @return void
     */
    protected function undoCreatedEntity($entity)
    {
        \XLite\Core\Database::getEM()->remove($entity);
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return static::t('X entities has been created', ['count' => $count]);
    }

    /**
     * Get update message
     *
     * @return string
     */
    protected function getUpdateMessage()
    {
        return null;
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entityClass = $this->defineRepositoryName();

        return new $entityClass();
    }

    /**
     * Get dump entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDumpEntity()
    {
        if ($this->dumpEntity === null) {
            $this->dumpEntity = $this->createEntity();
        }

        return $this->dumpEntity;
    }

    /**
     * @inheritdoc
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        if (!$this->isCloned) {
            $this->clearSavedData();
        }

        if ($this->dumpEntity instanceof \XLite\Model\AEntity) {
            $this->removeDumpEntity($this->dumpEntity);
        }

        if ($this->showLinesWithErrors()) {
            $this->clearSavedLinesWithErrors();
        }
    }

    /**
     * Remove dump entity to avoid side-effects
     *
     * @param \XLite\Model\AEntity $entity
     */
    protected function removeDumpEntity($entity)
    {
        \XLite\Core\Database::getEM()->remove($entity);
    }

    /**
     * Get new data line
     *
     * @return array
     */
    protected function getNewDataLine()
    {
        return $this->executeCachedRuntime(function () {
            $data = $this->getRequestData();
            $prefix = $this->getCreateDataPrefix();

            return (isset($data[$prefix]) && is_array($data[$prefix])) ? $data[$prefix] : [];
        });
    }

    /**
     * Check - new line is sufficient or not
     *
     * @param array   $line Data line
     * @param integer $key  Field key gathered from request data, eg: new[this-key][field-name]
     *                      (see ..\AInline::processCreate())
     *
     * @return boolean
     */
    protected function isNewLineSufficient(array $line, $key)
    {
        return $key !== 0 && 0 < count($line);
    }

    /**
     * Create inline fields list
     *
     * @param array                $line   Line data
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function createInlineFields(array $line, \XLite\Model\AEntity $entity)
    {
        $list = [];

        foreach ($this->getCreateFieldClasses() as $object) {
            $this->prepareInlineField($object, $entity);
            $list[] = $object;
        }

        return $list;
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processCreateErrors()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        // Run controller's method
        $this->setActionError();
    }

    /**
     * Process warnings
     *
     * @return void
     */
    protected function processCreateWarnings()
    {
        $warnings = $this->getWarningMessages();

        if ($warnings) {
            \XLite\Core\TopMessage::getInstance()->addBatch($warnings, \XLite\Core\TopMessage::WARNING);
        }
    }


    // }}}

    // {{{ Remove

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X entities has been removed', ['count' => $count]);
    }

    /**
     * Process remove
     *
     * @return integer
     */
    protected function processRemove()
    {
        $count = 0;

        foreach ($this->getEntityIdListForRemove() as $id) {
            $entity = $this->findForRemove($id);
            if ($entity && $this->removeEntity($entity)) {
                $count++;
            }
        }

        if (0 < $count) {
            \XLite\Core\Database::getEM()->flush();

            $label = $this->getRemoveMessage($count);
            if ($label) {
                \XLite\Core\TopMessage::getInstance()->addInfo($label);
            }
        }

        return $count;
    }

    /**
     * Find for remove
     *
     * @param mixed $id Entity id
     *
     * @return \XLite\Model\AEntity
     */
    protected function findForRemove($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Get entity's ID list for remove
     *
     * @return array
     */
    protected function getEntityIdListForRemove()
    {
        $data = $this->getRequestData();
        $prefix = $this->getRemoveDataPrefix();

        $list = [];

        if (isset($data[$prefix]) && is_array($data[$prefix]) && $data[$prefix]) {
            foreach ($data[$prefix] as $id => $allow) {
                if ($allow) {
                    $list[] = $id;
                }
            }
        }

        return $list;
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $entity->getRepository()->delete($entity, false);

        return true;
    }

    // }}}

    // {{{ Rename duplicates

    /**
     * Return true if duplicates should be renamed
     *
     * @return boolean
     */
    protected function shouldRenameDuplicates()
    {
        return false;
    }

    /**
     * Process renaming of duplicate items
     */
    protected function processRenameDuplicates()
    {
        if ($this->renameDuplicates()) {
            $this->postProcessRenameDuplicates();
        }
    }

    /**
     * @return boolean
     */
    protected function renameDuplicates()
    {
        $data = $this->getRequestData();
        $dataPrefix = $this->getDataPrefix();
        $entitiesDataFromRequest = $data[$dataPrefix];
        $repo = $this->getRepository();

        $duplicateItemsWereRenamed = false;

        foreach ($this->findDuplicateNames() as $duplicateName) {
            $duplicateItems = $this->findDuplicates($duplicateName['name']);
            $duplicateItemsCount = count($duplicateItems);

            for ($i = 1; $i < $duplicateItemsCount; $i++) {
                $id = $duplicateItems[$i]->getId();

                if ($entitiesDataFromRequest[$id]['name'] === $duplicateName['name']) {
                    $newName = $duplicateItems[$i]->getName() . '_' . $id;

                    $duplicateItems[$i]->setName($newName);
                    $entitiesDataFromRequest[$id]['name'] = $newName;

                    $repo->update($duplicateItems[$i], [], false);
                    $duplicateItemsWereRenamed = true;
                }
            }
        }

        if ($duplicateItemsWereRenamed) {
            $data[$dataPrefix] = $entitiesDataFromRequest;
            $this->requestData = $data;
        }

        return $duplicateItemsWereRenamed;
    }

    /**
     * Postprocess renaming of duplicate items
     */
    protected function postProcessRenameDuplicates()
    {
        \XLite\Core\TopMessage::getInstance()->addWarning(
            'Identical entries were renamed as follows: entry_XXXX, where XXXX are numbers. Please review and correct them if necessary.'
        );

        $requestData = $this->getRequestData();
        $dataPrefix = $this->getDataPrefix();

        $this->setSavedData($requestData[$dataPrefix]);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * @return array
     */
    protected function findDuplicateNames()
    {
        return [];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function findDuplicates($name)
    {
        return [];
    }

    // }}}

    // {{{ Update

    /**
     * Process update
     *
     * @return boolean
     */
    protected function processUpdate()
    {
        $result = true;

        if ($this->isActiveModelProcessing()) {
            $result = $this->validateUpdate() && $this->update() !== false;

            if (!$result) {
                $this->processUpdateErrors();
            } else {
                $label = $this->getUpdateMessage();
                if ($label) {
                    \XLite\Core\TopMessage::getInstance()->addInfo($label);
                }
            }

            $this->processUpdateWarnings();
        }

        return $result;
    }

    /**
     * Check - model processing is active or not
     *
     * @return boolean
     */
    protected function isActiveModelProcessing()
    {
        return $this->hasResults() && $this->getFieldObjects();
    }

    /**
     * Validate data
     *
     * @return boolean
     */
    protected function validateUpdate()
    {
        $validated = true;

        foreach ($this->prepareInlineFields() as $field) {
            $validated = $this->validateCell($field) && $validated;
        }

        return $validated;
    }

    /**
     * Save data
     *
     * @return integer
     */
    protected function update()
    {
        $this->saveEntities();

        return $this->showLinesWithErrors()
            ? $this->updateWithLinesErrors()
            : $this->updateWithTopMessageErrors();
    }

    /**
     * Update items
     * If there are any errors save correct entities and show line errors near wrong lines
     *
     * @return boolean
     */
    protected function updateWithLinesErrors()
    {
        $result = true;

        foreach ($this->getPageDataForUpdate() as $entity) {
            if ($this->prevalidateEntity($entity)) {
                if ($this->isDefault()) {
                    $this->setDefaultValue($entity, $this->isDefaultEntity($entity));
                }

                $entity->getRepository()->update($entity, [], false);
            } else {
                \XLite\Core\Database::getEM()->detach($entity);
                $result = false;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return $result;
    }

    /**
     * Update items
     * If there are any errors undo all entities and show top message
     *
     * @return boolean
     */
    protected function updateWithTopMessageErrors()
    {
        if ($this->prevalidateEntities()) {
            $this->updateEntities();
            \XLite\Core\Database::getEM()->flush();
            $result = true;
        } else {
            $this->undoEntities();
            $result = false;
        }

        return $result;
    }

    /**
     * Save entities
     *
     * @return integer
     */
    protected function saveEntities()
    {
        $count = 0;

        foreach ($this->prepareInlineFields() as $field) {
            $count++;
            $this->saveCell($field);
        }

        return $count;
    }

    /**
     * Pre-validate entities
     *
     * @return boolean
     */
    protected function prevalidateEntities()
    {
        $result = true;
        foreach ($this->getPageDataForUpdate() as $entity) {
            $result = $this->prevalidateEntity($entity) && $result;
        }

        return $result;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        return true;
    }

    /**
     * Undo entities if entities pre-validation routine detect some errors
     *
     * @return void
     */
    protected function undoEntities()
    {
        foreach ($this->getPageDataForUpdate() as $entity) {
            \XLite\Core\Database::getEM()->refresh($entity);
        }
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
        foreach ($this->getPageDataForUpdate() as $entity) {
            $entity->getRepository()->update($entity, [], false);
            if ($this->isDefault()) {
                $this->setDefaultValue($entity, $this->isDefaultEntity($entity));
            }
        }
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param boolean $value
     */
    protected function setDefaultValue($entity, $value)
    {
        $entity->setDefaultValue($value);
    }

    /**
     * Is default entity
     *
     * @param \XLite\Model\AEntity $entity Line
     *
     * @return boolean
     */
    protected function isDefaultEntity(\XLite\Model\AEntity $entity)
    {
        $requestData = $this->getRequestData();

        return isset($requestData['defaultValue']) && (int) $requestData['defaultValue'] === $entity->getUniqueIdentifier();
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processUpdateErrors()
    {
        \XLite\Core\TopMessage::getInstance()->addBatch($this->getErrorMessages(), \XLite\Core\TopMessage::ERROR);

        // Run controller's method
        $this->setActionError();
    }

    /**
     * Process errors
     *
     * @return void
     */
    protected function processUpdateWarnings()
    {
        $warnings = $this->getWarningMessages();

        if ($warnings) {
            \XLite\Core\TopMessage::getInstance()->addBatch($warnings, \XLite\Core\TopMessage::WARNING);
        }
    }

    /**
     * Validate inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     * @param integer                              $key    Field key gathered from request data,
     *                                                     eg: new[this-key][field-name]
     *                                                     (see ..\AInline::processCreate()) OPTIONAL
     *
     * @return boolean
     */
    protected function validateCell(\XLite\View\FormField\Inline\AInline $inline, $key = null)
    {
        $inline->setValueFromRequest($this->getRequestData(), $key);
        [$flag, $message] = $inline->validate();
        if (!$flag) {
            $this->addErrorMessage($inline, $message);
        }

        return $flag;
    }

    /**
     * Save cell
     *
     * @param \XLite\View\FormField\Inline\AInline $inline Inline field
     *
     * @return void
     */
    protected function saveCell(\XLite\View\FormField\Inline\AInline $inline)
    {
        $inline->saveValue();
    }

    /**
     * Get inline fields
     *
     * @return array
     */
    protected function prepareInlineFields()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineInlineFields();
        });
    }

    /**
     * Define inline fields
     *
     * @return array
     */
    protected function defineInlineFields()
    {
        $list = [];

        foreach ($this->getPageDataForUpdate() as $entity) {
            foreach ($this->getFieldObjects() as $object) {
                $this->prepareInlineField($object, $entity);
                $list[] = $object;
            }
        }

        return $list;
    }

    /**
     * Get inline field
     *
     * @param \XLite\View\FormField\Inline\AInline $field  Field
     * @param \XLite\Model\AEntity                 $entity Entity
     *
     * @return void
     */
    protected function prepareInlineField(\XLite\View\FormField\Inline\AInline $field, \XLite\Model\AEntity $entity)
    {
        $field->setWidgetParams(['entity' => $entity, 'itemsList' => $this]);
    }

    /**
     * @param $fieldName
     * @param $id
     *
     * @return mixed|null
     */
    public function getSavedFieldValue($fieldName, $id)
    {
        $data = $this->getSavedData();

        return $data[$id][$fieldName] ?? null;
    }

    /**
     * @return mixed
     */
    protected function getSavedData()
    {
        // This is not executeCachedRuntime
        // because executeCachedRuntime bind to the object, not class

        if (!static::$savedDataCache) {
            $session = \XLite\Core\Session::getInstance();
            static::$savedDataCache = $session->get(
                get_class($this) . '_' . $session->getCurrentLanguage()
            );
        }

        return static::$savedDataCache;
    }

    /**
     * @param $data
     */
    protected function setSavedData($data)
    {
        $session = \XLite\Core\Session::getInstance();
        $session->set(
            get_class($this) . '_' . $session->getCurrentLanguage(),
            $data
        );
    }

    /**
     * Clear form fields in session
     *
     * @return void
     */
    public function clearSavedData()
    {
        $this->setSavedData(null);
    }

    /**
     * Get page data for update
     *
     * @return array
     */
    protected function getPageDataForUpdate()
    {
        $list = [];
        foreach ($this->getPageData() as $entity) {
            if ($entity->isPersistent()) {
                $list[] = $entity;
            }
        }

        return $list;
    }

    // }}}

    // {{{ Misc.

    /**
     * Get request data
     *
     * @return array
     */
    protected function getRequestData()
    {
        if ($this->requestData === null) {
            $this->requestData = $this->defineRequestData();
        }

        return $this->requestData;
    }

    /**
     * Define request data
     *
     * @return array
     */
    protected function defineRequestData()
    {
        return $this->prepareRequestParamsList();
    }

    /**
     * Add error message
     *
     * @param \XLite\View\FormField\Inline\AInline $inline  Inline field
     * @param string                               $message Message
     */
    protected function addErrorMessage(\XLite\View\FormField\Inline\AInline $inline, $message)
    {
        $this->errorMessages[] = $inline->getLabel() . ': ' . $message;
    }

    /**
     * Add error message
     *
     * @param        $label
     * @param string $message Message
     */
    public function addPlainErrorMessage($label, $message)
    {
        $this->errorMessages[] = ($label ? $label . ': ' : '') . $message;
    }

    /**
     * Get error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Add error message
     *
     * @param \XLite\View\FormField\Inline\AInline $inline  Inline field
     * @param string                               $message Message
     *
     * @return void
     */
    protected function addWarningMessage(\XLite\View\FormField\Inline\AInline $inline, $message)
    {
        $this->warningMessages[] = $inline->getLabel() . ': ' . $message;
    }

    /**
     * Get warning messages
     *
     * @return array
     */
    protected function getWarningMessages()
    {
        return $this->warningMessages;
    }

    /**
     * @param array  $line
     * @param string $fieldName
     * @param string $errorMessage
     */
    protected function addLineWithError($line, $fieldName, $errorMessage)
    {
        $type = isset($line['id'])
            ? 'existing'
            : 'new';

        $this->linesWithErrors[$type][] = [
            'fields' => $line,
            'error' => [
                'fieldName' => $fieldName,
                'message' => $errorMessage,
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getLinesWithErrors()
    {
        return $this->linesWithErrors;
    }

    /**
     * Save new lines with errors in session
     */
    protected function saveLinesWithErrors()
    {
        \XLite\Core\Session::getInstance()->set(
            self::SAVED_LINES_WITH_ERRORS,
            $this->getLinesWithErrors() ?: []
        );
    }

    /**
     * Fetch saved new lines with errors from session
     *
     * @return array
     */
    protected function getSavedLinesWithErrors()
    {
        return \XLite\Core\Session::getInstance()->get(self::SAVED_LINES_WITH_ERRORS);
    }

    /**
     * Clear new lines with errors in session
     */
    protected function clearSavedLinesWithErrors()
    {
        \XLite\Core\Session::getInstance()->set(
            self::SAVED_LINES_WITH_ERRORS,
            []
        );
    }

    /**
     * Return true if lines with errors should be shown
     *
     * @return boolean
     */
    protected function showLinesWithErrors()
    {
        return false;
    }

    // }}}

    // {{{ Content helpers

    /**
     * Get anchor name
     *
     * @return string
     */
    public function getAnchorName()
    {
        return implode('_', $this->getViewClassKeys());
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/model/style.less';
        $list[] = $this->getDir() . '/model/style.css';

        return $list;
    }

    /**
     * Check - body template is visible or not
     *
     * @return boolean
     */
    protected function isPageBodyVisible()
    {
        return $this->hasResults() || $this->isInlineCreation() !== static::CREATE_INLINE_NONE;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->isPageBodyVisible() && $this->getPager();
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'model';
    }

    /**
     * Get line attributes
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line entity OPTIONAL
     *
     * @return array
     */
    protected function getLineAttributes($index, \XLite\Model\AEntity $entity = null)
    {
        $result = [
            'class'   => $this->defineLineClass($index, $entity),
            'data-id' => $entity ? $entity->getUniqueIdentifier() : 0,
        ];

        if ($index == -1) {
            $result['style'] = 'display: none;';
            $result['v-pre'] = '';
        }

        return $result;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = ['line'];

        if ($index === 0) {
            $classes[] = 'first';
        }

        if ($this->getItemsCount() == $index + 1) {
            $classes[] = 'last';
        }

        if (0 === ($index + 1) % $this->hightlightStep) {
            $classes[] = 'even';
        }

        if ($entity && $entity->isPersistent()) {
            $classes[] = 'entity-' . $entity->getUniqueIdentifier();
        } else {
            $classes[] = 'create-tpl';
            $classes[] = 'dump-entity';
        }

        return $classes;
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.' . implode('.', $this->getListNameSuffixes());
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        $parts = explode('\\', get_called_class());

        $names = [];
        if ($parts[0] !== 'XLite') {
            $names[] = strtolower($parts[0]);
            $names[] = strtolower($parts[1]);
        }

        $names[] = strtolower($parts[count($parts) - 1]);

        return $names;
    }

    /**
     * Search for page data. Returns entity if search count has single result or null otherwise.
     *
     * @return array|null
     */
    public function searchForSingleEntity()
    {
        $result = null;

        if ($this->getItemsCount() == 1) {
            $data = $this->getPageData();
            $result = current($data);
        }

        return $result;
    }

    /**
     * Build entity page URL
     * @todo: reorder params
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            [$entity->getUniqueIdentifierName() => $entity->getUniqueIdentifier()]
        );
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'widget items-list'
            . ' widgetclass-' . $this->getWidgetClass()
            . ' widgettarget-' . static::getWidgetTarget()
            . ' sessioncell-' . $this->getSessionCell()
            . ($this->isPagerVisible() ? ' pager-visible' : '')
            . ($this->isListBlank() ? ' list-blank' : '');
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return [
            'class'         => $this->getContainerClass(),
            'data-js-class' => $this->getJSHandlerClassName(),
        ];
    }

    /**
     * Get container attributes as string
     *
     * @return string
     */
    protected function getContainerAttributesAsString()
    {
        $list = [];
        foreach ($this->getContainerAttributes() as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }


    // }}}

    // {{{ Line behaviors

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * Get edit link
     *
     * @return string
     */
    protected function getEditLink()
    {
        return null;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Create';
    }

    /**
     * Get entity position
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return integer
     */
    protected function getEntityPosition(\XLite\Model\AEntity $entity)
    {
        return $entity->getOrder();
    }

    // }}}

    // {{{ Sticky panel

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return $this->getPanelClass();
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsListForm';
    }

    // }}}

    /**
     * Return specific items list parameters that will be sent to JS code
     *
     * @return array
     */
    protected function getItemsListParams()
    {
        $itemsListParams = parent::getItemsListParams();

        if ($this->showLinesWithErrors()) {
            $itemsListParams['linesWithErrors'] = $this->getSavedLinesWithErrors();
        }

        return  $itemsListParams;
    }
}
