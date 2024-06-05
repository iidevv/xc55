<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\API\Endpoint\CustomProductTab\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use XC\CustomProductTabs\API\Endpoint\CustomProductTab\DTO\Input as InputDTO;
use XC\CustomProductTabs\API\Resource\CustomProductTab;
use XLite\Core\Database;
use XLite\Model\Product\GlobalTab;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): CustomProductTab
    {
        /** @var CustomProductTab $resource */
        $resource = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new CustomProductTab();
        $resource->name = $object->name;
        $resource->content = $object->content;
        $resource->brief_info = $object->brief_info;

        return $resource;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        $tabId = $this->getTabId($context);
        if ($tabId) {
            /** @var \XC\CustomProductTabs\Model\Product\GlobalTab $model */
            $model = Database::getRepo(GlobalTab::class)->find($tabId);
            if (!$model) {
                return new InputDTO();
            }
        } else {
            return new InputDTO();
        }

        $input             = new InputDTO();
        $input->name       = $model->getCustomTab()->getName();
        $input->content    = $model->getCustomTab()->getContent();
        $input->brief_info = $model->getCustomTab()->getBriefInfo();

        return $input;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof CustomProductTab) {
            return false;
        }

        return $to === CustomProductTab::class && ($context['input']['class'] ?? null) !== null;
    }

    protected function getTabId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/custom_product_tabs\/(\d+)/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
