<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <controls>
    <GoToExternalAppStoreLink/>
    <ActivateLicenseKeyButton/>
    <div class="filters">
      <div class="modules-list-filters">
        <dropdown-select
          :value="filter.sortBy"
          :options="filterOptions"
          @selected="filterHandler"/>
      </div>
    </div>
  </controls>
</template>

<script>
import Controls from './Controls';
import DropdownSelect from '../../input/DropdownSelect';
import ActivateLicenseKeyButton from '../../block/ActivateLicenseKeyButton';
import GoToExternalAppStoreLink from '../../block/GoToExternalAppStoreLink';

export default {
  name: 'ModulesControls',
  components: {
    Controls,
    DropdownSelect,
    ActivateLicenseKeyButton,
    GoToExternalAppStoreLink,
  },
  extends: Controls,
  props: {
    filter: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    filterOptions() {
      return [
        { value: '', label: 'modules-list-controls.installed.all' },
        { value: 'enabled', label: 'Only enabled' },
        { value: 'disabled', label: 'Only disabled' },
        { value: 'recent', label: 'modules-list-controls.installed.recent' },
      ];
    },
  },
  methods: {
    filterHandler(sortBy) {
      if (sortBy) {
        this.queryPatch({ sortBy }, 'limit');
      } else {
        this.queryRemove(['sortBy', 'limit']);
      }
    },
  },
};
</script>
