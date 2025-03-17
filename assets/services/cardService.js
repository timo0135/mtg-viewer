export async function fetchAllCards(page = 1, limit = 100, setCode = '') {
    const url = new URL(`/api/card/all`, window.location.origin);
    url.searchParams.append('page', page);
    url.searchParams.append('limit', limit);
    if (setCode) {
        url.searchParams.append('setCode', setCode);
    }
    const response = await fetch(url);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/sets');
    if (!response.ok) throw new Error('Failed to fetch set codes');
    const setCodes = await response.json();
    return setCodes;
}