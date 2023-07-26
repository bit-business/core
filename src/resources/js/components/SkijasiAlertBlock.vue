<template>
  <div class="skijasi-alert__body">
    <p class="skijasi-alert__title">
      <slot name="title"></slot>
    </p>
    <p class="skijasi-alert__description">
      <slot name="desc"></slot>
    </p>
  </div>
</template>
<script>
export default {
  name: "SkijasiAlertBlock",
  components: {},
};
</script>
