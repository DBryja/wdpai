const brandsContainer = document.getElementById('brand-list');
const modelsContainer = document.getElementById('model-list');
const brandInput = document.getElementById('brand');
const modelInput = document.getElementById('model');

async function fetchAndUpdateOptions(query, url, container, input) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ query })
    });
    const items = await response.json();
    container.innerHTML = ''; // Clear previous options

    if (query === '') {
        const emptyOption = document.createElement("option");
        emptyOption.value = '';
        emptyOption.textContent = '';
        container.appendChild(emptyOption);
    }

    items.forEach(item => {
        const optionElement = document.createElement("option");
        optionElement.value = item;
        optionElement.textContent = item;
        container.appendChild(optionElement);
    });

    if (items.length === 1) {
        input.value = items[0];
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchAndUpdateOptions('', '/api/getBrandsLike', brandsContainer, brandInput);
    fetchAndUpdateOptions('', '/api/getModelsLike', modelsContainer, modelInput);
})

brandInput.addEventListener('input', debounce((event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getBrandsLike', brandsContainer, brandInput);
}, 200));

modelInput.addEventListener('input', debounce((event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getModelsLike', modelsContainer, modelInput);
}, 200));

brandsContainer.addEventListener('change', (event) => {
    brandInput.value = event.target.value;
});

modelsContainer.addEventListener('change', (event) => {
    modelInput.value = event.target.value;
});