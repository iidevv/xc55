<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Membership\Transformer;

use XLite\API\Endpoint\Membership\DTO\MembershipInput as InputDTO;
use XLite\Model\Membership;

interface InputTransformerInterface
{
    public function transform(InputDTO $object, string $to, array $context = []): Membership;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
