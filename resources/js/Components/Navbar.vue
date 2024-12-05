<script>
import DropdownLink from '@/Components/Dropdown.vue';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
export default {
  methods: {
    navigateToProfile() {
      this.$inertia.visit(this.route('profile.edit')); // Navigiert zur Profil-Seite
    },
    navigateToExhibits(){
      this.$inertia.visit(this.route('exhibit.overview'));
    },
    navigateToLocation(){
      this.$inertia.visit(this.route('locations.all'));
    },

  },
  //name: "Navbar",
};

</script>

<template>
  <div class="fixed-navbar">
    <nav class="navbar">
      <ul class="nav-list">
        <!-- Card für Exponate -->
        <li class="nav-item">
          <div class="card card-exponate" @click="navigateToExhibits">
            <img src="/resources/graphic/exponat.png" alt="Exponate Icon" class="card-image" />
            <div class="card-content">
              <span>Exponate</span>
            </div>
          </div>
        </li>

        <!-- Card für Nutzer -->
        <li class="nav-item">
          <div class="card card-user" @click="navigateToProfile">
            <img src="/resources/graphic/user1.png" alt="Nutzer Icon" class="card-image" />
              <DropdownLink  :href="route('profile.edit')">
               </DropdownLink>
            <div class="card-content">
              <span>Nutzer</span>
            </div>
          </div>
        </li>

        <!-- Card für Exponat-Standort -->
        <li class="nav-item">
          <div class="card card-standort" @click="navigateToLocation">
            <img src="/resources/graphic/exponatOrt.png" alt="Standort Icon" class="card-image" />
            <div class="card-content">
              <span>Exponat-Standort</span>
            </div>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</template>


<style scoped>
.fixed-navbar {
  position: fixed;
  top: 0;
  left: 0;
}

.navbar {
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
