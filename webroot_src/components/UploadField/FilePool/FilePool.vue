<template>
	<div
		v-if="isOpen"
		class="file-pool"
	>
		<button
			@click="close"
			title="close file pool"
			class="close-button"
		></button>
		<div class="popup-wrapper">
			<div class="popup">
				<div class="popup-content">
					<FilePoolContent
						:context="context"
						@select-asset="onSelectAsset"
					/>
				</div>
				<PopupSidebar :context="context"></PopupSidebar>
			</div>
		</div>
	</div>
</template>

<script setup>

import {onMounted, ref} from "vue";
import FilePoolContent from "./FilePoolContent.vue";
import PopupSidebar from "./PopupSidebar.vue";

const isOpen = ref(false);

const context = ref({});

const addEventListeners = () => {
	window.addEventListener('Assets.FilePool.Open', e => {
		context.value = e.detail.context;
		isOpen.value = true;
	});
}

const close = () => {
	isOpen.value = false;
}

const onSelectAsset = ($event) => {
	window.dispatchEvent(new CustomEvent('Assets.FilePool.onSelectAsset', {
		detail: {
			asset: $event.asset,
			context: context.value,
		}
	}));
	isOpen.value = false;
}

onMounted(() => {
	addEventListeners();
});

</script>

<style scoped>
.file-pool {
	position: fixed;
	z-index: 50;
	inset: 0;
}

.close-button {
	@apply absolute inset-0 bg-black opacity-80;
}

.popup-wrapper {
	@apply absolute pointer-events-none inset-0 flex items-center justify-center;
}

.popup {
	position: relative;
	pointer-events: auto;
	flex-grow: 1;
	background-color: #fff;
	color: #000;
	width: 100%;
	height: 100%;
}

@media screen and (min-width: 750px) {
	.popup {
		max-height: 60vh;
		max-width: 75vw;
	}
}

.popup-content {
	padding: 1rem;
	height: 100%;
	overflow-y: auto;
}
</style>
