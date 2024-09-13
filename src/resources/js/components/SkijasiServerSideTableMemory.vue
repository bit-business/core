<template>
  <div>
    <vs-table v-model="selected" :data="data" stripe multiple>
      <template slot="header">
        <vs-row class="skijasi-server-side-table__header">
          <vs-col vs-lg="6" vs-md="6" vs-sm="6" vs-xs="12">
            <div class="skijasi-server-side-table__header-select">  <vs-button class="gumbpomak" @click="resetFilters">Resetiraj pretrazivanje</vs-button>
              Prikaži&nbsp;
              <vs-select v-model="limit" width="100px">
                <vs-select-item
                  :key="index"
                  :value="row"
                  :text="row"
                  v-for="(row, index) in descriptionItems"
                />
              </vs-select>
              &nbsp;rezultata
            </div>
          </vs-col>
          <vs-col vs-lg="6" vs-md="6" vs-sm="6" vs-xs="12">
            <div class="skijasi-server-side-table__header-search">
              <div class="input-container">
                <input
                  type="text"
                  class="skijasi-server-side-table__input-search"
                  v-model="searchInput"
                  @keyup.enter="handleSearch"
                />
                <vs-icon
                  v-if="searchInput"
                  class="clear-icon"
                  icon="close"
                  @click="clearSearchInput"
                ></vs-icon>
              </div>
              <vs-icon icon="search"></vs-icon>
            </div>
          </vs-col>
        </vs-row>
      </template>
      <template slot="thead"><slot name="thead" /></template>
      <slot name="tbody" />
    </vs-table>
    <div class="skijasi-server-side-table__pagination">
      <vs-row
        class="skijasi-server-side-table__pagination-container"
        vs-justify="space-between"
        vs-type="flex"
        vs-w="12"
      >
        <vs-col
          class="skijasi-server-side-table__pagination-item"
          vs-type="flex"
          vs-justify="flex-start"
          vs-align="center"
          vs-lg="6"
          vs-md="12"
          vs-sm="12"
          vs-xs="12"
        >
          <span class="vs-pagination-desc">
            {{ descriptionTitle }}: {{ paginationData.from }} -
            {{ paginationData.to }} {{ descriptionConnector }}
            {{ paginationData.total }}
          </span>
        </vs-col>
        <vs-col
          class="skijasi-server-side-table__pagination-item"
          vs-type="flex"
          vs-justify="flex-end"
          vs-align="center"
          vs-lg="6"
          vs-md="12"
          vs-sm="12"
          vs-xs="12"
        >
          <vs-pagination :total="totalItem" v-model="page"></vs-pagination>
        </vs-col>
      </vs-row>
    </div>
  
  </div>
</template>

<script>
export default {
  name: "SkijasiServerSideTable",
  props: {
    paginationData: {},
    data: {
      type: Array,
      // eslint-disable-next-line vue/require-valid-default-prop
      default: [],
    },
    description: {
      default: true,
      type: Boolean,
    },
    descriptionItems: {
      default: () => [10, 50, 100, 200],
      type: Array,
    },
    descriptionTitle: {
      default: "Broj rezultata",
      type: String,
    },
    descriptionConnector: {
      default: "od",
      type: String,
    },
    descriptionBody: {
      type: String,
    },
  },
  data: () => ({
    selected: [],
    limit: 10,
    page: parseInt(localStorage.getItem('skijasi-page')) || 1,
    currentSortKey: null,
    currentSortType: null,
    searchInput: ''
  }),
  computed: {
    totalItem() {
      return this.paginationData.total > 0
        ? Math.ceil(this.paginationData.total / this.limit)
        : 1;
    },
  },
  watch: {
    page: function (to) {
      localStorage.setItem('skijasi-page', to);
      this.$emit("changePage", to);
    },
    limit: function (to) {
      this.page = 1;
      localStorage.setItem('skijasi-limit', to);
      this.$emit("changeLimit", to);
    },
    searchInput: function (to) {
      localStorage.setItem('skijasi-search', to);
    },
    selected: function (to) {
      this.$emit("select", to);
    },
  },
  mounted() {
    // Retrieve values from localStorage
    const savedPage = localStorage.getItem('skijasi-page');
    const savedLimit = localStorage.getItem('skijasi-limit');
    const savedSearch = localStorage.getItem('skijasi-search');
    

    if (savedPage) this.page = parseInt(savedPage);
    if (savedLimit) this.limit = parseInt(savedLimit);
    if (savedSearch) this.searchInput = savedSearch;
  },
  methods: {
    handleSearch(e) {
      this.$emit("search", e);
      this.page = 1;
    },
    
    clearSearchInput() {
      this.page = 1;
      this.searchInput = '';
      this.$emit("search", ''); // Emit search event with empty string
    },

    resetFilters() {
      this.page = 1;
      this.limit = 10;
      this.searchInput = '';

      // Clear from localStorage
      localStorage.removeItem('skijasi-page');
      localStorage.removeItem('skijasi-limit');
      localStorage.removeItem('skijasi-search');

      this.$emit("search", '');
    },

    handleSort(key, sortType) {
      this.currentSortKey = key;
      this.currentSortType = sortType;
      this.$emit("sort", key, sortType);
    },
  },
};
</script>

<style scoped>
.input-container {
  position: relative;
}

.clear-icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 10px;
  cursor: pointer;
}

.gumbpomak {
  margin-right: 20px;
}
</style>
