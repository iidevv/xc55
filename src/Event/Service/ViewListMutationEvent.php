<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Event\Service;

use Symfony\Contracts\EventDispatcher\Event;

final class ViewListMutationEvent extends Event
{
    public const TO_INSERT = 'to_insert';
    public const TO_REMOVE = 'to_remove';

    private string $versionKey = '';

    private array $mutations = [];

    public function addMutations(array $mutations): void
    {
        foreach ($mutations as $subject => $mutation) {
            $this->addMutation($subject, $mutation);
        }
    }

    /**
     * The mutation can be in the following formats:
     * 1. To remove from single list:
     * [
     *      {remove_definition}
     * ]
     * 2. To remove from single list and insert to single list (move)
     * [
     *      {remove_definition},
     *      {insert_definition}
     * ]
     * 3. To remove from several lists or/and insert to several lists
     * [
     *      'to_remove' => [{remove_definition}, ...],
     *      'to_insert'    => [{insert_definition}, ...]
     * ]
     * {remove_definition}: 'list_name' | ['list_name'{, 'interface'}]
     * {insert_definition}: 'list_name' | ['list_name'{, (int) weight{, 'interface'}}]
     *
     * @param string $subject  FQCN or Template relative path
     * @param array  $mutation Mutation definition
     */
    public function addMutation(string $subject, array $mutation): void
    {
        if (isset($mutation[self::TO_REMOVE]) || isset($mutation[self::TO_INSERT])) {
            $remove = $mutation[self::TO_REMOVE] ?? [];
            $insert = $mutation[self::TO_INSERT] ?? [];
        } elseif (count($mutation) === 1) {
            $remove = [is_array($mutation[0]) ? $mutation[0] : [$mutation[0]]];
            $insert = [];
        } else {
            $remove = [is_array($mutation[0]) ? $mutation[0] : [$mutation[0]]];
            $insert = [is_array($mutation[1]) ? $mutation[1] : [$mutation[1]]];
        }

        foreach ($remove as $value) {
            $this->addRemoveMutation($subject, $value);
        }

        foreach ($insert as $value) {
            $this->addInsertMutation($subject, $value);
        }
    }

    private function removePreviouslyAddedMutationsWithSameDefinition(string $subject, array $removeMutationToAdd): void
    {
        foreach ($this->mutations[$subject][self::TO_INSERT] ?? [] as $key => $previousInsertMutation) {
            unset($previousInsertMutation[1]); // unset "weight" property
            $previousInsertMutation = array_values($previousInsertMutation);

            if ($previousInsertMutation === $removeMutationToAdd) {
                unset($this->mutations[$subject][self::TO_INSERT][$key]);
                break;
            }
        }

        foreach ($this->mutations[$subject][self::TO_REMOVE] ?? [] as $key => $previousRemoveMutation) {
            if ($previousRemoveMutation === $removeMutationToAdd) {
                unset($this->mutations[$subject][self::TO_REMOVE][$key]);
                break;
            }
        }
    }

    public function addRemoveMutation(string $subject, array $definition = []): void
    {
        $removeMutation = $this->convertDefinitionToMutation($definition, 'remove');

        $this->removePreviouslyAddedMutationsWithSameDefinition($subject, $removeMutation);

        $this->mutations[$subject][self::TO_REMOVE][] = $removeMutation;
    }

    public function addInsertMutation(string $subject, array $definition = []): void
    {
        $this->mutations[$subject][self::TO_INSERT][] = $this->convertDefinitionToMutation($definition, 'insert');
    }

    private function convertDefinitionToMutation(array $definition, string $mutationType): array
    {
        $mutation = [];

        if ($definition) {
            $mutation = [$definition[0], $definition[1] ?? null, $definition[2] ?? null];

            if ($mutationType === 'insert') {
                $mutation[] = $definition[3] ?? null;
            }
        }

        return $mutation;
    }

    public function getVersionKey(): string
    {
        return $this->versionKey;
    }

    public function setVersionKey(string $versionKey): void
    {
        $this->versionKey = $versionKey;
    }

    public function getMutations(): array
    {
        return $this->mutations;
    }

    public function setMutations(array $mutations): void
    {
        $this->mutations = $mutations;
    }
}
