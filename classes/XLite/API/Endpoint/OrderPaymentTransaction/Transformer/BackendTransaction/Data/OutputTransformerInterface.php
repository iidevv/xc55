<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderPaymentTransaction\Transformer\BackendTransaction\Data;

use XLite\API\Endpoint\OrderPaymentTransaction\DTO\BackendTransaction\Data\OrderPaymentTransactionBackendTransactionDataOutput as OutputDTO;
use XLite\Model\Payment\BackendTransactionData;

interface OutputTransformerInterface
{
    public function transform(BackendTransactionData $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
