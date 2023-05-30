<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_permissions')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("permission.add.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="permission.key"
              size="6"
              :label="$t('permission.add.field.key.title')"
              :placeholder="$t('permission.add.field.key.placeholder')"
              :alert="errors.key"
            ></skijasi-text>
            <skijasi-switch
              v-model="permission.alwaysAllow"
              size="3"
              :label="$t('permission.add.field.alwaysAllow')"
              placeholder="Always Allow"
              :alert="errors.alwaysAllow"
              :tooltip="$t('permission.help.alwaysAllow')"
            ></skijasi-switch>
            <skijasi-switch
              v-model="permission.isPublic"
              size="3"
              :label="$t('permission.add.field.isPublic')"
              placeholder="Is Public"
              :alert="errors.isPublic"
              :tooltip="$t('permission.help.isPublic')"
            ></skijasi-switch>
            <skijasi-textarea
              v-model="permission.description"
              size="12"
              :label="$t('permission.add.field.description.title')"
              :placeholder="$t('permission.add.field.description.placeholder')"
              :alert="errors.description"
            ></skijasi-textarea>
            <skijasi-text
              v-model="permission.tableName"
              size="12"
              :label="$t('permission.add.field.tableName.title')"
              :placeholder="$t('permission.add.field.tableName.placeholder')"
              :alert="errors.tableName"
            ></skijasi-text>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon>
                {{ $t("permission.add.button") }}
              </vs-button>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
    <vs-row v-else>
      <vs-col vs-lg="12">
        <vs-card>
          <vs-row>
            <vs-col vs-lg="12">
              <h3>{{ $t("permission.warning.notAllowedToAdd") }}</h3>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "PermissionManagementAdd",
  components: {},
  data: () => ({
    errors: {},
    permission: {
      description: "",
      key: "",
      tableName: "",
      alwaysAllow: false,
      isPublic: false,
    },
  }),
  mounted() {},
  methods: {
    submitForm() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiPermission
        .add(this.permission)
        .then((response) => {
          this.$closeLoader();
          this.$router.push({ name: "PermissionManagementBrowse" });
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
