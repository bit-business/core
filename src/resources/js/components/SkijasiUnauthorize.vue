<template>
  <vs-popup
    :title="$t('authorizationIssue.title')"
    :active.sync="unauthorize"
    class="skijasi-unauthorize__container"
  >
    <vs-row>
      <vs-col>
        <p class="skijasi-unauthorize__title">
          {{ $t("authorizationIssue.subtitle") }}
        </p>
        <div>
          <h3 class="skijasi-unauthorize__message">
            {{ $t("authorizationIssue.message") }}
          </h3>
        </div>
      </vs-col>
    </vs-row>
    <vs-row>
      <vs-col class="skijasi-unauthorize__button">
        <vs-button type="relief" @click="login()">{{
          $t("login.button")
        }}</vs-button>
      </vs-col>
    </vs-row>
  </vs-popup>
</template>

<script>
export default {
  name: "SkijasiUnauthorize",
  components: {},
  data: () => ({}),
  mounted() {
    this.$store.commit("skijasi/SET_AUTH_ISSUE", {
      unauthorized: false,
    });
  },
  computed: {
    unauthorize: {
      get() {
        return this.$store.state.skijasi.authorizationIssue
          ? this.$store.state.skijasi.authorizationIssue.unauthorized
          : false;
      },
      set(val) {
        if (val === false) {
          this.$store.commit("skijasi/LOGOUT");
        }
      },
    },
    authorizationIssue: {
      get() {
        return this.$store.state.skijasi.authorizationIssue;
      },
    },
  },
  methods: {
    login() {
      this.$store.commit("skijasi/LOGOUT");
    },
  },
};
</script>
