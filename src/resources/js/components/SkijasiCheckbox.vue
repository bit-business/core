<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-checkbox__container">
    <label v-if="label != ''" for="" class="skijasi-checkbox__label">{{
      label
    }}</label>
    <ul class="skijasi-checkbox__list">
      <li
        class="skijasi-checkbox__list-item"
        v-for="item in items"
        :key="item.value"
      >
        <vs-checkbox
          class="skijasi-checkbox__checkbox"
          :value="value"
          @input="handleInput($event)"
          :vs-value="item.value"
        >
          {{ item.label }}
        </vs-checkbox>
      </li>
    </ul>
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-checkbox__checkbox--error"
          v-for="(info, index) in alert"
          :key="index"
        >
          {{ info }}
        </span>
      </div>
      <div v-else>
        <span class="skijasi-checkbox__checkbox--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiCheckbox",
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
      default: "Checkbox",
    },
    value: {
  type: [Boolean, Number],
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
