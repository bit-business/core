<template>
  <vs-col :vs-lg="size" vs-xs="12" class="skijasi-date__container">
    <label v-if="label != ''" for="" class="skijasi-date__label">{{ label }}</label>
    <div class="skijasi-date__date-container">
      <datepicker
      v-model="innerValue"
        :format="dateFormat"
        :placeholder="placeholder"
        @input="handleInput"
        class="skijasi-date__input"
      ></datepicker>
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
import Datepicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "SkijasiDate",
  components: {
    Datepicker,
  },
  data: () => ({

    dateFormat: 'DD-MM-YYYY' // Adjust the format as per your requirement
  }),
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
      default: "Datum",
    },
    value: {
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
  },
  computed: {
  innerValue: {
    get() {
      return this.value ? new Date(this.value) : null;
    },
    set(val) {
      if (!val) {
        this.$emit("input", null);
      } else {
        const selectedDate = new Date(val);
        const formattedDate = `${selectedDate.getFullYear()}-${(selectedDate.getMonth() + 1).toString().padStart(2, '0')}-${selectedDate.getDate().toString().padStart(2, '0')}`;
        this.$emit("input", formattedDate);
      }
    }
  }
},
methods: {
  handleInput(date) {
    if (date == null || date === '') {
      this.$emit("input", null);
    } else {
      const selectedDate = new Date(date);
      const formattedDate = `${selectedDate.getFullYear()}-${(selectedDate.getMonth() + 1).toString().padStart(2, '0')}-${selectedDate.getDate().toString().padStart(2, '0')}`;
      this.$emit("input", formattedDate);
    }
  },
},
};
</script>





