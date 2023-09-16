import Pages from "./../../pages/index.vue";

const prefix = process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
  ? "/" + process.env.MIX_ADMIN_PANEL_ROUTE_PREFIX
  : "/skijasi-dashboard";
const secretLoginPrefix = process.env.MIX_SKIJASI_SECRET_LOGIN_PREFIX 
  ? "/" + process.env.MIX_SKIJASI_SECRET_LOGIN_PREFIX
  : "/skijasi-secret-login"
  
export default [
  {
    path: prefix + "/login",
    name: "AuthLogin",
    component: Pages,
    meta: {
      title: "Prijava",
    },
  },
  {
    path: prefix + "/register",
    name: "AuthRegister",
    component: Pages,
    meta: {
      title: "Registracija",
    },
  },
  {
    path: prefix + "/forgot-password",
    name: "AuthForgotPassword",
    component: Pages,
    meta: {
      title: "Zaboravljena lozinka",
    },
  },
  {
    path: prefix + "/send-contact-form",
    name: "AuthSendContactForm",
    component: Pages,
    meta: {
      title: "Slanje kontakt forme",
    },
  },

  {
    path: prefix + "/reset-password",
    name: "AuthResetPassword",
    component: Pages,
    meta: {
      title: "Resetiranje lozinke",
    },
  },
  {
    path: prefix + "/verify",
    name: "AuthVerify",
    component: Pages,
    meta: {
      title: "Verifikacija Emaila",
    },
  },
  {
    path: prefix + secretLoginPrefix,
    name: "SecretLogin",
    component: Pages,
    meta: {
      title: "Secret Login",
    },
  },
];
