<script setup lang="ts">
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import ResponsiveNavLink from './ResponsiveNavLink.vue';
</script>

<template>
	<div class="navbar-underlay"/>
	<nav>
		<div class="navbar flex flex-col justify-between">
			<div class="p-1">
				<img src="/resources/graphic/Logo_isc_300x122.jpg">
			</div>
			
			<div class="gap-8">
				<ResponsiveNavLink :href="route('exhibit.overview')" class="flex justify-between items-center p-4 text-white">
					<span class="material-symbols-outlined pr-2">
						archive
					</span>
					<span>Exponate</span>
				</ResponsiveNavLink>
				
				<ResponsiveNavLink :href="route('location.overview')" class="flex justify-between items-center p-4 text-white">
					<span class="material-symbols-outlined pr-2">
						location_on
					</span>
					<span>Standorte</span>
				</ResponsiveNavLink>
				
				<ResponsiveNavLink :href="route('user.overview')" class="flex justify-between items-center p-4 text-white">
					<span class="material-symbols-outlined pr-2">
						person
					</span>
					<span>Benutzer</span>
				</ResponsiveNavLink>
			</div>

			<div class="justify-self-end p-1">
				<img src="/resources/graphic/FV_ISC.jpg">
			</div>
		</div>
	</nav>
</template>

<style scoped lang="css">
.navbar-underlay {
	width: 9rem;
}

.navbar {
	width: 9rem;
	height: 100vh;
	position: fixed;
	top: 0;
	left: 0;
	justify-content: space-between;
	background-color: #003366; /* Dunkelblau */
	align-items: center;
}

.nav-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.7rem;
}

.nav-item {
	display: flex;
	justify-content: center;
	width: 100%;
}

/* Allgemeines Card-Styling */
.card {
	display: flex;
	flex-direction: column;
	align-items:center;
	background-color: rgb(122, 126, 173); /* Hintergrundfarbe für die Card */
	border-radius: 0.5rem;
	padding: 0.5rem;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	width: 8rem; /* Breite der Card */
	text-align: center;
	transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
	transform: scale(1.05); /* Vergrößern bei Hover */
	box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
}

/* Styling für alle Bilder */
.card-image {
	/* object-fit: contain; Bild skalieren, ohne zu verzerren */
	margin-bottom: 0.3rem; /* Abstand zum Text */
}

/* Spezifisches Styling für Exponate-Card */
.card-exponate .card-image {
	width: 8rem; /* Individuelle Breite */
	height: auto; /* Individuelle Höhe */
}

/* Spezifisches Styling für Nutzer-Card */
.card-user .card-image {
	width: 3.6rem; /* Individuelle Breite */
	height: auto; /* Höhe proportional */
}

/* Spezifisches Styling für Standort-Card */
.card-standort .card-image {
	width: 3.7rem; /* Individuelle Breite */
	height: auto; /* Höhe proportional */
}

/* Inhalt der Card (Text) */
.card-content span {
	font-size: 1.2rem;
	font-weight: bold;
	color: #ffffff; /* Textfarbe */
}
</style>
