import Vue from "vue";
import Vuex from "vuex";
import _ from "lodash";

const exported = {};

const pluginsEnv = process.env.MIX_SKIJASI_PLUGINS
  ? process.env.MIX_SKIJASI_PLUGINS
  : null;

// DYNAMIC IMPORT SKIJASI STORES
try {
  const modules = require.context("./modules", false, /\.js$/); //
  modules.keys().forEach((fileName) => {
    const property = fileName
      .replace("./", "")
      .replace(".js", "")
      .replace(/([a-z])([A-Z])/g, "$1-$2") // get all lowercase letters that are near to uppercase ones
      .replace(/[\s_]+/g, "-") // replace all spaces and low dash
      .replace(/^\.\/_/, "")
      .replace(/\.\w+$/, "")
      .split("-")
      .map((word, index) => {
        if (index > 0) {
          return word.charAt(0).toUpperCase() + word.slice(1);
        } else {
          return word;
        }
      })
      .join("");
    exported[property] = modules(fileName).default;
  });
} catch (error) {
  console.info("Failed to load skijasi stores", error);
}

// DYNAMIC IMPORT CUSTOM STORES
try {
  const customModules = require.context(
    "../../../../../../../resources/js/skijasi/stores",
    false,
    /\.js$/
  ); //
  customModules.keys().forEach((fileName) => {
    const property = fileName
      .replace("./", "")
      .replace(".js", "")
      .replace(/([a-z])([A-Z])/g, "$1-$2") // get all lowercase letters that are near to uppercase ones
      .replace(/[\s_]+/g, "-") // replace all spaces and low dash
      .replace(/^\.\/_/, "")
      .replace(/\.\w+$/, "")
      .split("-")
      .map((word, index) => {
        if (index > 0) {
          return word.charAt(0).toUpperCase() + word.slice(1);
        } else {
          return word;
        }
      })
      .join("");
    exported[property] = customModules(fileName).default;
  });
} catch (error) {
  console.info("Failed to load custom stores", error);
}

// DYNAMIC IMPORT SKIJASI PLUGINS STORES
try {
  if (pluginsEnv) {
    const plugins = process.env.MIX_SKIJASI_PLUGINS.split(",");
    if (plugins && plugins.length > 0) {
      plugins.forEach((plugin) => {
        const modules = require("../../../../../" +
          plugin +
          "/src/resources/js/store/skijasi.js").default;
        exported.skijasi = _.merge(exported.skijasi, modules);
      });
    }
  }
} catch (error) {
  console.info("Failed to load custom stores", error);
}

Vue.use(Vuex);
/* eslint-disable */
export default new Vuex.Store({
  modules: exported,
});
