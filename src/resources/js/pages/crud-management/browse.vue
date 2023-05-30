<template>
  <div>
    <skijasi-breadcrumb-hover full>
      <template slot="action">
        <download-excel
            :data="tables"
            :fields="fieldsForExcel"
            :worksheet="'CRUD Management'"
            :name="'CRUD Management '+ '.xls'"
            class="crud-generated__excel-button"
          >
            <skijasi-dropdown-item
              icon="file_upload"
            >
              {{ $t("action.exportToExcel") }}
            </skijasi-dropdown-item>
          </download-excel>
          <skijasi-dropdown-item
            icon="file_upload"
            @click="generatePdf"
          >
            {{ $t("action.exportToPdf") }}
          </skijasi-dropdown-item>
      </template>
    </skijasi-breadcrumb-hover>
    <vs-row v-if="$helper.isAllowed('browse_crud_data')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>{{ $t("crud.title") }}</h3>
          </div>
          <div>
            <skijasi-table
              v-model="selected"
              pagination
              max-items="10"
              search
              :data="tables"
              stripe
              description
              :description-items="descriptionItems"
              :description-title="$t('crud.footer.descriptionTitle')"
              :description-connector="$t('crud.footer.descriptionConnector')"
              :description-body="$t('crud.footer.descriptionBody')"
            >
              <template slot="thead">
                <vs-th sort-key="tableName">
                  {{ $t("crud.header.table") }}
                </vs-th>
                <vs-th> {{ $t("crud.header.action") }} </vs-th>
              </template>

              <template slot-scope="{ data }">
                <vs-tr
                  :data="table"
                  :key="index"
                  v-for="(table, index) in data"
                >
                  <vs-td :data="data[index].tableName">
                    {{ data[index].tableName }}
                  </vs-td>
                  <vs-td class="skijasi-table__td" v-if="data[index].crudData">
                    <skijasi-dropdown vs-trigger-click>
                      <vs-button
                        size="large"
                        type="flat"
                        icon="more_vert"
                      ></vs-button>
                      <vs-dropdown-menu>
                        <skijasi-dropdown-item
                          icon="visibility"
                          :to="{
                            name: 'CrudGeneratedBrowse',
                            params: { slug: data[index].crudData.slug },
                          }"
                        >
                          Detail
                        </skijasi-dropdown-item>
                        <skijasi-dropdown-item
                          icon="edit"
                          v-if="$helper.isAllowed('edit_crud_data')"
                          :to="{
                            name: 'CrudManagementEdit',
                            params: { tableName: data[index].tableName },
                          }"
                        >
                          Edit
                        </skijasi-dropdown-item>
                        <skijasi-dropdown-item
                          icon="delete"
                          v-if="$helper.isAllowed('delete_crud_data')"
                          @click="openConfirm(data[index].crudData.id)"
                        >
                          Delete
                        </skijasi-dropdown-item>
                      </vs-dropdown-menu>
                    </skijasi-dropdown>
                  </vs-td>
                  <vs-td v-else class="skijasi-table__td">
                    <skijasi-dropdown vs-trigger-click>
                      <vs-button
                        size="large"
                        type="flat"
                        icon="more_vert"
                      ></vs-button>
                      <vs-dropdown-menu>
                        <skijasi-dropdown-item
                          icon="add"
                          v-if="$helper.isAllowed('add_crud_data')"
                          :to="{
                            name: 'CrudManagementAdd',
                            params: { tableName: data[index].tableName },
                          }"
                        >
                          {{ $t("crud.body.button") }}
                        </skijasi-dropdown-item>
                      </vs-dropdown-menu>
                    </skijasi-dropdown>
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-table>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
    <vs-row v-else>
      <vs-col vs-lg="12">
        <vs-card>
          <vs-row>
            <vs-col vs-lg="12">
              <h3>{{ $t("crud.warning.notAllowed") }}</h3>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>

<script>
import downloadExcel from "vue-json-excel";
import jsPDF from "jspdf";
import "jspdf-autotable";
export default {
  components: { downloadExcel },
  name: "CrudManagementBrowse",
  data: () => ({
    descriptionItems: [10, 50, 100],
    selected: [],
    tables: [],
    willDeleteId: null,
    fieldsForExcel: {},
    fieldsForPdf: [],
    dataType: {
      fields: ["table_name"]
    }
  }),
  mounted() {
    this.getTableList();
  },
  methods: {
    openConfirm(id) {
      this.willDeleteId = id;
      this.$vs.dialog({
        type: "confirm",
        color: "danger",
        title: this.$t("action.delete.title"),
        text: this.$t("action.delete.text"),
        accept: this.deleteCRUDData,
        acceptText: this.$t("action.delete.accept"),
        cancelText: this.$t("action.delete.cancel"),
        cancel: () => {
          this.willDeleteId = null;
        },
      });
    },
    getTableList() {
      this.$openLoader();
      this.$api.skijasiCrud
        .browse()
        .then((response) => {
          this.$closeLoader();
          this.tables = response.data.tablesWithCrudData;
          this.prepareExcelExporter()
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
    deleteCRUDData() {
      this.$openLoader();
      this.$api.skijasiCrud
        .delete({
          id: this.willDeleteId,
        })
        .then((response) => {
          this.$closeLoader();
          this.getTableList();
          this.$store.commit("skijasi/FETCH_MENU");
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
    prepareExcelExporter() {
      for (const iterator of this.dataType.fields) {
        let field = iterator;
        if (field.includes("_")) {
          field = field.split("_");
          // field = field[0].charAt(0).toUpperCase() + field[0].slice(1) + " " + field[1].charAt(0).toUpperCase() + field[1].slice(1);
          field = 'Name';
        }
        field = field.charAt(0).toUpperCase() + field.slice(1);

        this.fieldsForExcel[field] = this.$caseConvert.stringSnakeToCamel(iterator);
      }

      for (let iterator of this.dataType.fields) {
        if (iterator.includes("_")) {
          iterator = iterator.split("_");
          // iterator = iterator[0] + " " + iterator[1].charAt(0).toUpperCase() + iterator[1].slice(1);
          iterator = 'Name'
        }
        
        const string = this.$caseConvert.stringSnakeToCamel(iterator);
        this.fieldsForPdf.push(
          string.charAt(0).toUpperCase() + string.slice(1)
        );
      }
    },
    generatePdf() {

      let data = this.tables;

      // data.map((value) => {
      //   for (const iterator in value) {
      //     if (!this.dataType.fields.includes(iterator)) {
      //       delete value[iterator]
      //     }
      //   }
      //   return value;
      // })

      const result = data.map(Object.values);

      // eslint-disable-next-line new-cap
      const doc = new jsPDF("l");

      // Dynamic table title
      doc.setFont("helvetica", "bold");
      doc.setFontSize(28);
      doc.text(this.$t("crud.title"), 149, 20, "center");

      // Data table
      doc.autoTable({
        head: [this.fieldsForPdf],
        body: result,
        startY: 30,
        // Default for all columns
        styles: { valign: "middle" },
        headStyles: { fillColor: [6, 187, 211] },
        // Override the default above for the text column
        columnStyles: { text: { cellWidth: "wrap" } },
      });

      // Output Table title and data table in new tab
      const output = doc.output("blob");
      data = window.URL.createObjectURL(output);
      window.open(data, "_blank");

      setTimeout(function () {
        // For Firefox it is necessary to delay revoking the ObjectURL
        window.URL.revokeObjectURL(data);
      }, 100);
    },
  },
};
</script>
