<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('edit_users')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("user.edit.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="user.name"
              size="6"
              :label="$t('user.edit.field.name.title')"
              :placeholder="$t('user.edit.field.name.placeholder')"
              :alert="errors.name"
            ></skijasi-text>
            <skijasi-text
              v-model="user.username"
              size="6"
              :label="$t('user.edit.field.username.title')"
              :placeholder="$t('user.edit.field.username.placeholder')"
              :alert="errors.username"
            ></skijasi-text>
            <skijasi-text
              v-model="user.email"
              size="6"
              :label="$t('user.edit.field.email.title')"
              :placeholder="$t('user.edit.field.email.placeholder')"
              :alert="errors.email"
            ></skijasi-text>
            <skijasi-password
              v-model="user.password"
              size="6"
              :label="$t('user.edit.field.password.title')"
              :placeholder="$t('user.edit.field.password.placeholder')"
              :alert="errors.password"
            ></skijasi-password>
            <skijasi-switch
              v-model="user.emailVerified"
              size="6"
              :label="$t('user.edit.field.emailVerified.title')"
              :placeholder="$t('user.edit.field.emailVerified.placeholder')"
              :alert="errors.emailVerified"
              onLabel="Yes"
              offLabel="No"
              :tooltip="$t('user.help.emailVerified')"
            ></skijasi-switch>
            <skijasi-upload-image
              v-model="user.avatar"
              size="12"
              :label="$t('user.edit.field.avatar.title')"
              :placeholder="$t('user.edit.field.avatar.placeholder')"
              :alert="errors.avatar"
            ></skijasi-upload-image>
            <vs-col vs-lg="12">
              <skijasi-code-editor
                v-model="user.additionalInfo"
                size="12"
                :label="$t('user.edit.field.additionalInfo.title')"
                :placeholder="$t('user.edit.field.additionalInfo.placeholder')"
                :alert="errors.additionalInfo"
              ></skijasi-code-editor>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> {{ $t("user.edit.button") }}
              </vs-button>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "UserManagementEdit",
  components: {},
  data: () => ({
    errors: {},
    user: {
      email: "",
      name: "",
      username: "",
      avatar: "",
      password: "",
      emailVerified: false,
      additionalInfo: "",
    },
  }),
  computed: {
    loggedInUser: {
      get() {
        const user = this.$store.getters["skijasi/getUser"];
        return user;
      },
    },
  },
  mounted() {
    this.getUserDetail();
  },
  methods: {
    getUserDetail() {
      this.$openLoader();
      this.$api.skijasiUser
        .read({
          id: this.$route.params.id,
        })
        .then((response) => {
          this.$closeLoader();
          this.user = response.data.user;
          this.user.password = "";
          this.user.additionalInfo = this.user.additionalInfo
            ? JSON.parse(this.user.additionalInfo)
            : "";
        })
        .catch((error) => {
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    submitForm() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiUser
        .edit({
          id: this.$route.params.id,
          email: this.user.email,
          name: this.user.name,
          username: this.user.username,
          avatar: this.user.avatar ? this.user.avatar.base64 : null,
          password: this.user.password,
          emailVerified: this.user.emailVerified,
          additionalInfo: JSON.stringify(this.user.additionalInfo),
        })
        .then((response) => {
          if (this.loggedInUser.id == this.user.id) {
            this.$store.commit("skijasi/FETCH_USER");
          }
          this.$closeLoader();
          this.$router.push({ name: "UserManagementBrowse" });
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
