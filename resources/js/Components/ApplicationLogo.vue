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
  <div class="image-wrapper">
    <!-- Container für Bild 1 -->
    <div class="image-container image-container-one">
      <img :src="imageURL" :alt="imageAltOne" class="image-class image-one" />
    </div>
    
    <!-- Container für Bild 2 -->
    <div class="image-container image-container-two">
      <img :src="imageURI" :alt="imageAltTwo" class="image-class image-two" />
    </div>
  </div>
</template>

<script setup lang="ts">
// Bild-Daten definieren
const imageURL = 'resources/graphic/FV_ISC.jpg'; // Pfad zum Bild 1
const imageURI = 'resources/graphic/Logo_isc_300x122.jpg'; // Pfad zum Bild 2
const imageAltOne = 'Beschreibung des ersten Bildes'; // Alternativer Text für Bild 1
const imageAltTwo = 'Beschreibung des zweiten Bildes'; // Alternativer Text für Bild 2
</script>

<style scoped>
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
  border: 0rem solid #ccc; /* Optional: Rahmen um Container */
  padding: 0rem; /* Optional: Abstand innerhalb des Containers */
  max-width: 290%; /* Container passt sich der Breite des Viewports an */
  position: relative;
  
  
}

/* Bild-Stile */
.image-class {
  max-width: 100%; /* Bild passt sich an den Container an */
  height: 100rem;    /* Proportionale Skalierung */
}

/* Spezifische Anpassungen für Bild 1 */
.image-container-one {
  background-color: #f9f9f9; /* Beispiel: individueller Hintergrund */
  position: relative;
  height: 12.3rem;
  left: -11rem;

}

.image-one {
  width: 11rem;
  max-width: 260rem; /* Individuelle Breite für Bild 1 */
  height: auto;
}

/* Spezifische Anpassungen für Bild 2 */
.image-container-two {
  background-color: #e0e0e0; /* Beispiel: individueller Hintergrund */
  position: relative;
  height: 6.5rem;
  left: -10.4rem;
}

.image-two {
  width: 15.9rem;
  max-width: 260rem; /* Individuelle Breite für Bild 2 */
  height: auto;
  right: 23rem;
}
</style>




<!-- <template>
  <div class="image-container">
    <img :src="imageURL" :alt="imageAlt" class="image-class image-left-one" />
    <img :src="imageURI" :alt="imageAlt" class="image-class image-left-two" />
  </div>
</template>

<style lang="css" scoped>
.image-container {
	display: flex;
	width: 110px;
	justify-content: space-between;
	align-items: center;
<script setup lang="ts">
// Bild-Daten definieren
const imageURL = 'resources/graphic/FV_ISC.jpg'; // Pfad zum Bild
const imageURI = 'resources/graphic/Logo_isc_300x122.jpg';
const imageAlt = 'Beschreibung des Bildes'; // Alternativer Text für das Bild
</script>

