<script lang="ts" setup>
// Bild-Daten definieren
import fv_logo_url from '../../images/FV_ISC.jpg'; // Pfad zum Bild 1
import isc_log_url from '../../images/Logo_isc_300x122.jpg'; // Pfad zum Bild 2
const imageAltOne = 'Beschreibung des ersten Bildes'; // Alternativer Text für Bild 1
const imageAltTwo = 'Beschreibung des zweiten Bildes'; // Alternativer Text für Bild 2
</script>

<template>
	<div class="image-wrapper">
		<!-- Container für Bild 1 -->
		<div class="image-container image-container-one">
			<img :src="fv_logo_url" :alt="imageAltOne" class="image-class image-one" />
		</div>
		<!-- Container für Bild 2 -->
		<div class="image-container image-container-two">
			<img :src="isc_log_url" :alt="imageAltTwo" class="image-class image-two" />
		</div>
	</div>
</template>

<style lang="css" scoped>
/* Wrapper für alle Bilder */
.image-wrapper {
	display: flex;
	flex-direction: block;
	/* flex-direction: block; Bilder untereinander anordnen */
	gap: 2px; /* Abstand zwischen den Containern */
}
/* Individueller Container für jedes Bild */
.image-container {
	display: flex;
	flex-direction: block;
	justify-content: center;
	align-items: center;
	border: 0px solid #ccc; /* Optional: Rahmen um Container */
	padding: 0px; /* Optional: Abstand innerhalb des Containers */
	max-width: 20%; /* Container passt sich der Breite des Viewports an */
	position: relative;
}
/* Bild-Stile */
.image-class {
  max-width: 100%; /* Bild passt sich an den Container an */
  height: 100%;    /* Proportionale Skalierung */
}
/* Spezifische Anpassungen für Bild 1 */
.image-container-one {
	background-color: #f9f9f9; /* Beispiel: individueller Hintergrund */
	position: relative;
	height: 110%;
	left: 20%;
}
.image-one {
	width: 5rem;
	max-width: 20rem; /* Individuelle Breite für Bild 1 */
	height: auto;
}
/* Spezifische Anpassungen für Bild 2 */
.image-container-two {
	/*background-color: #ffffff; /* Beispiel: individueller Hintergrund */
	position: relative;
	height: 132%;
	right: -310%;
}
.image-two {
	width: 16rem;
	max-width: 12rem; /* Individuelle Breite für Bild 2 */
	height: auto;
}
</style>
