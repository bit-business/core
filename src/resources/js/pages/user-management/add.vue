<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_users')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("user.add.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="user.name"
              size="6"
              :label="$t('user.add.field.name.title')"
              :placeholder="$t('user.add.field.name.placeholder')"
              :alert="errors.name"
            ></skijasi-text>
            <skijasi-text
              v-model="user.username"
              size="6"
              :label="$t('user.add.field.username.title')"
              :placeholder="$t('user.add.field.username.placeholder')"
              :alert="errors.username"
            ></skijasi-text>
            <skijasi-text
              v-model="user.email"
              size="6"
              :label="$t('user.add.field.email.title')"
              :placeholder="$t('user.add.field.email.placeholder')"
              :alert="errors.email"
            ></skijasi-text>
            <skijasi-password
              v-model="user.password"
              size="6"
              :label="$t('user.add.field.password.title')"
              :placeholder="$t('user.add.field.password.placeholder')"
              :alert="errors.password"
            ></skijasi-password>
            <skijasi-switch
              v-model="user.emailVerified"
              size="6"
              :label="$t('user.add.field.emailVerified.title')"
              :placeholder="$t('user.add.field.emailVerified.placeholder')"
              :alert="errors.emailVerified"
              onLabel="Yes"
              offLabel="No"
              :tooltip="$t('user.help.emailVerified')"
            ></skijasi-switch>
            <skijasi-upload-image
              v-model="user.avatar"
              size="12"
              :label="$t('user.add.field.avatar.title')"
              :placeholder="$t('user.add.field.avatar.placeholder')"
              :alert="errors.avatar"
            ></skijasi-upload-image>
            <vs-col vs-lg="12">
              <skijasi-code-editor
                v-model="user.additionalInfo"
                size="12"
                :label="$t('user.add.field.additionalInfo.title')"
                :placeholder="$t('user.add.field.additionalInfo.placeholder')"
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
                <vs-icon icon="save"></vs-icon> {{ $t("user.add.button") }}
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
  name: "UserManagementAdd",
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
  mounted() {},
  methods: {
    submitForm() {
      this.errors = {};
      try {
        if (this.user.additionalInfo && this.user.additionalInfo != "") {
          JSON.parse(this.user.additionalInfo);
        }
        this.$openLoader();
        this.$api.skijasiUser
          .add({
            email: this.user.email,
            name: this.user.name,
            username: this.user.username,
            avatar: this.user.avatar,
            password: this.user.password,
            emailVerified: this.user.emailVerified,
            additionalInfo: this.user.additionalInfo,
          })
          .then((response) => {
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
      } catch (e) {
        this.$vs.notify({
          title: this.$t("alert.danger"),
          text: this.$t("user.add.field.additionalInfo.invalid"),
          color: "danger",
        });
      }
    },
  },
};
</script>
