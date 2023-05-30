<template>
  <vs-row class="skijasi-auth-card-header__container">
    <vs-col vs-lg="6" class="skijasi-auth-card-header__title">
      <h3 class="skijasi-auth-card-header__title--text">
        <slot />
      </h3>
    </vs-col>
    <vs-col vs-lg="6" class="skijasi-auth-card-header__logo">
      <skijasi-logo-display />
    </vs-col>
  </vs-row>
</template>

<script>
export default {
  name: "SkijasiAuthCardHeader",
  components: {},
  data: () => ({}),
  computed: {},
};
</script>
