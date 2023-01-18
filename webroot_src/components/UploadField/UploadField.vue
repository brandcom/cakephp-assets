<template>
    <div class="upload-field relative z-20 border rounded flex gap-3 my-4">
        <div class="current-asset p-2">
            <a
                :href="currentAsset.downloadLink"
                target="_blank"
                v-if="currentAsset.id"
            >
                <img :src="currentAsset.thumbnail" :alt="currentAsset.filename">
                <p>
                    {{ currentAsset.mimetype }}, <br>
                    {{ currentAsset.fileSize }}
                </p>
            </a>
            <div
                v-else
            >
                No file selected yet.
            </div>
        </div>
        <button
            type="button"
            class="h-full p-2 px-6 border-none rounded-none transition-color hover:bg-neutral-200"
            @click="toggleFilepool"
        >
            {{ i18n.chooseExisting }}
        </button>
        <div class="relative grow">
            <label class="absolute inset-0 h-full w-full flex justify-center items-center hover:bg-neutral-100">
                <div>
                    {{ i18n.uploadNew }}
                </div>
                <div class="sr-only">
                    <input
                        type="file"
                        :id="originalFields.file.id"
                        :name="originalFields.file.name"
                    >
                </div>
            </label>
        </div>
        <button
            type="button"
            class="h-full p-4"
            title="empty"
            @click="emptyCurrentAsset"
        >
            X
        </button>
    </div>
    <div class="existing-asset sr-only">
        <p>{{originalFields.existingAsset}}</p>
        <input
            type="text"
            :name="originalFields.existingAsset.name"
            :id="originalFields.existingAsset.id"
            :value="currentAsset.id"
        >
    </div>
    <section
        class="filepool-popup fixed z-50 inset-0 w-screen h-screen flex items-center justify-center"
        v-if="showFilepool"
    >
        <button
            class="filepool-popup-close absolute z-10 inset-0 w-full h-full bg-black opacity-40"
            @click="toggleFilepool"
        />
        <div class="filepool-popup-content relative z-50 border bg-white p-4 w-full lg:max-w-screen-md max-h-screen overflow-y-auto">
            <FilePool />
        </div>
    </section>
</template>

<script>

import FilePool from '../file-pool/FilePool.vue';

export default {
    name: 'UploadField',
    components: {
        FilePool
    },
    props: {
        fileInfo: {
            required: true,
            type: Object,
        },
        originalFieldsTemplate: {
            required: true,
            type: String,
        },
        i18n: {
            required: true,
            type: Object,
        },
    },
    data() {
        return {
            showFilepool: false,
            originalFields: {
                file: {
                    name: null,
                    id: null,
                },
                existingAsset: {
                    name: null,
                    id: null,
                    value: false,
                },
            },
            currentAsset: {
                id: null,
                filename: null,
                mimetype: null,
                fileSize: null,
                thumbnail: null,
                downloadLink: null,
                associationName: null,
                parent: {
                    id: null,
                    model: null,
                }
            },
        };
    },
    methods: {
        /**
         * Sets the title, id etc. from the fields delivered by CakePHP to keep
         * a valid form for the FormProtectionComponent
         */
        setOriginalFields() {
            const domParser = new DOMParser();
            const template = domParser.parseFromString(this.originalFieldsTemplate, 'text/html');

            const fileInput = template.querySelector('.js-assets-upload-field');
            const existingAssetInput = template.querySelector('.js-assets-existing-file');

            if (!fileInput || !existingAssetInput) {
                throw new Error('Error in template. Could not find original fields template.');
            }

            this.originalFields = {
                file: {
                    name: fileInput.name,
                    id: fileInput.id,
                },
                existingAsset: {
                    name: existingAssetInput.name,
                    id: existingAssetInput.id,
                    value: existingAssetInput.value,
                },
            };
        },
        setCurrentAsset() {

            if (!this.originalFields.existingAsset.value) {
                return;
            }

            this.currentAsset = {
                id: this.originalFields.existingAsset.value,
                filename: this.fileInfo.asset.filename,
                mimetype: this.fileInfo.asset.mimetype,
                fileSize: this.fileInfo.asset.fileSize,
                thumbnail: this.fileInfo.asset.thumbnail,
                downloadLink: this.fileInfo.asset.downloadLink,
                associationName: this.fileInfo.associationName,
                parent: {
                    id: this.fileInfo.entityId,
                    model: this.fileInfo.entityModel,
                }
            };
        },
        emptyCurrentAsset() {
            this.currentAsset.id = null;
        },
        toggleFilepool() {
            this.showFilepool = !this.showFilepool;
        }
    },
    mounted() {
        this.setOriginalFields();
        this.setCurrentAsset();
    },
};
</script>
<style lang="scss" scoped>
</style>
