<template>
  <div id="parentx">
    <vs-sidebar
      default-index="1"
      :parent="parent"
      :hiddenBackground="doNotClose"
      color="primary"
      class="sidebarx skijasi-sidebar"
      spacer
      v-model="isSidebarActive"
      :click-not-close="doNotClose"
      :reduce="reduceSidebar"
    >
      <div class="header-sidebar text-center" slot="header">
        <vs-avatar size="70px" :src="getAvatar" />
        <skijasi-sidebar-group
          :title="user.name"
          :subTitle="user.email"
          icon="a"
        >
          <skijasi-sidebar-item
            v-if="user.id"
            icon="person_outline"
            :to="{
              name: 'UserProfile',
            }"
          >
            Profil
          </skijasi-sidebar-item>
          <skijasi-sidebar-item icon="logout" @click="logout()">
            Odlogiraj se
          </skijasi-sidebar-item>
        </skijasi-sidebar-group>
        <vs-select
          v-model="selectedLang"
          width="100%"
          style="padding: 10px"
          v-if="view == $constants.MOBILE"
        >
          <vs-select-item
            :key="index"
            :value="item.key ? item.key : item"
            :text="item.label ? item.label : item.key ? item.key : item"
            v-for="(item, index) in getLocale"
          />
        </vs-select>
      </div>

      <template v-for="(displayMenu, indexMenu) in mainMenu">
        <!-- if show header -->
        <skijasi-sidebar-group
          :title="displayMenu.menu.displayName"
          :open="displayMenu.menu.isExpand == 1"
          :icon="displayMenu.menu.icon"
          :key="indexMenu"
          v-if="
            displayMenu.menuItems &&
            displayMenu.menuItems.length > 0 &&
            displayMenu.menu.isShowHeader
          "
        >
          <template v-for="(menu, index) in displayMenu.menuItems">
            <skijasi-sidebar-menu
              :defaultIsExpand="menu.isExpand == 1"
              :title="menu.title"
              :url="menu.url"
              :icon="menu.icon"
              :children="menu.children"
              :key="index"
            />
          </template>
        </skijasi-sidebar-group>

        <!-- else hidden header -->
        <div :key="indexMenu" v-else>
          <template v-for="(menu, index) in displayMenu.menuItems">
            <skijasi-sidebar-menu
              :defaultIsExpand="menu.isExpand == 1"
              :title="menu.title"
              :url="menu.url"
              :icon="menu.icon"
              :children="menu.children"
              :key="index"
            />
          </template>
        </div>
      </template>
    </vs-sidebar>
  </div>
</template>

<script>
import _ from "lodash";

export default {
  name: "SideBar",
  components: {},
  props: {
    parent: {
      type: String,
    },
    index: {
      default: null,
      type: [String, Number],
    },
    doNotClose: {
      default: false,
      type: Boolean,
    },
    view: {
      type: String,
      default: "desktop",
    },
  },
  data: () => ({
    sidebarModel: true,
    windowWidth: window.innerWidth,
    prefix: process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
      ? process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
      : "skijasi-dashboard",
  }),
  computed: {
    // This is for mobile trigger
    isSidebarActive: {
      get() {
        return this.$store.state.skijasi.isSidebarActive;
      },
      set(val) {
        this.$store.commit("skijasi/IS_SIDEBAR_ACTIVE", val);
      },
    },
    reduceSidebar: {
      get() {
        return this.$store.state.skijasi.reduceSidebar;
      },
    },
    mainMenu: {
      get() {
        return this.$store.getters["skijasi/getMenu"];
      },
    },
    configurationMenu: {
      get() {
        return this.$store.getters["skijasi/getConfigurationMenu"];
      },
    },
    user: {
      get() {
        const user = this.$store.getters["skijasi/getUser"];
        return user;
      },
    },
    getLocale: {
      get() {
        return this.$store.getters["skijasi/getLocale"];
      },
    },
    selectedLang: {
      get() {
        let selected = this.$store.getters["skijasi/getSelectedLocale"];
        if (selected.key) {
          selected = selected.key;
        }
        return selected;
      },
      set(val) {
        this.setLocale(_.find(this.getLocale, ["key", val]));
      },
    },
    adminPanelHeaderColor() {
      const config = this.$store.getters["skijasi/getConfig"];
      return config.adminPanelHeaderColor
        ? config.adminPanelHeaderColor
        : "#ffffff";
    },
    getAvatar() {
      const user = this.$store.getters["skijasi/getUser"];
      return user.avatar;
    },
  },
  methods: {
    open(url) {
      if (!this.doNotClose) {
        this.isSidebarActive = false;
      }
      window.open(url);
    },
    logout() {
      this.$api.skijasiAuth
        .logout()
        .then((response) => {
          localStorage.clear();
          this.$router.push({ name: "AuthLogin" });
        })
        .catch((error) => {
          this.$vs.notify({
            title: this.$t("alert.danger"),
            text: error.message,
            color: "danger",
          });
        });
    },
    setLocale(item) {
      this.$i18n.locale = item.key;
      this.$store.commit("skijasi/SET_LOCALE", item);
    },
  },
  watch: {
    adminPanelHeaderColor: {
      handler(val, oldVal) {
        document
          .querySelectorAll(".skijasi-sidebar .vs-sidebar--items")
          .forEach((element) => {
            try {
              let vsScrollPrimary = "255,255,255";
              if (val.substring(0, 1) == "#") {
                vsScrollPrimary = this.$helper.hexToVsPrimary(val);
              } else {
                vsScrollPrimary = this.$helper.rgbToVsPrimary(val);
              }
              element.style.setProperty(
                "--vs-scrollbar-primary",
                vsScrollPrimary
              );
            } catch (error) {
              console.log(val, error);
            }
          });
      },
    },
  },
  mounted() {
    this.$store.commit("skijasi/FETCH_MENU");
    this.$store.commit("skijasi/FETCH_CONFIGURATION_MENU");
  },
};
</script>
<style>
.vs-avatar img {
  object-fit: cover;
  object-position: center;
}
</style>