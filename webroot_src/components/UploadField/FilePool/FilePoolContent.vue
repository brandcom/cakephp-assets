<template>
	<ul>
		<li>
			<button
				type="button"
				@click="fetchRecent"
			>
				recent
			</button>
		</li>
		<li>
			<button
				type="button"
				@click="fetchOldest"
			>
				oldest
			</button>
		</li>
	</ul>
	<ul v-if="assets.length">
		<li v-for="asset in assets">
			<AssetListEntry
				:asset-data="asset"
				@select-asset="onSelectAsset"
			/>
		</li>
	</ul>
</template>

<script setup>

import {onMounted, ref} from "vue";
import FilePoolRequest from "./helpers/FilePoolRequest";
import AssetListEntry from "./AssetListEntry.vue";

const props = defineProps({
	context: {
		type: Object,
		required: true,
	}
});

const assets = ref([]);
const conditions = ref({
	'filename LIKE': '%flow%',
});
const emit = defineEmits(['selectAsset']);

onMounted(() => {
	fetchEntries();
});

const fetchEntries = (conditions = {}, order) => {
	assets.value = [];
	FilePoolRequest.query({
		context: props.context,
		conditions: conditions,
		order: order,
	})
		.then(response => {
			if (true !== response.data.success) {
				throw new Error('request went wrong');
			}
			assets.value = response.data.assets;
		})
		.catch(error => {
			console.error('Error fetching files.', error);
		});
}

const fetchRecent = () => {
	fetchEntries();
}
const fetchOldest = () => {
	fetchEntries({}, 'Assets.created ASC');
}

const onSelectAsset = ($event) => {
	emit('selectAsset', $event);
}

</script>

<style scoped>
ul {
	list-style: none;
	padding: 0;
}

</style>
