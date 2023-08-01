<template>
  <div class="skijasi-loading-page__wrapper">
    <vs-row class="skijasi-loading-page__container">
      <vs-col vs-lg="12" class="skijasi-loading-page__box">
        <vs-card class="skijasi-loading-page__card">
          <div slot="header">
            <h3 class="skijasi-loading-page__title">{{ title }}</h3>
          </div>
          <div>
            <vs-progress indeterminate color="primary">primary</vs-progress>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "SkijasiLoadingPage",
  components: {},
  props: {
    title: {
      type: String,
      default: "Molimo pričekajte...",
    },
  },
  data: () => ({}),
  mounted() {},
  computed: {},
};
</script>
