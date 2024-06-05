<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\View\FormModel\Type;

use Qualiteam\SkinActFullWidthImages\View\FormField\FileUploader\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;
use XLite\View\FormModel\Type\UploaderType as UploaderTypeParent;

class UploaderType extends AType
{
    public function getParent()
    {
        return UploaderTypeParent::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['uploaderClass' => Image::class]);
    }
}