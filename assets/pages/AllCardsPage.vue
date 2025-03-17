<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const currentPage = ref(1);

const totalCards = ref(0);
const limit = 100;

async function loadCards(page = 1) {
    loadingCards.value = true;
    const result = await fetchAllCards(page, limit);
    cards.value = result.cards;
    totalCards.value = result.total;
    currentPage.value = result.page;
    loadingCards.value = false;
}

function nextPage() {
    if (currentPage.value * limit < totalCards.value) {
        loadCards(currentPage.value + 1);
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        loadCards(currentPage.value - 1);
    }
}

onMounted(() => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
            <div class="pagination">
                <button type="button" @click="prevPage">Previous</button>
                <span>{{ currentPage }}</span>
                <button type="button" @click="nextPage">Next</button>
            </div>
        </div>
    </div>
</template>
