<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBrandCoupon\View\FormField\Select\Select2;

use QSL\ShopByBrand\Model\Brand;
use Qualiteam\SkinActBrandCoupon\SkinActBrandCouponModule;
use XLite\Core\Database;
use XLite\View\FormField\Select\Multiple;
use XLite\View\FormField\Select\MultipleTrait;
use XLite\View\FormField\Select\Select2Trait;

class Brands extends Multiple
{
    use Select2Trait, MultipleTrait {
        MultipleTrait::getCommonAttributes as getCommonAttributesMultiple;
        MultipleTrait::setCommonAttributes as setCommonAttributesMultiple;
        MultipleTrait::isOptionSelected as isOptionSelectedMultiple;
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

    /**
     * @return string
     */
    protected function getValueContainerClass(): string
    {
        $class = parent::getValueContainerClass();

        $class .= ' input-brands-select2';

        return $class;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getJSDirPath() . '/select/brands.js';

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

        return parent::prepareRequestData($value);
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value): void
    {
        if (is_string($value)) {
            $value = $this->getValuesArray($value);
        }

        parent::setValue($value);
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
     * @return string
     */
    protected function getJSDirPath(): string
    {
        return SkinActBrandCouponModule::getModulePath() . $this->getDir();
    }

    /**
     * @return string
     */
    protected function getPlaceholderLabel(): string
    {
        return static::t('SkinActBrandCoupon all');
    }

    /**
     * Get product classes list
     *
     * @return array
     */
    protected function getBrandsList(): array
    {
        $list = [];
        foreach (Database::getRepo(Brand::class)->search() as $e) {
            $list[$e->getId()] = $e->getName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return $this->getBrandsList();
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = 'form_field/input/text/autocomplete.css';

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData(): array
    {
        $data = $this->getSelect2CommentedData();
        $data['ajaxUrl'] = $this->buildURL('select_brands');
        $data['short-lbl'] = static::t('Please enter 3 or more characters');

        return $data;
    }
}