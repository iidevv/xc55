<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField\Select\Select2;

use CDev\SimpleCMS\View\FormField\Select\PagesSelectorTrait;
use XLite\View\FormField\Select\Select2Trait;

class Pages extends \XLite\View\FormField\Select\ASelect
{
    use PagesSelectorTrait, Select2Trait {
        Select2Trait::getCommentedData as getSelect2CommentedData;
        Select2Trait::getValueContainerClass as getSelect2ContainerClass;
    }

    /**
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][]  = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/SimpleCMS/form_field/pages/script.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/SimpleCMS/form_field/pages/style.less';

        return $list;
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' input-pages-select2';
    }

    /**
     * @return array
     */
    protected function getCommentedData()
    {
        return array_merge($this->getSelect2CommentedData(), [
            'placeholder-lbl' => static::t('Search or paste link'),
            'short-lbl'       => static::t('Please enter 3 or more characters'),
            'more-lbl'        => static::t('Loading more results...')
        ]);
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $options = static::getAllPages();

        $options = isset($options[$this->getValue()])
            ? [$this->getValue() => $options[$this->getValue()]]
            : [$this->getValue() => $this->getValue()];

        return $options;
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();
        $path = static::getAllPages()[$this->getValue()] ?? null;

        if ($path && static::getChildrenQuantityByPath($path) > 0) {
            $path .= Pages::$PATH_SEPARATOR . '_';
        }

        $list['data-path'] = $path;

        return $list;
    }
}
