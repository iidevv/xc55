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
 * @Extender\Mixin
 */
class MailTextareaAdvancedType extends \XLite\View\FormModel\Type\MailTextareaAdvancedType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return $this->useAsEmailEidtor()
            ? 'XLite\View\FormModel\Type\OldType'
            : parent::getParent();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        if ($this->useAsEmailEidtor()) {
            $resolver->setDefaults([
                'oldType' => 'XC\FroalaEditor\View\FormField\Textarea\MailAdvanced',
            ]);
        } else {
            parent::configureOptions($resolver);
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($this->useAsEmailEidtor()) {
            $view->vars = array_replace($view->vars, [
                'input_grid'   => 'froala-widget-column',
                'fieldOptions' => array_replace(
                    $view->vars['fieldOptions'],
                    [
                        'attributes' => [
                            'v-model' => $view->vars['v_model'],
                            'id'      => '',
                        ],
                        'value'      => $view->vars['value'],
                    ]
                ),
            ]);
        } else {
            parent::buildView($view, $form, $options);
        }
    }

    /**
     * @return boolean
     */
    protected function useAsEmailEidtor()
    {
        return !empty(\XLite\Core\Config::getInstance()->XC->FroalaEditor->use_as_email_editor);
    }
}
