<template>
  <div
    class="skijasi-maintenance__container--full skijasi-maintenance__container"
  >
    <img :src="`${maintenanceImg}`" alt="Maintenance Icon" />
    <h1 class="skijasi-maintenance__text">We are under <br />maintenance</h1>
  </div>
</template>

<script>
export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: "Maintenance",
  computed: {
    maintenanceImg() {
      const config = this.$store.getters["skijasi/getConfig"];
      return config.maintenanceImage;
    },
  },
};
</script>
