<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to export page
 */
class Export extends \XLite\View\Tabs\ATabs
{
    /**
     * Widget parameter names
     */
    public const PARAM_PRESELECT = 'preselect';

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [
            'export_new' => [
                'weight'     => 100,
                'title'      => static::t('New export'),
                'url_params' => ['target' => 'export', 'page' => 'new'],
                'template'   => 'export/parts/begin.new_export.twig',
            ],
        ];

        if ($this->downloadFilesAvailable()) {
            $tabs['export_last'] = [
                'weight'     => 200,
                'title'      => static::t('Last exported'),
                'url_params' => ['target' => 'export', 'page' => 'last'],
                'template'   => 'export/parts/begin.last_export.twig',
            ];
        }

        return $tabs;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL(
            'export',
            '',
            [
                'page' => $target,
            ]
        );
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRESELECT => new \XLite\Model\WidgetParam\TypeString('Preselected class', 'XLite\Logic\Export\Step\Products'),
        ];
    }

    /**
     * Define sections list
     *
     * @return array<string, array<string, string|int>>
     */
    protected function defineSections()
    {
        return [
            'XLite\Logic\Export\Step\Products'                               => ['label' => 'Products', 'position' => 10],
            'XLite\Logic\Export\Step\Categories'                             => ['label' => 'Categories', 'position' => 30],
            'XLite\Logic\Export\Step\Attributes'                             => ['label' => 'Classes & Attributes', 'position' => 50],
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox' => ['label' => 'Product attributes values', 'position' => 70],
            'XLite\Logic\Export\Step\Orders'                                 => ['label' => 'Orders', 'position' => 90],
            'XLite\Logic\Export\Step\Users'                                  => ['label' => 'Customers', 'position' => 110],
        ];
    }

    /**
     * Return sections list
     *
     * @return string[]
     */
    protected function getSections()
    {
        $sections = $this->defineSections();

        uasort($sections, static function ($one, $two) {
            $onePosition = (is_array($one) && isset($one['position'])) ? $one['position'] : INF;
            $twoPosition = (is_array($two) && isset($two['position'])) ? $two['position'] : INF;

            return $onePosition > $twoPosition;
        });

        return array_map(
            static fn ($item) => (is_array($item) && isset($item['label'])) ? $item['label'] : $item,
            $sections
        );
    }

    /**
     * Check section is selected or not
     *
     * @param string $class Class
     *
     * @return bool
     */
    protected function isSectionSelected($class)
    {
        return $this->getParam(static::PARAM_PRESELECT) == $class && !$this->isSectionDisabled($class)
            && !$this->isSectionDisabled($class);
    }

    /**
     * Check section is disabled or not
     *
     * @param string $class Class
     *
     * @return bool
     */
    protected function isSectionDisabled($class)
    {
        $found = false;

        $classes = [];

        $classes[] = $class;

        if ($class === 'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox') {
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect';
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueText';
        }

        foreach ($classes as $c) {
            $class = new $c();
            if ($found = (0 < $class->count())) {
                break;
            }
        }

        return !$found;
    }

    /**
     * Check - charset enabled or not
     *
     * @return bool
     */
    protected function isCharsetEnabled()
    {
        return \XLite\Core\Iconv::getInstance()->isValid();
    }

    /**
     * Check download files available or not
     *
     * @return bool
     */
    protected function downloadFilesAvailable()
    {
        if ($this->getGenerator()) {
            foreach ($this->getGenerator()->getDownloadableFiles() as $path) {
                if (preg_match('/\.csv$/Ss', $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get export state
     *
     * @return bool
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }
}
