<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-email__container">
    <vs-input
      type="email"
      :label="displayLabel"
      :placeholder="placeholder"
      :disable="disabled"
      :readonly="readonly"
      :autofocus="autofocus"
      :value="value"
      @input="handleInput($event)"
      icon="email"
      icon-after
    />
    <div v-if="additionalInfo" v-html="additionalInfo"></div>
    <div v-if="alert">
      <div v-if="$helper.isArray(alert)">
        <span
          class="skijasi-email__input--error"
          v-for="(info, index) in alert"
          :key="index"
        >
          {{ info }}
        </span>
      </div>
      <div v-else>
        <span class="skijasi-email__input--error" v-html="alert"></span>
      </div>
    </div>
  </vs-col>
</template>

<script>
export default {
  name: "SkijasiEmail",
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
    additionalInfo: {
      type: String,
      default: "",
    },
    alert: {
      type: String || Array,
      default: "",
    },
    placeholder: {
      type: String,
      default: "Email",
    },
    value: {
      type: String,
      required: true,
      default: "",
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    autofocus: {
      type: Boolean,
      default: false,
    },
    required: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    displayLabel: function () {
      if (this.required) {
        return this.label + " *";
      } else {
        return this.label;
      }
    },
  },
  methods: {
    handleInput(val) {
      if (!this.validEmail(val)) {
        this.alert = "Please input correct email.";
      } else {
        this.alert = null;
      }
      this.$emit("input", val);
    },
    validEmail: function (email) {
      const re =
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },
  },
};
</script>
