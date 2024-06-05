<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor\View\FormModel\Type;

use XCart\Extender\Mapping\Extender;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Froala Editor wrapper for vue forms
 * @Extender\Mixin
 */
class TextareaAdvancedType extends \XLite\View\FormModel\Type\TextareaAdvancedType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\OldType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'oldType' => 'XLite\View\FormField\Textarea\Advanced',
        ]);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'input_grid'   => 'froala-widget-column',
            'fieldOptions' => array_replace(
                $view->vars['fieldOptions'],
                [
                    'attributes' => [
                        'v-model' => $view->vars['v_model'],
                        '@input' => 'onFormInputChange',
                    ],
                    'value'      => $view->vars['value'],
                ]
            ),
        ]);
    }
}
