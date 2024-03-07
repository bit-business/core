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




    <!-- Loader with Image -->
    <skijasi-prompt
      :active.sync="loaderWithImage"
      buttons-hidden
      :title="title"
      type="confirm"
      class="skijasi-loader"
      :color="color"
      :headerColor="headerColor"
    >
      <div class="loader-image-container" v-if="loaderWithImage">
        <img src="/storage/slike/su_logo.png" alt="Loading..." class="loader-image">
      </div>
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
    loaderWithImage: false,
    title: "Loading",
    color: "primary",
    headerColor: null,
    loaderSync: false,
  }),
  methods: {
    openLoader(payload = null) {
      this.title = payload ? payload.title : "Učitavanje";
      this.color = payload ? payload.color : "primary";
      this.headerColor = payload ? payload.headerColor : null;
      this.loader = true;
    },
    openLoaderImage(payload = null) { // New method for loader with image
    this.title = payload ? payload.title : "Učitavanje";
    this.color = payload ? payload.color : "primary";
    this.headerColor = payload ? payload.headerColor : null;
    this.loaderWithImage = true; // Activate loader with image
  },
  closeLoader() {
    this.loader = false;
    this.loaderWithImage = false; // Ensure both loaders are deactivated
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

<style>
.loader-image-container {
  display: flex;
  justify-content: center;
}

.loader-image {
  width: 100px; /* Adjust as needed */
  animation: pulse 2s infinite ease-in-out;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(0.95);
  }
  50% {
    transform: scale(1.06); /* Adjust the scale factor as needed */
  }
}
</style>
