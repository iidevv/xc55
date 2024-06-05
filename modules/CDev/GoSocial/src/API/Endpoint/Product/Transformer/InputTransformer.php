<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\API\Endpoint\Product\Transformer;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Input as InputDTO;
use XLite\Model\Product;
use CDev\GoSocial\API\Endpoint\Product\DTO\Input as DecoratedInputDTO;
use CDev\GoSocial\Model\Product as DecoratedProduct;

/**
 * @Extender\Mixin
 */
class InputTransformer extends \XLite\API\Endpoint\Product\Transformer\InputTransformer
{
    /**
     * @param InputDTO|DecoratedInputDTO $object
     */
    public function transform($object, string $to, array $context = []): Product
    {
        /** @var DecoratedProduct $model */
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
        /** @var DecoratedProduct $product */
        $product = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        /** @var DecoratedInputDTO $input */
        $input = parent::initialize($inputClass, $context);

        if (!$product) {
            return $input;
        }

        $input->og_meta_tags_type = $product->getUseCustomOG() ? 'C' : 'A';
        $input->og_meta_tags = $product->getOpenGraphMetaTags();

        return $input;
    }
}
