<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\PaymentStatus;

use XLite\API\Endpoint\Order\DTO\PaymentStatus\OrderPaymentStatusOutput as OutputDTO;
use XLite\Model\Order\Status\Payment;

interface OutputTransformerInterface
{
    public function transform(Payment $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
