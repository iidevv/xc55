<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Category\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Category\DTO\Input as InputDTO;
use XLite\Model\Category;
use CDev\GoSocial\API\Endpoint\Category\DTO\Input as DecoratedInputDTO;
use CDev\GoSocial\Model\Category as DecoratedCategory;

/**
 * @Extender\Mixin
 */
class InputTransformer extends \XLite\API\Endpoint\Category\Transformer\InputTransformer
{
    /**
     * @param InputDTO|DecoratedInputDTO $object
     */
    public function transform($object, string $to, array $context = []): Category
    {
        /** @var DecoratedCategory $model */
        $model = parent::transform($object, $to, $context);

        $model->setUseCustomOG($object->og_meta_tags_type === 'C');
        $model->setOgMeta($object->og_meta_tags);

        return $model;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var DecoratedCategory $category */
        $category = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        /** @var DecoratedInputDTO $input */
        $input = parent::initialize($inputClass, $context);

        if (!$category) {
            return $input;
        }

        $input->og_meta_tags_type = $category->getUseCustomOG() ? 'C' : 'A';
        $input->og_meta_tags = $category->getOpenGraphMetaTags();

        return $input;
    }
}
