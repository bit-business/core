<template>
  <img
    v-if="adminPanelLogo && adminPanelLogo != ''"
    :src="adminPanelLogo"
    alt="adminPanelTitle"
    class="skijasi-logo-display"
  />
</template>

<script>
export default {
  name: "SkijasiLogoDisplay",
  components: {},
  data: () => ({}),
  computed: {
    adminPanelTitle: {
      get() {
        const config = this.$store.getters["skijasi/getConfig"];
        return config.adminPanelTitle ? config.adminPanelTitle : "Skijasi";
      },
    },
    adminPanelLogo: {
      get() {
        const config = this.$store.getters["skijasi/getConfig"];
        return config.adminPanelLogo;
      },
    },
    adminPanelHeaderColor: {
      get() {
        const config = this.$store.getters["skijasi/getConfig"];
        return config.adminPanelHeaderColor
          ? config.adminPanelHeaderColor
          : "#fff";
      },
    },
    adminPanelLogoConfig: {
      get() {
        const config = this.$store.getters["skijasi/getConfig"];
        return config.adminPanelLogoConfig
          ? config.adminPanelLogoConfig
          : "logo_and_text";
      },
    },
    adminPanelHeaderFontColor: {
      get() {
        const config = this.$store.getters["skijasi/getConfig"];
        return config.adminPanelHeaderFontColor
          ? config.adminPanelHeaderFontColor
          : "#000";
      },
    },
  },
};
</script>
