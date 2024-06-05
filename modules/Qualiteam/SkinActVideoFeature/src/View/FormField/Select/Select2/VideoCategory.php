<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\FormField\Select\Select2;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Request;
use XLite\View\FormField\Select\MultipleTrait;
use XLite\View\FormField\Select\Select2Trait;
use XLite\Core\Validator;

class VideoCategory extends \Qualiteam\SkinActVideoFeature\View\FormField\Select\VideoCategory
{
    use ExecuteCachedTrait, MultipleTrait, Select2Trait {
        MultipleTrait::getCommonAttributes as getCommonAttributesMultiple;
        MultipleTrait::setCommonAttributes as setCommonAttributesMultiple;
        MultipleTrait::isOptionSelected as isOptionSelectedMultiple;
        Select2Trait::getCommentedData as getSelect2CommentedData;
        Select2Trait::getValueContainerClass as getSelect2ContainerClass;
    }

    public const PARAM_MULTIPLE          = 'multiple';
    public const PARAM_OBJECT_CLASS_NAME = 'objectClassName';
    public const PARAM_OBJECT_ID_NAME    = 'objectIdName';
    public const PARAM_OBJECT_ID         = 'objectId';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MULTIPLE        => new \XLite\Model\WidgetParam\TypeBool('Select multiple', false),
            self::PARAM_OBJECT_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Object class name'),
            self::PARAM_OBJECT_ID_NAME    => new \XLite\Model\WidgetParam\TypeString('Object Id name', 'id'),
            self::PARAM_OBJECT_ID         => new \XLite\Model\WidgetParam\TypeInt('Object Id'),
        ];
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = $this->getSelect2ContainerClass();

        $class .= ' input-video-category-select2';

        return $class;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        if ($this->getParam(static::PARAM_MULTIPLE)) {
            return $this->setCommonAttributesMultiple($attrs);
        }

        return parent::setCommonAttributes($attrs);
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        if ($this->getParam(static::PARAM_MULTIPLE)) {
            return $this->getCommonAttributesMultiple();
        }

        return parent::getCommonAttributes();
    }

    /**
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);

        if ($value !== 0 && $value !== 'no_category') {
            $category = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getCategory($value);

            if (!$category->isVisible()) {
                $attributes['data-disabled'] = true;
            }
        }

        return $attributes;
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = [];

        if ($this->getValue()) {
            foreach ($this->getValue() as $selectedCategoryId) {
                if ($selectedCategoryId == '0') {
                    $list[$selectedCategoryId] = static::t('SkinActVideoFeature any category');
                } elseif ($selectedCategoryId == 'no_category') {
                    $list[$selectedCategoryId] = static::t('SkinActVideoFeature no category assigned');
                } else {
                    $selectedCategory = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getCategory($selectedCategoryId);

                    if (isset($selectedCategory)) {
                        if ($selectedCategory->isRootCategory()) {
                            $list[$selectedCategoryId] = $this->getTarget() == 'video_category'
                                ? static::t('SkinActVideoFeature root category')
                                : static::t('SkinActVideoFeature any category');
                        } else {
                            $list[$selectedCategoryId] = $selectedCategory->getStringPath();
                        }
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/form_filed/select/select2/video_category.less';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/form_field/select/select2/video_category.js';

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array_merge($this->getSelect2CommentedData(), [
            'placeholder-lbl'     => static::t('SkinActVideoFeature any category'),
            'disabled-lbl'        => static::t('SkinActVideoFeature category is not accessible'),
            'short-lbl'           => static::t('SkinActVideoFeature please enter 3 or more characters'),
            'more-lbl'            => static::t('SkinActVideoFeature loading more results'),
            'displayNoCategory'   => $this->getParam(static::PARAM_DISPLAY_NO_CATEGORY) ? 1 : 0,
            'displayRootCategory' => $this->getParam(static::PARAM_DISPLAY_ROOT_CATEGORY) ? 1 : 0,
            'displayAnyCategory'  => $this->getParam(static::PARAM_DISPLAY_ANY_CATEGORY) ? 1 : 0,
            'excludeCategory'     => $this->getParam(static::PARAM_EXCLUDE_CATEGORY) ?? 0,
        ]);
    }

    /**
     * Check field validity
     *
     * @return bool
     */
    protected function checkFieldValidity()
    {
        $result      = parent::checkFieldValidity();
        $objectClass = $this->getParam(self::PARAM_OBJECT_CLASS_NAME);

        if ($result && $objectClass && $this->getValue()) {
            $validator = new Validator\LoopProtect(
                $this->getParam(self::PARAM_NAME),
                $objectClass,
                $this->getObjectId()
            );
            try {
                foreach ($this->getValue() as $value) {
                    $validator->validate($value);
                }
            } catch (Validator\Exception $exception) {
                $result             = false;
                $this->errorMessage = static::t('SkinActVideoFeature the directory selected as a parent directory has already been specified as a child directory');
            }
        }

        return $result;
    }

    /**
     * Returns object id
     *
     * @return integer
     */
    protected function getObjectId()
    {
        return $this->getParam(static::PARAM_OBJECT_ID)
            ?: Request::getInstance()->{$this->getParam(static::PARAM_OBJECT_ID_NAME)};
    }
}