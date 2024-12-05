<template>
  <div class="center-container">
    <!-- Card -->
    <div class="card card-headline">
      <img src="/resources/graphic/meikWelcome.png" alt="Headline" class="card-image" />
      <!-- <div class="card-content">
        <span>Centered Card</span>
      </div> -->
    </div>
  </div>
</template>

<script>
export default {
  name: "Centered Card",
};
</script>

<style scoped>
/* Container für Zentrierung */
.center-container {
  display: flex; /* Flexbox für Zentrierung */
  flex-direction: column; /* Vertikale Anordnung von Card und Header */
  justify-content: center; /* Vertikal zentrieren */
  align-items: center; /* Horizontal zentrieren */
  height: 10vh; /* Gesamte Bildschirmhöhe */
  background-color: #003366; /* Hintergrundfarbe */
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
