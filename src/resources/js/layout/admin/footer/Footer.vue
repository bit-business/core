<template>
  <div class="skijasi-footer__container">
    <div class="skijasi-footer__version">Verzija 1.0.0</div>
    <div>
      <a href="https://hzuts.hr" target="_blank">HZUTS</a> © 2023 Sva prava pridržana.
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
