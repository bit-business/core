<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-url__container">
    <vs-input
      type="url"
      :label="label"
      :placeholder="placeholder"
      :value="value"
      @input="handleInput($event)"
      icon="http"
      icon-after
    />
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-url__input--error"
          v-for="(info, index) in alert"
          :key="index"
          >{{ info }}</span
        >
      </div>
      <div v-else>
        <span class="skijasi-url__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiUrl",
  components: {},
  data: () => ({}),
  props: {
    size: {
      type: String,
      default: "12",
    },
    label: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "URL",
    },
    value: {
      type: String,
      required: true,
      default: "",
    },
    additionalInfo: {
      type: String,
      default: "",
    },
    alert: {
      type: String || Array,
      default: "",
    },
  },
  methods: {
    handleInput(val) {
      this.$emit("input", val);
    },
  },
};
</script>
