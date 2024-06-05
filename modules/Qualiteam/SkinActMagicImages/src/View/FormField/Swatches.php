<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\FormField;

use Qualiteam\SkinActMagicImages\View\FormField\Select\Swatches as SelectSwatches;
use XLite\Core\Database;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\View\FormField\Inline\Base\Single;

class Swatches extends Single
{
    /**
     * Define field class
     *
     * @return string
     */
    protected function defineFieldClass(): string
    {
        return SelectSwatches::class;
    }

    protected function saveFieldEntityValue(array $field, $value)
    {
        $value = Database::getRepo(AttributeValueSelect::class)->find($value);
        parent::saveFieldEntityValue($field, $value);
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getViewValue(array $field): mixed
    {
        return $this->getEntityValue() ? $this->getEntityValue()->getAttributeOption()->getName() : static::t('SkinActMagicImages none');
    }
}