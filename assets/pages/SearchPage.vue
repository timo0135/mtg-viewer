<script setup>
import { ref, watch } from 'vue';

const searchQuery = ref('');
const cards = ref([]);
const loadingCards = ref(false);

watch(searchQuery, async (newQuery) => {
  if (newQuery.length >= 3) {
    loadingCards.value = true;
    try {
      const response = await fetch(`/api/card/search?name=${encodeURIComponent(newQuery)}`);
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await response.json();
      cards.value = data;
    } catch (error) {
      console.error('Error fetching cards:', error);
    } finally {
      loadingCards.value = false;
    }
  } else {
    cards.value = [];
  }
});
</script>

<template>
  <div>
    <h1>Rechercher une Carte</h1>
    <input v-model="searchQuery" placeholder="Entrez le nom de la carte" />
  </div>
  <div class="card-list">
    <div v-if="loadingCards">Loading...</div>
    <div v-else>
      <div class="card" v-for="card in cards" :key="card.id">
        <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{ card.uuid }} </router-link>
      </div>
    </div>
  </div>
</template>