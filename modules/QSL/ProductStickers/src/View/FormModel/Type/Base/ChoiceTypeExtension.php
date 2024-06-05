<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * Adds a CSRF field to the root form view.
     *
     * @param FormView      $view    The form view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['multiple']) {
            foreach ($view->vars['choices'] as $choice) {
                if (@$choice->value === $view->vars['value']) {
                    $view->vars['value_label'] = $choice->label;
                }
            }
        }
    }

    public function getExtendedType()
    {
        return self::getExtendedTypes()[0];
    }

    public static function getExtendedTypes(): iterable
    {
        return ['Symfony\Component\Form\Extension\Core\Type\ChoiceType'];
    }
}
