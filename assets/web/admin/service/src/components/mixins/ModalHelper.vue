<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<script>
let modalData = {};

export default {
  methods: {
    showModal(type, title, description, data = {}) {
      modalData = JSON.parse(data);
      this.$modal.show(type, {
        title,
        text: description,
        textArgs: modalData,
        list: modalData,
      });
      this.$store.commit('setLastOpenedModal', type);
    },
    hideModal() {
      this.$modal.hide(this.$store.getters.lastOpenedModal);
    },
  },
};
</script>
