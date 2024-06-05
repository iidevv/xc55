<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View\FormField\Select;


class Swatch extends \XLite\View\FormField\Select\ASelect
{

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.js';

        return $list;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [0 => ''];
        foreach ($this->getSwatches() as $swatch) {
            $list[$swatch->getId()] = $swatch->getName();
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();
        if ($this->getValue() && isset($list[0])) {
            unset($list[0]);
        }

        return $list;
    }

    /**
     * @return \QSL\ColorSwatches\Model\Swatch[]
     */
    protected function getSwatches()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(static function () {
            $qb = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->createQueryBuilder('s');
            $qb->orderBy('translations.name', 'asc');
            return $qb->getResult();
        }, ['all_active_swatches']);
    }

    protected function getFieldTemplate()
    {
        return 'modules/QSL/ColorSwatches/form_field/swatch.twig';
    }

    protected function getDir()
    {
        return '';
    }

    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' swatch-selector';
    }

    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);
        foreach ($this->getSwatches() as $swatch) {
            if ($swatch->getId() === $value) {
                $attributes['data-color'] = $swatch->getColor();
                $attributes['data-image'] = $swatch->getImage()
                    ? $swatch->getImage()->getFrontURL()
                    : null;
                break;
            }
        }

        return $attributes;
    }

}