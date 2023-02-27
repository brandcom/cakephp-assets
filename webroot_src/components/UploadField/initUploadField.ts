import UploadField from './UploadField.vue';
import FilePool from './FilePool/FilePool.vue';
import { createApp } from "vue";

const filePoolDiv = document.createElement('div');
document.body.appendChild(filePoolDiv);
createApp(FilePool).mount(filePoolDiv);

document.querySelectorAll('[data-assets-upload-field]').forEach(el => {
	createApp(UploadField, {
		fileInfo: JSON.parse(el.querySelector('[data-json]')?.textContent as string),
		field: el.querySelector('input'),
	}).mount(el);
});
