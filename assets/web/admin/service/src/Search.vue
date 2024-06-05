<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div id="search-bar">
    <search-bar/>
  </div>
</template>

<script>
import SearchBar from './components/block/SearchBar';

export default {
  name: 'Search',
  components: {
    SearchBar,
  },
};
</script>
