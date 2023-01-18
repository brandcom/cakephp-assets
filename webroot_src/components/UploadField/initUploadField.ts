import UploadField from './UploadField.vue';
import VueMountingHelper from '../../helpers/VueMountingHelper';

VueMountingHelper.mount([
	{
		vueApp: UploadField,
		cssSelector: '[data-vue-UploadField]',
	},
]);
