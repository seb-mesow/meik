<script setup lang="ts">
// Bild-Daten definieren
const imageURL = '/resources/graphic/FV_ISC.jpg'; // Pfad zum Bild
const imageURI = '/resources/graphic/Logo_isc_300x122.jpg';
const imageAlt = 'Beschreibung des Bildes'; // Alternativer Text für das Bild
</script>

<template>
	<div class="image-container">
		<img :src="imageURL" :alt="imageAlt" class="image-class" />
		<img :src="imageURI" :alt="imageAlt" class="image-class" />   
	</div>
</template>

<style lang="css" scoped>
.image-container {
	display: flex;
	width: 110px;
	justify-content: space-between;
	align-items: center;
}

/*.image-class {
  width: 100px; /* Beispiel-Breite des Bildes */
  /*height: auto;
}*/
</style>
