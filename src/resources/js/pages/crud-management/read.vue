<template>
  <div>
    <skijasi-breadcrumb-row></skijasi-breadcrumb-row>
    <vs-row v-if="$helper.isAllowed('read_crud_data')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>Add CRUD for {{ $route.params.tableName }}</h3>
          </div>
          <vs-row>
            <skijasi-text
              v-model="crudData.name"
              size="6"
              label="Table Name"
              placeholder="Table Name"
              required
              readonly
            ></skijasi-text>
            <skijasi-switch
              size="3"
              v-model="crudData.generatePermissions"
              label="Generate Permissions"
            ></skijasi-switch>
            <skijasi-switch
              size="3"
              v-model="crudData.serverSide"
              label="Server Side"
            ></skijasi-switch>
          </vs-row>
          <vs-row>
            <skijasi-text
              v-model="crudData.displayNameSingular"
              size="6"
              label="Display Name(Singular)"
              required
              placeholder="Display Name(Singular)"
            ></skijasi-text>
            <skijasi-text
              v-model="crudData.displayNamePlural"
              size="6"
              label="Display Name(Plural)"
              required
              placeholder="Display Name(Plural)"
            ></skijasi-text>
            <skijasi-text
              v-model="crudData.slug"
              size="6"
              label="URL Slug (must be unique)"
              required
              placeholder="URL Slug (must be unique)"
            ></skijasi-text>
            <skijasi-text
              v-model="crudData.icon"
              size="6"
              label="Icon"
              placeholder="Icon"
            ></skijasi-text>
            <skijasi-text
              v-model="crudData.modelName"
              size="6"
              label="Model Name"
              placeholder="Model Name"
            ></skijasi-text>
            <skijasi-text
              v-model="crudData.controller"
              size="6"
              label="Controller Name"
              placeholder="Controller Name"
            ></skijasi-text>
            <skijasi-select
              v-model="crudData.orderColumn"
              size="3"
              label="Order Column"
              placeholder="Order Column"
              :items="fieldList"
            ></skijasi-select>
            <skijasi-select
              v-model="crudData.orderDisplayColumn"
              size="3"
              label="Order Display Column"
              placeholder="Order Display Column"
              :items="fieldList"
            ></skijasi-select>
            <skijasi-select
              v-model="crudData.orderDirection"
              size="3"
              label="Order Direction"
              placeholder="Order Direction"
              :items="orderDirections"
            ></skijasi-select>
            <skijasi-select
              v-model="crudData.defaultServerSideSearchField"
              size="3"
              label="Default Server Side Search Field"
              placeholder="Default Server Side Search Field"
              :items="fieldList"
            ></skijasi-select>
            <skijasi-textarea
              size="12"
              label="Description"
              placeholder="Description"
              v-model="crudData.description"
            >
            </skijasi-textarea>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>Add CRUD Fields for {{ $route.params.tableName }}</h3>
          </div>
          <vs-row>
            <vs-col col-lg="12">
              <table class="skijasi-table">
                <thead>
                  <th class="skijasi-table__th"></th>
                  <th class="skijasi-table__th">Field</th>
                  <th class="skijasi-table__th">Visibility</th>
                  <th class="skijasi-table__th">Input Type</th>
                  <th class="skijasi-table__th--sm">Display Name</th>
                  <th>Optional Details</th>
                </thead>
                <draggable v-model="crudData.rows" tag="tbody">
                  <tr :key="index" v-for="(field, index) in crudData.rows">
                    <td>
                      <vs-icon
                        icon="drag_indicator"
                        class="is-draggable"
                      ></vs-icon>
                    </td>
                    <td :data="field.field">
                      <strong>{{ field.field }}</strong>
                      <br />
                      <span> Type: {{ field.type }} </span>
                      <br />
                      <span>
                        Required: <span v-if="field.required">Yes</span
                        ><span v-else>No</span>
                      </span>
                    </td>
                    <td>
                      <vs-checkbox
                        v-model="field.browse"
                        class="crud-management__notification-item"
                        >Browse</vs-checkbox
                      >
                      <vs-checkbox
                        v-model="field.read"
                        class="crud-management__notification-item"
                        >Read</vs-checkbox
                      >
                      <vs-checkbox
                        v-model="field.edit"
                        class="crud-management__notification-item"
                        >Edit</vs-checkbox
                      >
                      <vs-checkbox
                        v-model="field.add"
                        class="crud-management__notification-item"
                        >Add</vs-checkbox
                      >
                      <vs-checkbox
                        v-model="field.delete"
                        class="crud-management__notification-item"
                        >Delete</vs-checkbox
                      >
                    </td>
                    <td>
                      <vs-select v-model="field.type">
                        <vs-select-item
                          :key="index"
                          :value="item.value"
                          :text="item.label"
                          v-for="(item, index) in componentList"
                        />
                      </vs-select>
                    </td>
                    <td>
                      <vs-input
                        placeholder="Display Name"
                        v-model="field.displayName"
                      />
                    </td>
                    <td>
                      <skijasi-code-editor v-model="field.details">
                      </skijasi-code-editor>
                    </td>
                  </tr>
                </draggable>
              </table>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
      <vs-col vs-lg="12">
        <vs-card>
          <vs-row>
            <vs-col vs-lg="12">
              <vs-button color="primary" type="relief" @click="submitForm">
                <vs-icon icon="save"></vs-icon> Save
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
              <h3>You're not allowed to read CRUD</h3>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
  name: "CrudManagementRead",
  components: {
    draggable,
  },
  data: () => ({
    breadcrumb: [],
    fieldList: [],
    tableColumns: [],
    orderDirections: [
      {
        label: "Ascending",
        value: "asc",
      },
      {
        label: "Descending",
        value: "desc",
      },
    ],
    crudData: {
      name: "",
      slug: "",
      displayNameSingular: "",
      displayNamePlural: "",
      icon: "",
      modelName: "",
      policyName: "",
      description: "",
      generatePermissions: true,
      serverSide: false,
      details: "",
      controller: "",
      rows: [],
    },
  }),
  computed: {
    componentList: {
      get() {
        return this.$store.getters["skijasi/getComponent"];
      },
    },
  },
  mounted() {
    this.crudData.name = this.$route.params.tableName;
    this.crudData.displayNameSingular = this.$helper.generateDisplayName(
      this.$route.params.tableName
    );
    this.crudData.displayNamePlural =
      this.$helper.generateDisplayName(this.$route.params.tableName) + "s";
    this.crudData.slug = this.$helper.generateSlug(
      this.$route.params.tableName
    );
    this.getTableDetail();
  },
  methods: {
    submitForm() {
      this.$openLoader();
      this.$api.skijasiCrud
        .add(this.$caseConvert.snake(this.crudData))
        .then((response) => {
          this.$closeLoader();
          this.$store.commit("skijasi/FETCH_MENU");
          this.$router.push({ name: "CrudManagementBrowseBrowse" });
        })
        .catch((error) => {
          this.$closeLoader();
          this.$vs.notify({
            title: "Danger",
            text: error.message,
            color: "danger",
          });
        });
    },
    getTableDetail() {
      this.$openLoader();
      this.$api.skijasiTable
        .read({
          table: this.$route.params.tableName,
        })
        .then((response) => {
          const fieldList = response.data;
          this.tableColumns = fieldList;
          this.fieldList = fieldList.map((field) => {
            return {
              label: field.name,
              value: field.name,
            };
          });
          this.crudData.rows = fieldList.map((field) => {
            return {
              field: field.name,
              type: this.$helper.mapFieldType(field.type),
              displayName: this.$helper.generateDisplayName(field.name),
              required: field.isNotNull,
              browse: true,
              read: true,
              edit: false,
              add: false,
              delete: false,
              details: "{}",
              order: 1,
            };
          });
          this.$closeLoader();
        })
        .catch(() => {
          this.$closeLoader();
        });
    },
  },
};
</script>
