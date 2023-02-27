<template>
	<div class="border my-4 flex">
		<button
			@click="openFilePool"
			type="button"
			class="p-3 grow"
		>
			<span
				class="flex items-center gap-4"
				v-if="asset.filename"
			>
				<img
					v-if="asset.thumbnail"
					:src="asset.thumbnail"
					:alt="asset.filename"
				>
				<span>
					{{ asset.filename }}
				</span>
			</span>
			<span v-else>
				<em>
					No file selected <br>
					open file pool
				</em>
			</span>
		</button>
		<button
			@click="clear"
			type="button"
			class="p-3"
		>
			x
		</button>
	</div>
	<div class="field" v-html="fieldHtml"></div>
</template>

<script setup>

import {computed, onMounted, ref} from "vue";

const props = defineProps({
	fileInfo: {
		type: Object,
		required: true,
	},
	field: {
		type: Object,
		required: true,
	}
});

const asset = ref({});
const field = ref(null);

onMounted(() => {
	asset.value.id = props.fileInfo.asset?.id;
	asset.value.filename = props.fileInfo.asset?.filename;
	asset.value.thumbnail = props.fileInfo.asset?.thumbnail;
	field.value = props.field;
	addEventListeners();
});

const updateField = (attributes = {}) => {
	const copy = field.value;
	field.value = null;
	Object.keys(attributes).forEach(attribute => {
		copy?.setAttribute(attribute, attributes[attribute]);
	});
	field.value = copy;
}

const fieldHtml = computed(() => {
	return field.value?.outerHTML;
});

const clear = () => {
	asset.value = {};
	updateField({
		value: '',
	})
}

const openFilePool = () => {
	window.dispatchEvent(new CustomEvent('Assets.FilePool.Open', {
		detail: {
			context: {
				entityId: props.fileInfo.entityId,
				entityModel: props.fileInfo.entityModel,
				associationName: props.fileInfo.associationName,
				assetId: asset.value?.id,
			},
		}
	}));
}

const addEventListeners = () => {
	window.addEventListener('Assets.FilePool.onSelectAsset', e => {
		const eventAsset = e.detail.asset;
		const eventContext = e.detail.context;
		if (
			eventContext.entityModel !== props.fileInfo.entityModel
			|| eventContext.entityId !== props.fileInfo.entityId
			|| eventContext.associationName !== props.fileInfo.associationName
		) {
			console.error('other context.. skipping.')
			return;
		}

		asset.value.id = eventAsset.id;
		asset.value.filename = eventAsset.filename;
		asset.value.thumbnail = eventAsset.thumbnail_link;
		updateField({
			value: eventAsset.id,
		})
	});
}

</script>
<style lang="scss" scoped>
.field {
	position: absolute;
	height: 1px;
	width: 1px;
	opacity: 0;
	pointer-events: none;
	top: -10000px;
	left: -10000px;
}
</style>
