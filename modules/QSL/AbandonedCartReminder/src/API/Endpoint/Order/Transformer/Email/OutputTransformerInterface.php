<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\API\Endpoint\Order\Transformer\Email;

use QSL\AbandonedCartReminder\API\Endpoint\Order\DTO\Email\OrderAbandonedCartReminderEmailOutput as OutputDTO;
use QSL\AbandonedCartReminder\Model\Email;

interface OutputTransformerInterface
{
    public function transform(Email $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
