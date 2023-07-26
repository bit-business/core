<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-radio__container">
    <label v-if="label != ''" for="" class="skijasi-radio__label">{{
      label
    }}</label>
    <ul class="skijasi-radio__list">
      <li
        class="skijasi-radio__list-item"
        v-for="item in items"
        :key="item.value"
      >
        <vs-radio
          :value="value"
          @input="handleInput($event)"
          :vs-value="item.value"
        >
          {{ item.label }}
        </vs-radio>
      </li>
    </ul>
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-radio__input--error"
          v-for="(info, index) in alert"
          :key="index"
        >
          {{ info }}
        </span>
      </div>
      <div v-else>
        <span class="skijasi-radio__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiRadio",
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
      default: "Radio",
    },
    value: {
      type: String,
      required: true,
    },
    items: {
      type: Array,
      required: true,
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
