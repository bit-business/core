<template>
  <div>
 <!-- User Login Statistics Chart -->
 <vs-row v-if="$helper.isAllowed('browse_activitylogs')">
      <vs-col vs-lg="12">
        <vs-card>
          <div slot="header">
            <h3>Statistika prijavljivanja</h3>
          </div>
          <div>
            <canvas ref="chart" style="width: 100%; height: 400px;"></canvas>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
    <!-- <skijasi-breadcrumb-hover full>
  template slot="action">
        <download-excel
            :data="activitylogs"
            :fields="fieldsForExcel"
            :worksheet="'Povijest Aktivnosti'"
            :name="'Povijest Aktivnosti '+ '.xls'"
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
          <skijasi-dropdown-item
            icon="delete_sweep"
            v-if="selected.length > 0 && $helper.isAllowed('delete_roles')"
            @click.stop
            @click="confirmDeleteMultiple"
          >
            {{ $t("action.bulkDelete") }}
          </skijasi-dropdown-item>
      </template> 
    </skijasi-breadcrumb-hover> -->
    <vs-row v-if="$helper.isAllowed('browse_activitylogs')">
      <vs-col vs-lg="12">
        <vs-card>

          <div slot="header">
            <h3>{{ $t("activityLog.title") }}</h3>
          </div>


          <div>
            <skijasi-server-side-table
              v-model="selected"
              :data="activitylogs"
              stripe
              :pagination-data="data"
              @search="handleSearch"
              @changePage="handlePageChange"
              @changeLimit="handleLimitChange"
              @sort="handleSort"
              :description-items="descriptionItems"
              :description-title="$t('crudGenerated.footer.descriptionTitle')"
              :description-connector="
                $t('crudGenerated.footer.descriptionConnector')
              "
            >
              <template slot="thead" style="text-align: center;">
                <skijasi-th sort-key="logName">
                  {{ $t("activityLog.header.logName") }}
                </skijasi-th>
                <skijasi-th sort-key="causerName">
                  {{ $t("activityLog.header.causerName") }}
                </skijasi-th>
                <skijasi-th sort-key="description">
                  {{ $t("activityLog.header.description") }}
                </skijasi-th>
                <skijasi-th sort-key="createdAt">
                  {{ "Vrijeme promjene" }}
                </skijasi-th>
                <skijasi-th> {{ $t("activityLog.header.action") }} </skijasi-th>
              </template>

              <template slot="tbody">
                <vs-tr
                  :data="record"
                  :key="index"
                  v-for="(record, index) in activitylogs"
                  style="text-align: center;"
                >
                  <vs-td :data="record.logName">
                    {{ record.logName ? record.logName : "-" }}
                  </vs-td>
                  <vs-td :data="record.causerName">
                    {{ record.causerName ? record.causerName : "-" }}
                  </vs-td>
                  <vs-td :data="record.description">
                    {{ record.description }}
                  </vs-td>
                  <vs-td :data="record.createdAt">
                    {{ formatDate(record.createdAt) }}
                  </vs-td>
                  <vs-td style="" class="activity-log__dropdown-button">
                
                     
                        <vs-button
                          :to="{
                            name: 'ActivityLogRead',
                            params: { id: record.id },
                          }"
                          icon="visibility"
                        >
                          Detalji
                        </vs-button>
                     
                  </vs-td>
                </vs-tr>
              </template>
            </skijasi-server-side-table>
          </div>
        </vs-card>
      </vs-col>
    </vs-row>
    <vs-row v-else>
      <vs-col vs-lg="12">
        <vs-card>
          <vs-row>
            <vs-col vs-lg="12">
              <h3>{{ $t("activityLog.warning.notAllowed") }}</h3>
            </vs-col>
          </vs-row>
        </vs-card>
      </vs-col>
    </vs-row>
  </div>
</template>
<script>
// eslint-disable-next-line no-unused-vars
import moment from "moment";
import downloadExcel from "vue-json-excel";
import jsPDF from "jspdf";
import "jspdf-autotable";

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);


export default {
  name: "ActivityLogBrowse",
  components: { downloadExcel },
  data: () => ({
    chart: null,


    data: {},
    selected: [],
    descriptionItems: [10, 50, 100],
    activitylogs: [],
    willDeleteId: null,
    page: 1,
    limit: 10,
    totalItem: 0,
    filter: "",
    orderField: "",
    orderDirection: "",
    fieldsForExcel: {},
    fieldsForPdf: [],
    dataType: {
      fields: ['log_name', "description", "created_at", "causer_name"]
    }
  }),
  mounted() {
    this.getActivityLogList();
    this.$nextTick(() => {
    this.fetchUserLoginStats();
  });
  },
  watch: {
    page: function (to, from) {
      this.getActivityLogList();
    },
    limit: function (to, from) {
      this.page = 1;
      this.getActivityLogList();
    },
  },
  methods: {
    formatDate(dateString) {
      // Format the date string as needed
      return new Date(dateString).toLocaleString();
    },


    handleSearch(e) {
      this.filter = e.target.value;
      this.page = 1;
      this.getActivityLogList();
    },
    handlePageChange(e) {
      this.page = e;
    },
    handleLimitChange(e) {
      this.limit = e;
    },
    handleSort(key, type) {
      this.orderField = key;
      this.orderDirection = type;
      this.getActivityLogList();
    },
    getActivityLogList() {
      this.$openLoader();
      this.$api.skijasiActivityLog
        .browse({
          filter: this.filter,
          limit: this.limit,
          page: this.page,
          orderField: this.orderField,
          orderDirection: this.orderDirection,
        })
        .then((response) => {
          this.$closeLoader();
          this.selected = [];
          this.data = response.data;
          this.activitylogs = response.data.data;
          this.totalItem =
            response.data.total > 0
              ? Math.ceil(response.data.total / this.limit)
              : 1;
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
    prepareExcelExporter() {
      for (const iterator of this.dataType.fields) {
        let field = iterator;
        if (field.includes("_")) {
          field = field.split("_");
          field = field[0].charAt(0).toUpperCase() + field[0].slice(1) + " " + field[1].charAt(0).toUpperCase() + field[1].slice(1);
        }
        field = field.charAt(0).toUpperCase() + field.slice(1);

        this.fieldsForExcel[field] = this.$caseConvert.stringSnakeToCamel(iterator);
      }

      for (let iterator of this.dataType.fields) {
        if (iterator.includes("_")) {
          iterator = iterator.split("_");
          iterator = iterator[0] + " " + iterator[1].charAt(0).toUpperCase() + iterator[1].slice(1);
        }
        
        const string = this.$caseConvert.stringSnakeToCamel(iterator);
        this.fieldsForPdf.push(
          string.charAt(0).toUpperCase() + string.slice(1)
        );
      }
    },
    generatePdf() {

      let data = this.activitylogs;

      let fields = [];
      
      for (const iterator in this.dataType.fields){
        const string = this.$caseConvert.stringSnakeToCamel(this.dataType.fields[iterator]);
        fields.push(string);
      }

      data.map((value) => {
        for (const iterator in value) {
          if (!fields.includes(iterator)) {
            delete value[iterator]
          }
        }
        return value;
      })
      
      const result = data.map(Object.values);
      
      // eslint-disable-next-line new-cap
      const doc = new jsPDF("l");

      // Dynamic table title
      doc.setFont("helvetica", "bold");
      doc.setFontSize(28);
      doc.text(this.$t("activityLog.title"), 149, 20, "center");

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




    async fetchUserLoginStats() {
  try {
    const response = await this.$api.skijasiActivityLog.getstats({
      start_date: '2024-01-01',
      end_date: '2024-12-31',
    });
    
    if (response && response.data) {
      // Filter data for users 1-5
      const filteredData = Object.entries(response.data)
        .filter(([key, _]) => parseInt(key) >= 1 && parseInt(key) <= 5)
        .reduce((obj, [key, value]) => {
          obj[key] = value;
          return obj;
        }, {});

      const dataArray = Object.entries(filteredData);
      this.createChart(dataArray);
    } else {
      // Handle empty response
    }
  } catch (error) {
    console.error('Error fetching user login stats:', error);
  }
},
createChart(data) {
  if (this.chart) {
    this.chart.destroy();
  }
  const ctx = this.$refs.chart.getContext('2d');
  
  const chartData = {
    labels: data.map(([userId, _]) => `ID korisnika ${userId}`),
    datasets: [
      {
        label: 'Ukupno Prijavljen Korisnik (sati)',
        data: data.map(([_, stat]) => stat.totalDuration),
        backgroundColor: 'rgba(75, 192, 192, 0.6)',
        yAxisID: 'y-axis-1',
      },
      {
        label: 'Broj prijavljivanja',
        data: data.map(([_, stat]) => stat.sessionCount),
        backgroundColor: 'rgba(255, 199, 132, 0.6)',
        yAxisID: 'y-axis-2',
      },
    ],
  };
  
  this.chart = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: {
      responsive: true,
      scales: {
        'y-axis-1': {
          type: 'linear',
          position: 'left',
          title: {
            display: true,
            text: 'Ukupno vrijeme dok je bio prijavljen (sati)',
          },
        },
        'y-axis-2': {
          type: 'linear',
          position: 'right',
          title: {
            display: true,
            text: 'Broj prijavljivanja',
          },
          grid: {
            drawOnChartArea: false,
          },
        },
        x: {
          title: {
            display: true,
            text: 'Korisnički ID',
          },
        },
      },
    },
  });
},

  },
};
</script>
