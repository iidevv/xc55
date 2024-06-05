<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div>
    <component
      v-if="isLink"
      :is="component"
      :tag="linkTag"
      :class="styleFullClass"
      :to="linkTo"
      @click.native="clickHandler">
      <slot>{{ module.moduleName }}</slot>
    </component>
    <component
      v-else
      :is="component"
      :tag="linkTag"
      :class="styleFullClass">
      <slot>{{ module.moduleName }}</slot>
    </component>
  </div>
</template>

<script>
export default {
  props: {
    module: {
      type: Object,
      default: () => {},
    },
    tag: {
      type: String,
      default: 'div',
    },
    styleClass: {
      type: String,
      default: 'module-link',
    },
  },
  data() {
    return {
      linkTo: (this.isLink && !this.isIframe) ? { query: this.moduleQuery } : {},
    };
  },
  computed: {
    id() {
      return this.module.id;
    },
    isLink() {
      return this.id !== 'CDev-Core';
    },
    isIframe() {
      return this.$route.matched.some(m => m.name === 'iframe');
    },
    component() {
      return this.isLink ? 'router-link' : this.tag;
    },
    linkTag() {
      return this.isLink ? this.tag : null;
    },
    moduleQuery() {
      const query = JSON.parse(JSON.stringify(this.$route.query || {}));
      return Object.assign(query, { moduleId: this.id });
    },
    styleFullClass() {
      const styleClass = {
        link: this.isLink,
      };

      styleClass[this.styleClass] = true;

      return styleClass;
    },
  },
  methods: {
    clickHandler(e) {
      if (this.isIframe) {
        const resolved = this.$router.resolve({
          path: '/',
          query: {
            moduleId: this.module.id,
          },
        });

        window.top.location.href = resolved.href;
      } else {
        if (e.target.closest('.module-alert')) {
          return;
        }

        const messages = [];

        if (
          this.module.type === 'major'
          || this.module.type === 'minor'
        ) {
          const message = {
            message: 'module_state_message.update_available',
            type: 'success',
            params: {
              type: this.module.type,
              version: `${this.module.version.major}.${this.module.version.minor}.${this.module.version.build}`,
            },
          };

          messages.push(message);
        }

        this.$store.commit('singleModule/SET_ID', this.module.id);
        this.$store.dispatch('singleModule/fetchModuleData', { stateToSet: this.module.scenarioState, messages });
      }
    },
  },
};
</script>
