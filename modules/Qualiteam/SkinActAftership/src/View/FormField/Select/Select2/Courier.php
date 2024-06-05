<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Select\Select2;

use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\Database;
use XLite\View\FormField\Select\ASelect;
use XLite\View\FormField\Select\Select2Trait;

class Courier extends ASelect
{
    use AftershipTrait, Select2Trait {
        Select2Trait::getCommentedData as getSelect2CommentedData;
        Select2Trait::getValueContainerClass as getSelect2ContainerClass;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles(): array
    {
        $list                         = parent::getCommonFiles();
        $list[static::RESOURCE_JS][]  = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/form_field/input/select/couriers.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = 'form_field/input/text/autocomplete.css';
        $list[] = $this->getModulePath() . '/form_field/input/select/style.less';

        return $list;
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value): mixed
    {
        if (is_array($value)
            && is_array($value[0])
        ) {
            $arr = [];

            foreach ($value as $v) {
                $arr[] = $v[0];
            }

            $value = $arr;
        }

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::prepareRequestData($value);
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function getValuesArray(string $value): array
    {
        return array_map('trim', explode(',', $value));
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        $list = Database::getRepo(AftershipCouriers::class)
            ->findAll();

        $options = [];

        foreach ($list as $courier) {
            $options[$courier->getSlug()] = $courier->getName();
        }

        return $options;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass(): string
    {
        return parent::getValueContainerClass() . ' input-aftership-couriers-select2';
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData(): array
    {
        $data = $this->getSelect2CommentedData();
        $data['ajaxUrl'] = $this->buildURL('select_couriers');
        $data['short-lbl'] = static::t('SkinActAftership please enter 3 or more characters');
        return $data;
    }
}