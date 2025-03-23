const brandsContainer = document.getElementById('brand-list');
const modelsContainer = document.getElementById('model-list');
const brandInput = document.getElementById('brand');
const modelInput = document.getElementById('model');
const modelWrapper = document.querySelector('#model').closest('.autocomplete-wrapper');

async function fetchAndUpdateOptions(query, url, container, input, brand = '') {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ query, brand })
    });
    const items = await response.json();
    container.innerHTML = '';

    items.forEach(item => {
        const optionElement = document.createElement("div");
        optionElement.selectable = true;
        optionElement.tabIndex = 0;
        optionElement.textContent = item;
        container.appendChild(optionElement);

        const selectOption = () => {
            input.value = item.split("(")[0].trim();
            input.dispatchEvent(new Event('change')); // Manually dispatch change event
            container.classList.add('hidden');
            const nextInput = input.parentElement.parentElement.nextElementSibling.querySelector('input:not([type=disabled]):not([type=hidden]):not([readonly])');
            if (nextInput) {
                nextInput.focus();
            }
        };

        optionElement.addEventListener('click', selectOption);
        optionElement.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                selectOption();
            }
        });
    });
}

brandInput.addEventListener('input', debounce((event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getBrandsLike', brandsContainer, brandInput);
    modelWrapper.classList.add('disabled');
    brandsContainer.classList.remove('hidden');
}, 300));

brandInput.addEventListener("change", (event) => {
    const selectedBrand = event.target.value.trim();
    const options = Array.from(brandsContainer.children).map(option => option.textContent.split("(")[0].trim());

    modelInput.value = '';
    if (options.includes(selectedBrand)) {
        modelWrapper.classList.remove('disabled');
        modelInput.disabled = false;
        modelInput.focus();
    } else {
        modelWrapper.classList.add('disabled');
        modelInput.disabled = true;
    }
});

brandInput.addEventListener('focus', (event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getBrandsLike', brandsContainer, brandInput);
    brandsContainer.classList.remove('hidden');
});

modelInput.addEventListener('input', debounce((event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getModelsLike', modelsContainer, modelInput, brandInput.value);
    modelsContainer.classList.remove('hidden');
}, 300));

modelInput.addEventListener('focus', (event) => {
    fetchAndUpdateOptions(event.target.value, '/api/getModelsLike', modelsContainer, modelInput, brandInput.value);
    modelsContainer.classList.remove('hidden');
});

filtersForm.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && event.target.type !== 'submit') {
        event.preventDefault();
    }
});