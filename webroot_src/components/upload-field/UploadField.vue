<template>
    Upload Field!
</template>

<script>
export default {
    name: 'UploadField',
    props: {
        fileInfo: {
            required: true,
            type: Object,
        },
        originalFields: {
            required: true,
            type: String,
        }
    },
    methods: {
    },
    mounted() {
        console.log(this.fileInfo)
        console.log(this.originalFields)
    }
};
</script>

<style scoped>

</style>
