<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList\Utils;

interface ViewListDocParserInterface
{
    public function parse(string $template): array;

    public function parseContent(string $content): array;
}
