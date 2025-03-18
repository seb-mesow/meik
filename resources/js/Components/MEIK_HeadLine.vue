<script lang="ts" setup>
import welcome_url from '../../images/meikWelcome.png';
</script>

<template>
	<div class="center-container">
		<div class="card card-headline">
			<img :src="welcome_url" alt="Headline" class="card-image" />
		</div>
	</div>
</template>

<style lang="css" scoped>
/* Container für Zentrierung */
.center-container {
	display: flex; /* Flexbox für Zentrierung */
	flex-direction: column; /* Vertikale Anordnung von Card und Header */
	justify-content: center; /* Vertikal zentrieren */
	align-items: center; /* Horizontal zentrieren */
	height: 7rem; /* Gesamte Bildschirmhöhe */
	width: auto;
	
	text-align: center;
}

/* Allgemeines Card-Styling */
.card {
	display: flex;
	flex-direction: column;
	align-items: center;
	background-color: white; /* Hintergrundfarbe für die Card */
	border-radius: 0.5rem;
	padding: 0.5rem;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	width: 28rem; /* Breite der Card */
	height: 120rem;
	text-align: center;
	transition: transform 0.3s, box-shadow 0.3s;
	margin-top: -4rem;
}

/* Bild in der Card */
.card-image {
	width: 1rem; /* Breite des Icons */
	height: auto;
	object-fit: contain; /* Bild skalieren, ohne zu verzerren */
}

/* Card mit spezifischem Styling für die Headline */
.card-headline .card-image {
	width: 30rem; /* Individuelle Breite */
	height: auto; /* Höhe proportional */
}

</style>
