<template>
  <div>
    <skijasi-breadcrumb-row> </skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('add_roles')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("role.add.title") }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="role.name"
              size="6"
              :label="$t('role.add.field.name.title')"
              :placeholder="$t('role.add.field.name.placeholder')"
              :alert="errors.name"
            ></skijasi-text>
            <skijasi-text
              v-model="role.displayName"
              size="6"
              :label="$t('role.add.field.displayName.title')"
              :placeholder="$t('role.add.field.displayName.placeholder')"
              :alert="errors.displayName"
            ></skijasi-text>
            <skijasi-textarea
              v-model="role.description"
              size="12"
              :label="$t('role.add.field.description.title')"
              :placeholder="$t('role.add.field.description.placeholder')"
              :alert="errors.description"
            ></skijasi-textarea>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card class="action-card">
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> {{ $t("role.add.button") }}
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
              <h3>{{ $t("role.warning.notAllowedToAdd") }}</h3>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
export default {
  name: "RoleManagementAdd",
  components: {},
  data: () => ({
    errors: {},
    role: {
      description: "",
      name: "",
      displayName: "",
    },
  }),
  mounted() {},
  methods: {
    submitForm() {
      this.errors = {};
      this.$openLoader();
      this.$api.skijasiRole
        .add(this.role)
        .then((response) => {
          this.$closeLoader();
          this.$router.push({ name: "RoleManagementBrowse" });
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
