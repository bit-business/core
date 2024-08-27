import Pages from "./../../pages/index.vue";
import PrijavaNaDogadaj from '../../../../../../../../vendor/skijasi/commerce-theme/src/resources/app/pages/prijavanadogadaj.vue';


const adminPanelRoutePrefix = process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
  ? "/" + process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
  : "/skijasi-dashboard";
const defaultMenuPrefix = process.env.MIX_DEFAULT_MENU
  ? "/" + process.env.MIX_DEFAULT_MENU
  : "/general";
const prefix = `${adminPanelRoutePrefix}${defaultMenuPrefix}`;

export default [
  {
    path: prefix + "/:slug",
    name: "CrudGeneratedBrowse",
    component: Pages,
    meta: {
      title: "HZUTS Baza",
    },
  },
  {
    path: prefix + "/:slug/bin",
    name: "CrudGeneratedBrowseBin",
    component: Pages,
    meta: {
      title: "Browse Recycle",
    },
  },
  {
    path: prefix + "/:slug/add",
    name: "CrudGeneratedAdd",
    component: Pages,
    meta: {
      title: "Dodavanje",
    },
  },
  {
    path: prefix + "/:slug/sort",
    name: "CrudGeneratedSort",
    component: Pages,
    meta: {
      title: "Sortiranje",
    },
  },
  {
    path: prefix + "/:slug/:id",
    name: "CrudGeneratedRead",
    component: Pages,
    meta: {
      title: "Pregled korisnika",
    },
  },
  {
    path: prefix + "/:slug/:id/edit",
    name: "CrudGeneratedEdit",
    component: Pages,
    meta: {
      title: "Promjena podataka",
    },
  },



  {
    path: "/prijavanadogadaj/:formId",
    name: "PrijavaNaDogadaj",
    component: PrijavaNaDogadaj,
    meta: {
      title: "Prijava na događaj",
    },
  },
];
