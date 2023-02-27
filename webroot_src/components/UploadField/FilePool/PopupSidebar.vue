<template>
	<section
		v-if="isOpen"
		class="popup-sidebar"
	>
		<button
			@click="close"
		>
			X
		</button>
		<header>
			<h2>
				Entity Info
			</h2>
			<ul>
				<li>
					<strong>Model:</strong>
					{{ context.entityModel }}
				</li>
				<li>
					<strong>Entity ID:</strong>
					{{ context.entityId }}
				</li>
				<li>
					<strong>Field:</strong>
					{{ context.associationName }}
				</li>
			</ul>
		</header>
		<footer>
			<div v-if="context.assetId">
				<h3>Currently Selected:</h3>
				{{ context.assetId }}
			</div>
		</footer>
	</section>
</template>

<script setup>

import {ref} from "vue";

const props = defineProps({
	context: {
		type: Object,
		required: true,
	}
});

const isOpen = ref(true);

const close = () => {
	isOpen.value = false;
}

</script>

<style scoped>
.popup-sidebar {
	position: absolute;
	width: 100%;
	max-width: 550px;
	z-index: 50;
	background-color: #bbb;
	inset: 0;
	left: auto;
	height: 100%;
	padding: 1rem;
}
</style>
