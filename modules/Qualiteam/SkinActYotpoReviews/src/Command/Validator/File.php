<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command\Validator;

class File implements IValidator
{
    public function __construct(
        private string $dir
    ) {
    }

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Command\Validator\ValidatorException
     */
    public function valid(): void
    {
        $this->checkDir();
        $this->checkWritableFile();
    }

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Command\Validator\ValidatorException
     */
    public function checkDir(): void
    {
        if (!is_dir($this->dir)) {
            throw new ValidatorException('Directory does not exist. Path: ' . $this->dir);
        }
    }

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Command\Validator\ValidatorException
     */
    public function checkWritableFile(): void
    {
        if (!is_writable($this->dir)) {
            throw new ValidatorException('Directory does not have permissions to write. Path: ' . $this->dir);
        }
    }
}
