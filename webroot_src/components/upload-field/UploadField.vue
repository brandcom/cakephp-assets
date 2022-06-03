<template>
    Upload Field!
</template>

<script>
/**
 * FIXME component-style does not work  - 404 error as the /assets part in the URL is missing.
 */

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
