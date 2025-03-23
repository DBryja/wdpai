document.addEventListener('DOMContentLoaded', () => {
    const filtersForm = document.getElementById('filters-form');
    const carsList = document.querySelector('.cars-list');
    const loader = document.getElementById('loader');
    const currentPageInput = document.getElementById('current-page');
    let isLoading = false;

    const fetchCars = async () => {
        const formData = new FormData(filtersForm);
        const response = await fetch('/api/getCarsByAttributes', {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        return await response.json();
    };

    const loadMoreCars = async () => {
        if (isLoading) return;
        isLoading = true;
        const cars = await fetchCars();
        if (cars.length > 0) {
            carsList.innerHTML += cars.map(car => `
                <a href="/car?id=${car.id}" class="car-card">
                    <div class="car-card__image">
                    ${car.images.length ? `<img src="${car.images[0]}" alt="${car.title}"/>` : ''}
                    </div>
                    <h3 class="car-card__title">${car.title}</h3>
                    <div class="car-card__details">
                        <p class="car-card__price">Price: ${car.price}</p>
                        <p class="car-card__year">Year: ${car.year}</p>
                        <p class="car-card__isNew">Is New: ${car.isNew ? 'Yes' : 'No'}</p>
                    </div>
                </a>
            `).join('');
            currentPageInput.value = parseInt(currentPageInput.value) + 1;
        } else {
            loader.style.display = 'none';
        }
        isLoading = false;
    };

    const handleScroll = () => {
        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 5) {
            console.log('load more');
            loadMoreCars();
        }
    };

    filtersForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        currentPageInput.value = 1;
        carsList.innerHTML = '';
        await loadMoreCars();
    });

    window.addEventListener('scroll', handleScroll);
});