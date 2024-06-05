<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer\Data;

use XLite\API\Endpoint\OrderPaymentTransaction\DTO\Data\OrderPaymentTransactionDataOutput as OutputDTO;
use XLite\Model\Payment\TransactionData;

interface OutputTransformerInterface
{
    public function transform(TransactionData $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
