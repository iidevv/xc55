<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Admin;

use XLite\Model\Attribute;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Checkbox;
use XLite\View\FormField\Input\Radio;

class DisplayModes extends AAdmin
{
    public const PARAM_ATTRIBUTE = 'attribute';

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ATTRIBUTE => new TypeObject(
                'Attribute',
                null,
                false,
                Attribute::class
            ),
        ];
    }

    public function getAttribute(): ?Attribute
    {
        return $this->getParam(static::PARAM_ATTRIBUTE);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return  'product/attributes/display_mode.twig';
    }

    public function getDisplayModesWidgets(): array
    {
        $widgetsSchema = $this->getDisplayModesSchema();
        uasort($widgetsSchema, static function ($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });

        return array_map(function ($schema) {
            return $this->getWidget(
                $schema['params'],
                $schema['class']
            );
        }, $widgetsSchema);
    }

    public function getCurrentDisplayModeText(): string
    {
        $widgetsSchema = $this->getDisplayModesSchema();

        foreach ($widgetsSchema as $schema) {
            $isChecked = $schema['params'][Checkbox::PARAM_IS_CHECKED] ?? false;
            if ($isChecked) {
                return $schema['params'][AFormField::PARAM_LABEL];
            }
        }

        return '';
    }

    protected function getDisplayModesSchema(): array
    {
        $fieldValue = Attribute::SELECT_BOX_MODE;
        $fieldId = 'newValue-new-id-displayMode';
        $fieldName = 'newValue[NEW_ID][displayMode]';

        if ($attribute = $this->getAttribute()) {
            $id = $attribute->getId();
            $fieldName = "displayMode[$id]";
            $fieldId = "displayMode[$id]";
            $fieldValue = $attribute->getDisplayMode();
        }

        $result = [];
        $weight = 100;

        foreach (Attribute::getDisplayModes() as $displayMode => $label) {
            $result[$displayMode] = [
                'params' => [
                    AFormField::PARAM_VALUE      => $displayMode,
                    AFormField::PARAM_NAME       => $fieldName,
                    AFormField::PARAM_LABEL      => $label,
                    AFormField::PARAM_ID         => $fieldId . $displayMode,
                    AFormField::PARAM_ATTRIBUTES => ['class' => 'display-mode-input'],
                    Checkbox::PARAM_IS_CHECKED   => $fieldValue === $displayMode,
                ],
                'class'  => Radio::class,
                'weight' => $weight,
            ];

            $weight += 100;
        }

        return $result;
    }
}
