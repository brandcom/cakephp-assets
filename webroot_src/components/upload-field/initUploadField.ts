import { createApp } from 'vue';
import FilePool from '../file-pool/FilePool.vue';

export default function initUploadField() {
    document.querySelectorAll('.js-assets-upload-wrapper').forEach(wrapper => {
        createApp(FilePool, {
            fieldWrapperTemplate: wrapper.outerHTML,
        }).mount(wrapper);
    });
}
