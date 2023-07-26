const exported = {};

const pluginsEnv = process.env.MIX_SKIJASI_PLUGINS
  ? process.env.MIX_SKIJASI_PLUGINS
  : null;

// DYNAMIC IMPORT SKIJASI API HELPER
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
  console.info("There is no skijasi api helper");
}

// DYNAMIC IMPORT CUSTOM API HELPER
try {
  const modules = require.context(
    "../../../../../../../resources/js/skijasi/api",
    false,
    /\.js$/
  ); //
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
  console.info("Failed to load custom api module", error);
}

// DYNAMIC IMPORT CUSTOM PLUGINS API HELPER
try {
  if (pluginsEnv) {
    const plugins = process.env.MIX_SKIJASI_PLUGINS.split(",");
    if (plugins && plugins.length > 0) {
      plugins.forEach((plugin) => {
        const modules = require("../../../../../" +
          plugin +
          "/src/resources/js/api/").default;
        Object.keys(modules).forEach((module, index) => {
          exported[module] = modules[module];
        });
      });
    }
  }
} catch (error) {
  console.info("There is no skijasi plugins api helper", error);
}

export default exported;
