<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-textarea__container">
    <label v-if="label != ''" for="" class="skijasi-textarea__label">{{
      label
    }}</label>
    <vs-textarea :value="value" @input="handleInput($event)" />
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-textarea__input--error"
          v-for="(info, index) in alert"
          :key="index"
        >
          {{ info }}
        </span>
      </div>
      <div v-else>
        <span class="skijasi-textarea__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiTextarea",
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
      default: "Textarea",
    },
    value: {
      required: true,
      default: "null",
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
