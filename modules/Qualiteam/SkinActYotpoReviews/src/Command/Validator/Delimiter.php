<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command\Validator;

class Delimiter implements IValidator
{
    public function __construct(
        private string $delimiter
    ) {
    }

    /**
     * @throws ValidatorException
     */
    public function valid(): void
    {
        $this->checkDelimiterCharacters();
    }

    /**
     * @throws ValidatorException
     */
    private function checkDelimiterCharacters(): void
    {
        if (strlen($this->delimiter) <> 1) {
            throw new ValidatorException("The maximum characters limit for the delimiter must be one character. Input delimiter is: " . $this->delimiter);
        }
    }
}
