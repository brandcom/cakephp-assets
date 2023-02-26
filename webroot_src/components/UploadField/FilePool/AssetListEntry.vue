<template>
	<div class="list-entry">
		<img
			class="thumbnail"
			v-if="assetData.thumbnail_link"
			:src="assetData.thumbnail_link"
			:alt="assetData.filename"
		>
		<div>
			<p>
				<strong>
					{{ assetData.filename }}
				</strong>
			</p>
			<a
				:href="assetData.admin_download_link"
			>
				Download
				({{ assetData.file_size_info }})
			</a>
			<button
				@click="onSelectAsset"
				type="button"
			>
				choose
			</button>
			<pre>
				{{ assetData }}
			</pre>
		</div>
	</div>
</template>

<script setup>

const emit = defineEmits(['selectAsset']);

const props = defineProps({
	assetData: {
		type: Object,
		required: true,
	}
});

const onSelectAsset = () => {
	emit('selectAsset', {
		asset: props.assetData,
	});
}

</script>

<style scoped>
.list-entry {
	border: 1px solid #333;
	padding: .5rem;
	display: flex;
	gap: 1rem;
}
.thumbnail {
	width: 8rem;
	flex-shrink: 0;
	object-fit: contain;
	object-position: top;
}
</style>
