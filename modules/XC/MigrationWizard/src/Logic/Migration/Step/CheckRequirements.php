<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Check Requirements
 */
class CheckRequirements extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    /**
     * Requirement
     *
     * @var \XC\MigrationWizard\Logic\Import\Processor\ARequirement
     */
    private $requirement;

    /**
     * Use requirement context for other methods
     *
     * @param string $method Method name
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        try {
            $result = call_user_func_array([$this->getRequirement(), $method], $args);

            return $result;
        } catch (Exception $ex) {
            \XLite\Core\TopMessage::addError(\XLite\Core\Translation::lbl('The database platform / version is not supported by migration wizard'));
            \XLite\Core\TopMessage::addError($ex->getMessage());
        }
    }

    /**
     * Get requirement
     *
     * @return \XC\MigrationWizard\Logic\Import\Processor\ARequirement
     */
    public function getRequirement()
    {
        if (empty($this->requirement)) {
            $rules = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRule')->findBy(
                [
                    'isSystem' => true
                ]
            );

            foreach ($rules as $rule) {
                $logic = new $rule->logic();

                if ($logic->isSupported()) {
                    $this->requirement = $logic;
                }
            }
        }

        // can be null here. So this still not fixed reason for errors like
        // XLite [warning] Warning: call_user_func_array() expects parameter 1 to be a valid callback, first array member is not a valid class name or object in src/classes/XLite/Module/XC/MigrationWizard/Logic/Migration/Step/CheckRequirements.php on line 38
        return $this->requirement;
    }

    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Check';
    }
}
