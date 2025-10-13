<?php /* Sell Pets page (focused create listing UI, reuses explore styles) */ ?>
<link rel="stylesheet" href="/PETVET/public/css/pet-owner/explore-pets.css">
<div class="main-content">
  <header class="page-header">
    <div class="title-wrap"><h2>Create a Pet Listing</h2><p class="subtitle">Fill the form below to publish a new pet listing (demo only).</p></div>
    <div class="actions"><a class="btn outline" href="/PETVET/index.php?module=pet-owner&page=explore-pets">Back to Explore</a></div>
  </header>
  <section>
    <form id="sellFormSingle">
      <div class="form-grid">
        <label>Pet Name<input type="text" name="name" required></label>
        <label>Species<select name="species" required><option>Dog</option><option>Cat</option><option>Bird</option><option>Other</option></select></label>
        <label>Breed<input type="text" name="breed" required></label>
        <label>Age<input type="text" name="age" placeholder="e.g., 2y" required></label>
        <label>Gender<select name="gender"><option>Male</option><option>Female</option></select></label>
        <label>Price (Rs)<input type="number" name="price" min="0" step="500" required></label>
        <label class="full">Health Badges
          <div class="checks">
            <label><input type="checkbox" name="badges[]" value="Vaccinated"> Vaccinated</label>
            <label><input type="checkbox" name="badges[]" value="Microchipped"> Microchipped</label>
          </div>
        </label>
        <label class="full">Short Description<textarea name="desc" rows="3" required></textarea></label>
        <label class="full">Image URL<input type="url" name="image" placeholder="https://example.com/pet.jpg" required></label>
      </div>
      <div class="modal-actions" style="margin-top:18px"><button type="reset" class="btn outline">Reset</button><button type="submit" class="btn primary">Publish Listing</button></div>
      <p class="empty" style="margin-top:12px;color:#64748b">Demo only – submission won’t persist.</p>
    </form>
  </section>
</div>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('sellFormSingle');
    form.addEventListener('submit', e=>{
      e.preventDefault();
      alert('Listing published (demo only). Returning to Explore.');
      location.href = '/PETVET/index.php?module=pet-owner&page=explore-pets';
    });
  });
</script>
