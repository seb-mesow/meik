<script setup lang="ts">
import { toggle_dark_mode } from '@/bootstrap';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import Button from 'primevue/button';
</script>

<template>
	<div class="navbar-underlay">
	<nav>
		<div class="navbar">
			<img class="logo" src="/resources/graphic/Logo_isc_300x122.jpg">
			<div>
				<a :href="route('exhibit.overview')">
					<div class="link">
						<div class="icon-box">
							<span class="icon material-symbols-outlined">
								archive
							</span>
						</div>
						<div class="label-box">
							<span class="label">Exponate</span>
						</div>
					</div>
				</a>
				
				<a :href="route('location.overview')">
					<div class="link">
						<div class="icon-box">
							<span class="icon material-symbols-outlined">
								location_on
							</span>
						</div>
						<div class="label-box">
							<span class="label">Standorte</span>
						</div>
					</div>
				</a>
				
				<a :href="route('user.overview')">
					<div class="link">
						<div class="icon-box">
							<span class="icon material-symbols-outlined">
								person
							</span>
						</div>
						<div class="label-box">
							<span>Benutzer</span>
						</div>
					</div>
				</a>
			</div>
			<Button @click="toggle_dark_mode">
				<i id="dark_mode_icon" class="pi pi-sun"/>
			</Button>
			<img class="logo" src="/resources/graphic/FV_ISC.jpg">
		</div>
	</nav>
	</div>
</template>

<style lang="css" scoped>
.navbar-underlay {
	flex-grow: 0;
	flex-shrink: 0;
	flex-basis: 8rem;
}
.navbar {
	width: 8rem;
	height: 100vh;
	position: fixed;
	top: 0;
	left: 0;
	background-color: #003366; /* Dunkelblau */
	display: flex;
	justify-content: space-between;
	flex-direction: column;
	align-items: center;
}
.logo {
	padding: 0.25rem;
}
.link {
	width: 7.5rem;
	height: 2rem;
	display: flow-root;
}
.icon-box {
	display: inline flow-root;
	vertical-align: middle;
	width: 33%;
	text-align: center;
}
.label-box {
	display: inline flow-root;
	width: 67%;
	text-align: center;
}
@media screen and (max-width: 768px) {
	.navbar-underlay {
		flex-basis: 3rem;
	}
	.navbar {
		width: 3rem;
		justify-content: space-around;
	}
	.logo {
		display: none;
	}
	.link {
		width: 3.5rem;
	}
	.icon-box {
		width: 100%;
	}	
	.label-box {
		display: none;
	}
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
