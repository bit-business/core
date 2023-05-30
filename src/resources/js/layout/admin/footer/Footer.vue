<template>
  <div class="skijasi-footer__container">
    <div class="skijasi-footer__version">Version 1.0.0</div>
    <div>
      Copyright © 2023
      <a href="https://hzuts.hr" target="_blank">HZUTS</a>. Sva prava pridržana.
    </div>
  </div>
</template>

<script>
import composer from "../../../../../../composer.json";

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Footer",
  computed: {
    version() {
      return composer.version;
    },
  },
};
</script>
