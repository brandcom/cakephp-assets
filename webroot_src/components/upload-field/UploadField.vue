<template>
    Upload Field!
    <p>
        Test
    </p>
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
<style lang="scss" scoped>
p {
    @apply text-red-500;
}
</style>
