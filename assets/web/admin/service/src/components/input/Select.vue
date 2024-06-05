<!--suppress HtmlUnknownTarget -->
<template>
  <div class="input-select">
    <label :for="id">{{ label }}</label>
    <select
      :id="id"
      :value="value"
      @change="onChange">
      <option
        v-for="(option, index) in options"
        :key="index"
        :value="option.value">{{ option.label }}</option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'InputSelect',
  props: {
    label: {
      type: String,
      default: '',
    },
    value: {
      type: String,
      default: '',
    },
    options: {
      type: Array,
      default: () => [],
    },
    disabled: {
      type: Boolean,
      default() {
        return false;
      },
    },
  },
  computed: {
    id() {
      return Math.random().toString(36).substr(2, 9);
    },
  },
  methods: {
    onChange(event) {
      const value = event.target.value;
      if (!this.disabled) {
        this.$emit('selected', value, this.value);
      }
    },
  },
};
</script>
