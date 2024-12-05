<script setup lang="ts">
import 'material-design-icons-iconfont/dist/material-design-icons.css';
</script>

<template>
	<div class="navbar-underlay"/>
	<nav class="navbar">
		<ul class="nav-list">
			<!-- Card für Exponate -->
			<li class="nav-item">
				<a class="card card-exponate" :href="route('exhibit.overview')">
					<img src="/resources/graphic/exponat.png" alt="Exponate Icon" class="card-image" />
					<div class="card-content">
						<span>Exponate</span>
					</div>
				</a>
			</li>
			
			<!-- Card für Exponat-Standort -->
			<li class="nav-item">
				<a class="card card-standort" :href="route('location.overview')">
					<img src="/resources/graphic/exponatOrt.png" alt="Standort Icon" class="card-image" />
					<div class="card-content">
						<span>Standorte</span>
					</div>
				</a>
			</li>
			
			<!-- Card für Nutzer -->
			<li class="nav-item">
				<a class="card card-user" :href="route('user.overview')">
					<img src="/resources/graphic/user1.png" alt="Nutzer Icon" class="card-image" />
					<div class="card-content">
						<span>Nutzer</span>
					</div>
				</a>
			</li>
		</ul>
	</nav>
</template>

<style scoped lang="css">
.navbar-underlay {
	width: 9rem;
}

.navbar {
	position: fixed;
	width: 9rem;
	height: 100vh;
	background-color: #003366; /* Dunkelblau */
	display: flex;
	flex-direction: column;
	align-items: center;
	padding-top: 7rem;
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
