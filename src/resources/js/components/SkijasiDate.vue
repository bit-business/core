<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-date__container">
    <label v-if="label != ''" for="" class="skijasi-date__label">{{
      label
    }}</label>
    <div class="skijasi-date__date-container">
      <datetime
        :label="label"
        type="date"
        :value="value"
        class="skijasi-date__input"
        @input="handleInput($event)"
      ></datetime>
      <div class="skijasi-date__date-icon-box">
        <vs-icon icon="calendar_today" class="skijasi-date__date-icon"></vs-icon>
      </div>
    </div>
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-date__input--error"
          v-for="(info, index) in alert"
          :key="index"
        >
          {{ info }}
        </span>
      </div>
      <div v-else>
        <span class="skijasi-date__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiDate",
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
      default: "Date",
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
