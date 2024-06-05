<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Membership\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\Model\Membership;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Membership $object
     */
    public function transform($object, string $to, array $context = []): MembershipOutput
    {
        $output = new MembershipOutput();
        $output->id = $object->getMembershipId();
        $output->name = $object->getName();
        $output->enabled = $object->getEnabled();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === MembershipOutput::class && $data instanceof Membership;
    }
}
