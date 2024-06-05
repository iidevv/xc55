<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\Extension\Core\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

class SwitcherType extends AType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setData($options['data'] ?? false);
        $builder->addViewTransformer(new BooleanToStringTransformer($options['value']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'value'       => $options['value'],
            'checked'     => $form->getViewData() !== null,
            'on_caption'  => $options['on_caption'],
            'off_caption' => $options['off_caption'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $emptyData = static function (FormInterface $form, $viewData) {
            return $viewData;
        };

        $resolver->setDefaults([
            'value'       => '1',
            'empty_data'  => $emptyData,
            'compound'    => false,
            'on_caption'  => static::t('Switcher YES'),
            'off_caption' => static::t('Switcher NO'),
        ]);
    }
}
