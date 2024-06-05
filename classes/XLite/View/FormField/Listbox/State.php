<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Listbox;

/**
 * States listbox widget
 */
class State extends \XLite\View\FormField\Listbox\AListbox
{
    /**
     * Widget param names
     */
    public const PARAM_ALL = 'all';


    /**
     * Prepare and set up value of listbox
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_object($value) && $value instanceof \Doctrine\Common\Collections\Collection) {
            $value = $value->toArray();
        } elseif (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $k => $v) {
            if (is_object($v) && $v instanceof \XLite\Model\AEntity) {
                $value[$k] = $v->getCountry()->getCode() . '_' . $v->getCode();
            }
        }

        parent::setValue($value);
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Core\Database::getRepo('XLite\Model\State')->findAllStates();
        usort($list, ['\XLite\Model\Zone', 'sortStates']);

        $options = [];

        foreach ($list as $state) {
            $options[$state->getCountry()->getCode() . '_' . $state->getCode()] = $state->getCountry()->getCountry() . ': ' . $state->getState();
        }

        return $options;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' state-listbox';
    }

    /**
     * Get field label template
     *
     * @return string
     */
    protected function getFieldLabelTemplate()
    {
        return 'form_field/label/state_label.twig';
    }
}
