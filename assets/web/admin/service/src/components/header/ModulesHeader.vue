<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div class="page-header">
    <i18n
      :path="title"
      tag="h1"/>

    <component
      :is="controlsType"
      :filter="actualFilter"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import PageHeader from './PageHeader';
import ModulesControls from './controls/ModulesControls';

export default {
  name: 'ModulesListHeader',
  components: {
    ModulesControls,
  },
  extends: PageHeader,
  props: {
    controlsType: {
      type: String,
      default: '',
    },
  },
  computed: {
    ...mapState({
      actualFilter: state => state.modulesData.filter,
    }),
  },
};
</script>
