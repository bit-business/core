<template>
  <div id="app">
    <router-view></router-view>
    <skijasi-prompt
      :active.sync="loader"
      buttons-hidden
      :title="title"
      type="confirm"
      class="skijasi-loader"
      :color="color"
      :headerColor="headerColor"
    >
      <br />
      <vs-progress indeterminate :color="color">primary</vs-progress>
    </skijasi-prompt>
    <skijasi-prompt
      :active.sync="loaderSync"
      buttons-hidden
      title="Synchron Data Loading"
      type="confirm"
      class="skijasi-loader"
      color="primary"
      headerColor="primary"
    >
      <br />
      <vs-progress indeterminate :color="color">primary</vs-progress>
    </skijasi-prompt>
  </div>
</template>

<script>
export default {
  name: "app",
  components: {},
  data: () => ({
    loader: false,
    title: "Loading",
    color: "primary",
    headerColor: null,
    loaderSync: false,
  }),
  methods: {
    openLoader(payload = null) {
      this.title = payload ? payload.title : "Loading";
      this.color = payload ? payload.color : "primary";
      this.headerColor = payload ? payload.headerColor : null;
      this.loader = true;
    },
    closeLoader() {
      this.loader = false;
    },
    syncLoader(loaderSyncStatus) {
      this.loaderSync = loaderSyncStatus;
    },
  },
  computed: {
    getSelectedLocale: {
      get() {
        return this.$store.getters["skijasi/getSelectedLocale"];
      },
    },
    verified: {
      get() {
        return this.$store.getters["skijasi/isVerified"];
      },
    },
    keyIssue: {
      get() {
        return this.$store.state.skijasi.keyIssue;
      },
    },
  },
  mounted() {
    this.$i18n.locale = this.getSelectedLocale.key;
    this.$store.commit("skijasi/FETCH_CONFIGURATION");
    this.$store.commit("skijasi/FETCH_FILE_CONFIGURATION");
  },
  beforeMount() {},
};
</script>
