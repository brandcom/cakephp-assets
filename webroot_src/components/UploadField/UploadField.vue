<template>
	<div class="upload-field">
		<div class="field-content">
			<div class="current-asset">
				<div v-if="currentAsset?.filename">
					<img
						v-if="currentAsset.thumbnail"
						:src="currentAsset.thumbnail"
						:alt="currentAsset.filename"
					>
					<p>{{ currentAsset.filename }}</p>
				</div>
				<p v-else>
					No file selected yet.
				</p>
			</div>
			<label class="upload-new button">
				{{ i18n.uploadNew }}
				<input
					v-if="keepCurrentUploadField"
					class="sr-only"
					type="file"
					@change="updateCurrentAsset"
					:name="originalFields.file.name"
				>
			</label>
			<button
				class="remove-selected button"
				v-if="currentAsset?.filename"
				type="button"
				title="empty"
				@click="emptyCurrentAsset"
			>
				X
			</button>
		</div>
	</div>
</template>

<script setup>

import {onMounted, ref} from "vue";

const props = defineProps({
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
});

const keepCurrentUploadField = ref(true);

const originalFields = ref({
	file: {
		name: null,
		id: null,
	}
});

const currentAsset = ref(null);

const setOriginalFields = () => {
	const domParser = new DOMParser();
	const template = domParser.parseFromString(props.originalFieldsTemplate, 'text/html');

	const fileInput = template.querySelector('.js-assets-upload-field');

	originalFields.value = {
		file: {
			name: fileInput.name,
			id: fileInput.id,
		},
	}
};

const emptyCurrentAsset = () => {
	keepCurrentUploadField.value = false;
	currentAsset.value = null;
	setTimeout(() => {
		keepCurrentUploadField.value = true;
	}, 50);
};

const updateCurrentAsset = (e) => {
	currentAsset.value = {
		filename: e.target.files[0].name,
	};
};

onMounted(() => {
	if (props.fileInfo.asset?.filename) {
		currentAsset.value = {
			 filename: props.fileInfo.asset.filename,
		}
	}
	setOriginalFields();
});
</script>
<style lang="scss" scoped>
.field-content {
	position: relative;
	z-index: 20;
	border: 1px solid #000;
	border-radius: .3rem;
	display: flex;
	gap: 1rem;
}

.current-asset {
	padding: .25rem;
}

label.upload-new {
	padding: .25rem;
	flex-grow: 1;
	text-align: center;
}

.remove-selected {
	aspect-ratio: 1 / 1;
	flex-shrink: 0;
	height: 100%;
	width: 2.5rem;
}

.button {
	cursor: pointer;

	&:focus,
	&:hover {
		transition: color .2s ease-out;
		background-color: #eee;
	}
}

.sr-only {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border-width: 0;
}
</style>
