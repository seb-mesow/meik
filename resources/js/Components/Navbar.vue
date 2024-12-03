<template>
    <nav class="navbar">
      <ul class="nav-list">
        <li class="nav-item">
          <i class="icon-exponate"></i>
          <span>Exponate</span>
        </li>
        <li class="nav-item">
          <i class="icon-nutzer"></i>
          <span>Nutzer</span>
        </li>
        <li class="nav-item">
          <i class="icon-standort"></i>
          <span>Standort</span>
        </li>
      </ul>
    </nav>
  </template>
  
  <script>
  export default {
    name: "Navbar",
  };
  </script>
  
  <style scoped>
  .navbar {
    width: 100px;
    height: 100vh;
    background-color: #003366; /* Dunkelblau */
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
  }
  
  .nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 30px;
  }
  
  .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
    font-family: Arial, sans-serif;
    font-size: 14px;
    text-align: center;
    cursor: pointer;
  }
  
  .nav-item i {
    font-size: 24px;
    margin-bottom: 8px;
  }
  
  /* Beispiel-Icons */
  .icon-exponate::before {
    content: "📦";
  }
  
  .icon-nutzer::before {
    content: "👤";
  }
  
  .icon-standort::before {
    content: "📍";
  }
  </style>
  