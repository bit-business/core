<template>
  <vs-prompt
    :title="$t('keyIssue.title')"
    :active.sync="show"
    @close="show = true"
    buttons-hidden
    class="skijasi-license-blocker__container"
  >
    <vs-row>
      <vs-col>
        <p>{{ $t("keyIssue.message") }}</p>
        <h3 class="skijasi-license-blocker__title">{{ keyIssue.message }}</h3>
        <br />
        <p>{{ $t("keyIssue.listTitle") }}</p>
        <ul>
          <li>
            <strong>{{ $t("keyIssue.licenseEmpty") }}</strong>
            <p>
              {{ $t("keyIssue.licenseEmptyDescription") }}
              <a
                href="https://skijasi-docs.nadzorserveraweb.hr/docs/en/getting-started/installation/"
                target="_blank"
              >
                <vs-icon icon="open_in_new"></vs-icon>
              </a>
            </p>
          </li>
          <li>
            <strong>{{ $t("keyIssue.licenseInvalid") }}</strong>
            <p>
              {{ $t("keyIssue.licenseInvalidDescription") }}
              <a
                href="https://skijasi-docs.nadzorserveraweb.hr/docs/en/getting-started/installation/"
                target="_blank"
              >
                <vs-icon icon="open_in_new"></vs-icon>
              </a>
            </p>
          </li>
          <li>
            <strong>{{ $t("keyIssue.licenseUsersExpired") }}</strong>
            <p>
              {{ $t("keyIssue.licenseUsersExpiredDescription") }}
              <a
                href="https://skijasi-docs.nadzorserveraweb.hr/docs/en/getting-started/installation/"
                target="_blank"
              >
                <vs-icon icon="open_in_new"></vs-icon>
              </a>
            </p>
          </li>
        </ul>
      </vs-col>
    </vs-row>
  </vs-prompt>
</template>

<script>
export default {
  name: "SkijasiLicenseBlocker",
  components: {},
  data: () => ({
    show: true,
  }),
  mounted() {},
  computed: {
    keyIssue: {
      get() {
        return this.$store.state.skijasi.keyIssue;
      },
    },
  },
};
</script>
