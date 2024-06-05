<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Model;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use Qualiteam\SkinActVideoFeature\View\Form\Model\VideoCategory as VideoCategoryFormModel;
use Qualiteam\SkinActVideoFeature\View\FormField\Select\Select2\VideoCategory as Select2VideoCategory;
use Qualiteam\SkinActVideoFeature\View\FormField\Select\VideoCategory as SelectVideoCategory;
use XLite\View\FormField\AFormField;

class VideoCategory extends \XLite\View\Model\AModel
{

    /**
     * @var bool
     */
    private bool $correctCategories = false;

    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_REQUIRED => true,
        ],
        'parent' => [
            self::SCHEMA_CLASS                            => Select2VideoCategory::class,
            self::SCHEMA_REQUIRED                         => true,
            Select2VideoCategory::PARAM_OBJECT_CLASS_NAME => 'Qualiteam\SkinActVideoFeature\Model\VideoCategory',
        ],
        'image'  => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\FileUploader\Image',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    public function __construct(array $params = [], array $sections = [])
    {
        $this->schemaDefault['name'][self::SCHEMA_LABEL]   = static::t('SkinActVideoFeature category name');
        $this->schemaDefault['image'][self::SCHEMA_LABEL]  = static::t('SkinActVideoFeature category icon');
        $this->schemaDefault['parent'][self::SCHEMA_LABEL] = static::t('SkinActVideoFeature parent category');

        parent::__construct($params, $sections);
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->id;
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
        if (isset($schema['parent'])) {
            $schema['parent'][SelectVideoCategory::PARAM_EXCLUDE_CATEGORY]      = $this->getModelId();
            $schema['parent'][SelectVideoCategory::PARAM_DISPLAY_ROOT_CATEGORY] = true;
            $schema['parent'][AFormField::PARAM_VALUE]                          = $this->getModelObject()->getParent()->getCategoryId();
        }

        return parent::getFieldsBySchema($schema);
    }

    /**
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        if ($name === 'enabled') {
            $data[static::SCHEMA_HELP] = static::t(
                'SkinActVideoFeature if the video category is disabled, the system will return 404',
                ['categoryLink' => $this->getModelObject()->getFrontUrl(true)]
            );
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return VideoCategoryModel
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo(VideoCategoryModel::class)->find($this->getModelId())
            : null;

        return $model ?: new VideoCategoryModel;
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
        $parentId = (int) (!empty($data['parent']) ? $data['parent'] : \XLite\Core\Request::getInstance()->parent);
        unset($data['parent']);

        parent::setModelProperties($data);

        $model = $this->getModelObject();

        $currentParentId = $model->getParent() ? $model->getParent()->getCategoryId() : null;

        $isRootCategory = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategoryId() == $model->getCategoryId();

        if (!$isRootCategory && (!$model->isPersistent() || !$currentParentId || ($parentId && $currentParentId != $parentId))) {
            // Set parent
            $parent = null;
            if ($parentId) {
                $parent = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->find($parentId);
            }

            if (!$parent) {
                $parent = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategory();
            }

            $model->setParent($parent);
            $this->correctCategories = true;
        }

        if (!$model->isPersistent()) {
            // Resort
            $pos = 0;
            $model->setPos($pos);
            foreach ($parent->getChildren() as $child) {
                $pos += 10;
                $child->setPos($pos);
            }
        }
    }

    /**
     * Correct categories structure after success saved
     */
    protected function postprocessSuccessActionUpdate()
    {
        if ($this->correctCategories) {
            \XLite\Core\Database::getRepo(VideoCategoryModel::class)->correctCategoriesStructure();
            $this->correctCategories = false;
        }

        parent::postprocessSuccessActionUpdate();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return VideoCategoryFormModel::class;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->isPersistent()
            ? static::t('SkinActVideoFeature update form button')
            : static::t('SkinActVideoFeature create form button');

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ($this->currentAction !== 'create') {
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature the video category has been updated');
        } else {
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature the video category has been added');
        }
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
        unset($data['image']);

        parent::saveFormData($data);
    }
}