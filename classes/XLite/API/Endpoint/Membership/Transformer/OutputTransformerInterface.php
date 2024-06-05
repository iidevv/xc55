<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Membership\Transformer;

use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\Model\Membership;

interface OutputTransformerInterface
{
    public function transform(Membership $object, string $to, array $context = []): MembershipOutput;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
