<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row>
      <vs-col vs-lg="12">
        <vs-card>
          <vs-tabs :color="adminPanelHeaderFontColor">
            <vs-tab :label="$t('myProfile.profile')">
              <vs-row class="profile__container">
                <skijasi-text
                  v-model="user.name"
                  size="12"
                  :label="$t('myProfile.name')"
                  :placeholder="$t('myProfile.name')"
                  :alert="errors.name"
                ></skijasi-text>
                <skijasi-text
                  v-model="user.username"
                  size="12"
                  :label="$t('myProfile.username')"
                  :placeholder="$t('myProfile.username')"
                  :alert="errors.username"
                ></skijasi-text>
                <skijasi-upload-image
                  v-model="user.avatar"
                  size="12"
                  :label="$t('myProfile.avatar')"
                  :placeholder="$t('myProfile.avatar')"
                  :alert="errors.avatar"
                ></skijasi-upload-image>
                <vs-col vs-lg="12">
                  <skijasi-code-editor
                    v-model="user.additionalInfo"
                    size="12"
                    :label="$t('myProfile.additionalInfo')"
                    :placeholder="$t('myProfile.additionalInfo')"
                    :alert="errors.additionalInfo"
                  ></skijasi-code-editor>
                </vs-col>
                <vs-col vs-lg="12">
                  <vs-button
                    color="primary"
                    type="relief"
                    @click="updateProfile"
                  >
                    <vs-icon icon="save"></vs-icon>
                    {{ $t("myProfile.buttons.updateProfile") }}
                  </vs-button>
                </vs-col>
              </vs-row>
            </vs-tab>
            <vs-tab :label="$t('myProfile.email')">
              <vs-row class="profile__container" v-if="shouldVerifyEmail">
                <skijasi-text
                  v-model="token"
                  size="12"
                  :label="$t('myProfile.token')"
                  :placeholder="$t('myProfile.token')"
                  :alert="errors.token"
                ></skijasi-text>
                <vs-col vs-lg="12">
                  <vs-button color="primary" type="relief" @click="verifyEmail">
                    <vs-icon icon="save"></vs-icon>
                    {{ $t("myProfile.buttons.verifyEmail") }}
                  </vs-button>
                </vs-col>
              </vs-row>
              <vs-row class="profile__container" v-else>
                <skijasi-email
                  v-model="user.email"
                  size="12"
                  :label="$t('myProfile.email')"
                  :placeholder="$t('myProfile.email')"
                  :alert="errors.email"
                ></skijasi-email>
                <vs-col vs-lg="12">
                  <vs-button color="primary" type="relief" @click="updateEmail">
                    <vs-icon icon="save"></vs-icon>
                    {{ $t("myProfile.buttons.updateEmail") }}
                  </vs-button>
                </vs-col>
              </vs-row>
            </vs-tab>
            <vs-tab :label="$t('myProfile.password')">
              <vs-row class="profile__container">
                <skijasi-password
                  v-model="user.oldPassword"
                  size="12"
                  :label="$t('myProfile.oldPassword')"
                  :placeholder="$t('myProfile.oldPassword')"
                  :alert="errors.oldPassword"
                ></skijasi-password>
                <skijasi-password
                  v-model="user.newPassword"
                  size="12"
                  :label="$t('myProfile.newPassword')"
                  :placeholder="$t('myProfile.newPassword')"
                  :alert="errors.newPassword"
                ></skijasi-password>
                <skijasi-password
                  v-model="user.newPasswordConfirmation"
                  size="12"
                  :label="$t('myProfile.newPasswordConfirmation')"
                  :placeholder="$t('myProfile.newPasswordConfirmation')"
                  :alert="errors.newPasswordConfirmation"
                ></skijasi-password>
                <vs-col vs-lg="12">
                  <vs-button
                    color="primary"
                    type="relief"
                    @click="changePassword"
                  >
                    <vs-icon icon="save"></vs-icon>
                    {{ $t("myProfile.buttons.changePassword") }}
                  </vs-button>
                </vs-col>
              </vs-row>
            </vs-tab>
          </vs-tabs>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "UserProfile",
  components: {},
  data: () => ({
    errors: {},
    user: {
      email: "",
      name: "",
      username: "",
      avatar: "",
      additionalInfo: "",
      oldPassword: "",
      newPassword: "",
      newPasswordConfirmation: "",
    },
    token: "",
    shouldVerifyEmail: false,
  }),
  mounted() {
    this.getUser();
  },
  computed: {
    adminPanelHeaderFontColor: {
      get() {
        return "#06bbd3";
      },
    },
  },
  methods: {
    getUser() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .user({})
        .then((response) => {
          this.$closeLoader();
          this.user = response.data.user;
          this.user.additionalInfo = this.user.additionalInfo
            ? JSON.parse(this.user.additionalInfo)
            : "";
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    updateProfile() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .updateProfile({
          name: this.user.name,
          username: this.user.username,
          avatar: this.user.avatar,
          additionalInfo:
            this.user.additionalInfo !== ""
              ? JSON.stringify(this.user.additionalInfo)
              : null,
        })
        .then((response) => {
          this.$store.commit("skijasi/FETCH_USER");
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.success"),
            text: "Profil je ažuriran",
            color: "success",
          });
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    updateEmail() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .updateEmail({
          email: this.user.email,
        })
        .then((response) => {
          this.shouldVerifyEmail = response.data.shouldVerifyEmail;
          if (this.shouldVerifyEmail) {
            this.$vs.notify({
              title: this.$t("alert.success"),
              text: response.data.message,
              color: "success",
            });
          } else {
            this.$store.commit("skijasi/FETCH_USER");
            this.$vs.notify({
              title: this.$t("alert.success"),
              text: "Email updated",
              color: "success",
            });
          }
          this.$closeLoader();
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    verifyEmail() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .verifyEmail({
          email: this.user.email,
          token: this.token,
        })
        .then((response) => {
          this.$store.commit("skijasi/FETCH_USER");
          this.shouldVerifyEmail = false;
          this.token = "";
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.success"),
            text: "Email updated",
            color: "success",
          });
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    changePassword() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiAuthUser
        .changePassword({
          oldPassword: this.user.oldPassword,
          newPassword: this.user.newPassword,
          newPasswordConfirmation: this.user.newPasswordConfirmation,
        })
        .then((response) => {
          this.$closeLoader();
          this.user.oldPassword = "";
          this.user.newPassword = "";
          this.user.newPasswordConfirmation = "";
          this.$vs.notify({
            title: this.$t("alert.success"),
            text: "Password updated",
            color: "success",
          });
        })
        .catch((error) => {
          this.errors = error.errors;
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
  },
};
</script>
