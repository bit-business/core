<template>
  <div>
    <template v-if="!isMaintenance">
      <skijasi-breadcrumb-row full> </skijasi-breadcrumb-row>
      <vs-row v-if="$helper.isAllowedToModifyGeneratedCRUD('edit', dataType)">
        <vs-col vs-lg="12">
          <vs-card>
            <div slot="header">
              <h3>
                {{
                  $t("crudGenerated.edit.title", {
                    tableName: dataType.displayNameSingular,
                  })
                }}
              </h3>
            </div>
            <vs-row>
              <vs-col vs-lg="12" v-if="!isValid">
                <p class="is-error">No fields have been filled</p>
              </vs-col>
              <vs-col
                v-for="(dataRow, rowIndex) in dataType.dataRows"
                :key="rowIndex"
                :vs-lg="dataRow.details.size ? dataRow.details.size : '6'"
              >
                <template v-if="dataRow.edit && dataRow.type !== 'hidden'">
                  <!-- <input type="text" v-model="dataRow.value"> -->
                  <!-- <vs-input type="text" v-model="dataRow.value"></vs-input> -->
                  <skijasi-text
                    v-if="dataRow.type == 'text'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-text>
                  <skijasi-email
                    v-if="dataRow.type == 'email'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-email>
                  <skijasi-password
                    v-if="dataRow.type == 'password'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-password>
                  <skijasi-textarea
                    v-if="dataRow.type == 'textarea'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-textarea>
                  <skijasi-search
                    v-if="dataRow.type == 'search'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-search>
                  <skijasi-number
                    v-if="dataRow.type == 'number'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-number>
                  <skijasi-url
                    v-if="dataRow.type == 'url'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-url>
                  <skijasi-time
                    v-if="dataRow.type == 'time'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-time>
                  <skijasi-date
                    v-if="dataRow.type == 'date'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-date>
                  <skijasi-datetime
                    v-if="dataRow.type == 'datetime'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-datetime>
                  <skijasi-upload-image
                    v-if="dataRow.type == 'upload_image'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :private-only="dataRow.details.type == 'private-only'"
                    :shares-only="dataRow.details.type == 'shares-only'"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-upload-image>
                  <skijasi-upload-image-multiple
                    v-if="dataRow.type == 'upload_image_multiple'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :private-only="dataRow.details.type == 'private-only'"
                    :shares-only="dataRow.details.type == 'shares-only'"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-upload-image-multiple>
                  <skijasi-upload-file
                    v-if="dataRow.type == 'upload_file'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :private-only="dataRow.details.type == 'private-only'"
                    :shares-only="dataRow.details.type == 'shares-only'"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-upload-file>
                  <skijasi-upload-file-multiple
                    v-if="dataRow.type == 'upload_file_multiple'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :private-only="dataRow.details.type == 'private-only'"
                    :shares-only="dataRow.details.type == 'shares-only'"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-upload-file-multiple>
                  <skijasi-switch
                    v-if="dataRow.type == 'switch'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-switch>
                  <skijasi-slider
                    v-if="dataRow.type == 'slider'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-slider>
                  <skijasi-editor
                    v-if="dataRow.type == 'editor'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-editor>
                  <skijasi-tags
                    v-if="dataRow.type == 'tags'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-tags>
                  <skijasi-color-picker
                    v-if="dataRow.type == 'color_picker'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-color-picker>
                  <skijasi-hidden
                    v-if="
                      dataRow.type == 'hidden' ||
                      dataRow.type == 'data_identifier' ||
                      dataRow.type == 'relation'
                    "
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-hidden>
                  <skijasi-checkbox
                    v-if="dataRow.type == 'checkbox'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                    :items="dataRow.details.items ? dataRow.details.items : []"
                  ></skijasi-checkbox>
                  <skijasi-select
                    v-if="dataRow.type == 'select'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                    :items="dataRow.details.items ? dataRow.details.items : []"
                  ></skijasi-select>
                  <skijasi-select-multiple
                    v-if="dataRow.type == 'select_multiple'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                    :items="dataRow.details.items ? dataRow.details.items : []"
                  ></skijasi-select-multiple>
                  <skijasi-radio
                    v-if="dataRow.type == 'radio'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                    :items="dataRow.details.items ? dataRow.details.items : []"
                  ></skijasi-radio>
                  <skijasi-code-editor
                    v-if="dataRow.type == 'code'"
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                  ></skijasi-code-editor>
                  <skijasi-select
                    v-if="
                      dataRow.type == 'relation' &&
                      dataRow.relation.relationType == 'belongs_to'
                    "
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :items="
                      relationData[
                        $caseConvert.stringSnakeToCamel(
                          dataRow.relation.destinationTable
                        )
                      ]
                    "
                  ></skijasi-select>
                  <skijasi-select-multiple
                    v-if="
                      dataRow.type == 'relation' &&
                      dataRow.relation.relationType == 'belongs_to_many'
                    "
                    :label="dataRow.displayName"
                    :placeholder="dataRow.displayName"
                    v-model="dataRow.value"
                    size="12"
                    :alert="
                      errors[$caseConvert.stringSnakeToCamel(dataRow.field)]
                    "
                    :items="
                      relationData[
                        $caseConvert.stringSnakeToCamel(
                          dataRow.relation.destinationTable
                        )
                      ]
                    "
                  >
                  </skijasi-select-multiple>
                </template>
              </vs-col>
            </vs-row>
          </vs-card>
        </vs-col>
        <vs-col vs-lg="12">
          <vs-card class="action-card">
            <vs-row>
              <vs-col vs-lg="12">
                <vs-button color="primary" type="relief" @click="submitForm">
                  <vs-icon icon="save"></vs-icon>
                  {{ $t("crudGenerated.edit.button") }}
                </vs-button>
                <vs-button
                  :to="{
                    name: 'DataPendingEditRead',
                    params: {
                      urlBase64: base64PathName,
                    },
                  }"
                  v-if="dataLength > 0 && !isOnline"
                  color="success"
                  type="relief"
                >
                  <vs-icon icon="history"></vs-icon>
                  <strong>{{ $t("offlineFeature.dataUpdatePending") }}</strong>
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
                <h3>
                  {{
                    $t("crudGenerated.warning.notAllowedToEdit", {
                      tableName: dataType.displayNameSingular,
                    })
                  }}
                </h3>
              </vs-col>
            </vs-row>
          </vs-card>
        </vs-col>
      </vs-row>
    </template>
    <template v-if="isMaintenance">
      <skijasi-breadcrumb-row full> </skijasi-breadcrumb-row>

      <vs-row v-if="$helper.isAllowedToModifyGeneratedCRUD('edit', dataType)">
        <vs-col vs-lg="12">
          <div class="skijasi-maintenance__container">
            <img :src="`${maintenanceImg}`" alt="Maintenance Icon" />
            <h1 class="skijasi-maintenance__text">
              We are under <br />maintenance
            </h1>
          </div>
        </vs-col>
      </vs-row>
    </template>
  </div>
</template>

<script>
// eslint-disable-next-line no-unused-vars
import * as _ from "lodash";

export default {
  name: "CrudGeneratedEdit",
  components: {},
  data: () => ({
    isValid: true,
    errors: {},
    dataType: {},
    relationData: {},
    dataLength: 0,
    pathname: location.pathname,
    isMaintenance: false,
  }),
  mounted() {
    this.getDetailEntity();
    this.getRelationDataBySlug();
    this.requestObjectStoreData();
  },
  methods: {
    submitForm() {
      // init data row
      const dataRows = {};
      for (const row of this.dataType.dataRows) {
        if ((row && row.value) || (row && row.type == "textarea")) {
          dataRows[row.field] = row.value;
        }
        if ((row && row.value) || (row && row.type == "switch")) {
          dataRows[row.field] = row.value;
        }
      }

      // validate values in data rows must not equals 0
      if (Object.values(dataRows).length == 0) {
        this.isValid = false;
        return;
      }

      this.$openLoader();
      this.$api.skijasiEntity
        .edit({
          slug: this.$route.params.slug,
          data: dataRows,
        })
        .then((response) => {
          this.$closeLoader();
          this.$router.push({
            name: "CrudGeneratedBrowse",
            params: {
              slug: this.$route.params.slug,
            },
          });
        })
        .catch((error) => {
          this.requestObjectStoreData();
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    async getDetailEntity() {
      this.$openLoader();

      try {
        const response = await this.$api.skijasiEntity.read({
          slug: this.$route.params.slug,
          id: this.$route.params.id,
        });

        const {
          data: { dataType },
        } = await this.$api.skijasiTable.getDataType({
          slug: this.$route.params.slug,
        });

        this.$closeLoader();

        this.dataType = dataType;
        this.record = response.data;

        const dataRows = this.dataType.dataRows.map((data) => {
          try {
            data.add = data.add == 1;
            data.edit = data.edit == 1;
            data.read = data.read == 1;
            data.details = JSON.parse(data.details);

            if (
              data.type == "upload_image_multiple" ||
              data.type == "upload_file_multiple" ||
              data.type == "checkbox" ||
              data.type == "select_multiple"
            ) {
              const val =
                this.record[this.$caseConvert.stringSnakeToCamel(data.field)];
              if (val) {
                data.value = val.split(",");
              }
            } else if (data.type == "switch") {
              data.value = this.record[
                this.$caseConvert.stringSnakeToCamel(data.field)
              ]
                ? this.record[this.$caseConvert.stringSnakeToCamel(data.field)]
                : false;
            } else if (data.type == "slider") {
              data.value = parseInt(
                this.record[this.$caseConvert.stringSnakeToCamel(data.field)]
              );
            } else if (data.type == "datetime" || data.type == "date") {
              var dateValue = this.record[
                this.$caseConvert.stringSnakeToCamel(data.field)
              ]
                ? this.record[
                    this.$caseConvert.stringSnakeToCamel(data.field)
                  ].replace(" ", "T")
                : null;
              data.value = new Date(dateValue);
            } else if (data.value == undefined && data.type == "hidden") {
              data.value = data.details.value ? data.details.value : "";
            } else if (
              data.type == "text" ||
              data.type == "hidden" ||
              data.type == "url" ||
              data.type == "search" ||
              data.type == "password"
            ) {
              data.value = this.record[
                this.$caseConvert.stringSnakeToCamel(data.field)
              ]
                ? this.record[this.$caseConvert.stringSnakeToCamel(data.field)]
                : "";
            } else if (
              data.type == "relation" &&
              data.relation.relationType == 'belongs_to_many'
            ) {
              let record =
                this.record[this.$caseConvert.stringSnakeToCamel(data.field)];
              let destinationTableId = data.relation.destinationTable + 'Id';
              data.value = [];
              Object.entries(record).filter(function (item, key) {
                return (data.value[key] = item[1][destinationTableId]);
              });
            } else {
              data.value =
                this.record[this.$caseConvert.stringSnakeToCamel(data.field)];
            }
          } catch (error) {}
          return data;
        });
        this.dataType.dataRows = JSON.parse(JSON.stringify(dataRows));
      } catch (error) {
        if (error.status == 503) {
          this.isMaintenance = true;
        }
        this.$closeLoader();
        this.$vs.notify({
          title: this.$t("alert.danger"),
          text: error.message,
          color: "danger",
        });
      }
    },
    getRelationDataBySlug() {
      this.$openLoader();
      this.$api.skijasiTable
        .relationDataBySlug({
          slug: this.$route.params.slug,
        })
        .then((response) => {
          this.$closeLoader();
          this.relationData = response.data;
        })
        .catch((error) => {
          if (error.status == 503) {
            this.isMaintenance = true;
          }
          this.$closeLoader();
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    requestObjectStoreData() {
      this.$readObjectStore(this.pathname).then((store) => {
        if (store.result) {
          this.dataLength = store.result.data.length;
        }
      });
    },
  },
  computed: {
    isOnline: {
      get() {
        const isOnline = this.$store.getters["skijasi/getGlobalState"].isOnline;
        return isOnline;
      },
    },
    base64PathName() {
      return window.btoa(location.pathname);
    },
    maintenanceImg() {
      const config = this.$store.getters["skijasi/getConfig"];
      return config.maintenanceImage;
    },
  },
};
</script>
