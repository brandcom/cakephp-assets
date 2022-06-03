<template>
    Pool!
    <div class="original">
        <div v-html="fieldWrapperTemplate" />
    </div>
</template>

<script>
export default {
  name: 'FilePool',
    props: {
      fieldWrapperTemplate: {
          required: true,
          type: String,
      }
    },
    methods: {
    },
    mounted() {
      console.log(this.fieldWrapperTemplate)
    }
};
</script>

<style scoped>

</style>
