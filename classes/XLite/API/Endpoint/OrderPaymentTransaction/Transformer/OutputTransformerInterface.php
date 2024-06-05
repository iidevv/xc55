<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer;

use XLite\API\Endpoint\OrderPaymentTransaction\DTO\OrderPaymentTransactionOutput as OutputDTO;
use XLite\Model\Payment\Transaction;

interface OutputTransformerInterface
{
    public function transform(Transaction $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
