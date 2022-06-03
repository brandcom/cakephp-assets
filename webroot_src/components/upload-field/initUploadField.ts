import UploadField from '../upload-field/UploadField.vue';
import VueMountingHelper from '../../helpers/VueMountingHelper';

export default function initUploadField() {
    VueMountingHelper.mount([
        {
            vueApp: UploadField,
            cssSelector: '[data-vue-upload-field]',
        },
    ]);
}
