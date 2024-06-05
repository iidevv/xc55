<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<script>
export default {
  methods: {
    queryRemove(remove) {
      const query = JSON.parse(
        JSON.stringify(this.$route.query || {}),
      );
      this.$router.push(
        { query: _.omit(query, remove) },
      );
    },
    queryUpdate(update) {
      const query = JSON.parse(
        JSON.stringify(this.$route.query || {}),
      );
      this.$router.push(
        { query: Object.assign(query, update) },
      );
    },
    queryPatch(update, remove, path) {
      let query = JSON.parse(
        JSON.stringify(this.$route.query || {}),
      );
      query = _.omit(query, remove);

      const route = { query: Object.assign(query, update) };
      if (path) {
        route.path = path;
      }

      this.$router.push(route);
    },
  },
};
</script>
