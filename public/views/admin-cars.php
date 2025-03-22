<?php
require_once __DIR__."/../utils/ComponentLoader.php";
ComponentLoader::load('header', ['title' => 'Admin - Cars']);
use repository\BrandRepository;
use repository\ModelRepository;

$brandsRepo = new BrandRepository();
$brands = $brandsRepo->getAll();
$modelsRepo = new ModelRepository();
$models = $modelsRepo->getAll();
?>

<main>
    <?php ComponentLoader::load("admin-nav"); ?>
    <h2>Welcome to the Admin Panel --- Cars Archive</h2>
    <form action="/admin/populateCars" method="post">
        <label>Number of cars to generate<input type="number" name="count"></label>
        <button id="auto-fill-cars">Auto Populate Cars</button>
    </form>
    <div>
        <?php
            if(isset($messages)){
                foreach($messages as $message){
                    echo "<p>$message</p>";
                }
            }
        ?>
    </div>

    <div class="dashboard">
        <div id="archive" class="dashboard-list">
        </div>
        <div class="scroll-container">
            <div id="car-modal" class="dashboard-modal">
                <div><h3>ADD NEW CAR</h3><button onClick="addNewCar()">Add New Car</button></div>
                <form action="/admin/addCar" method="post" class="dashboard-modal-form" enctype="multipart/form-data">
                <input type="hidden" name="car_id" id="car-id">

                <label for="car-title">Title:
                    <input type="text" name="title" id="car-title" maxlength="100" required>
                </label>

                <label for="car-brand">Brand:
                    <input type="text" name="brand" id="car-brand" list="brand-list" required>
                    <datalist id="brand-list">
                        <?php foreach($brands as $brand): ?>
                        <option value="<?= htmlspecialchars($brand['name']) ?>">
                            <?php endforeach; ?>
                    </datalist>
                </label>

                <label for="car-model">Model:
                    <input type="text" name="model" id="car-model" list="model-list" required>
                    <datalist id="model-list">
                        <?php foreach($models as $model): ?>
                        <option value="<?= htmlspecialchars($model['name']) ?>">
                            <?php endforeach; ?>
                    </datalist>
                </label>

                <label for="car-year">Year:
                    <input type="number" name="year" id="car-year" required min="1900" max="2025">
                </label>

                <label for="car-price">Price:
                    <input type="number" name="price" id="car-price" required min="0" step="0.01">
                </label>

                <label for="car-isNew">
                    Is New <input type="checkbox" name="isNew" id="car-isNew" value="1">
                </label>

                <label for="car-mileage">Mileage (km):
                    <input type="number" name="mileage" id="car-mileage" min="0" required>
                </label>

                <label for="car-fuel_type">Fuel Type:
                    <select name="fuel_type" id="car-fuel_type" required>
                        <option value="Gasoline">Gasoline</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Other">Other</option>
                    </select>
                </label>

                <label for="car-engine_size">Engine Size (L):
                    <input type="number" name="engine_size" id="car-engine_size" step="0.1" min="0" required>
                </label>

                <label for="car-horsepower">Horsepower (HP):
                    <input type="number" name="horsepower" id="car-horsepower" min="0" required>
                </label>

                <label for="car-transmission">Transmission:
                    <select name="transmission" id="car-transmission" required>
                        <option value="Manual">Manual</option>
                        <option value="Automatic">Automatic</option>
                        <option value="CVT">CVT</option>
                    </select>
                </label>

                <label for="car-color">Color:
                    <input type="text" name="color" id="car-color" required>
                </label>

                <label for="car-priority">Priority:
                    <input type="number" name="priority" id="car-priority" required min="0">
                </label>

                <label for="car-status">Status:
                    <select name="status" id="car-status" required>
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </label>

                <label for="car-isActive">
                    Active <input type="checkbox" name="isActive" id="car-isActive" value="1" checked>
                </label>

                <label for="car-description">Description:
                    <textarea name="description" id="car-description" required></textarea>
                </label>

                <label for="car-images">Images:
                    <input type="file" name="images[]" id="car-images" accept="image/jpeg, image/png, image/webp" multiple required>
                </label>

                <button type="submit">Save</button>
                <button onclick="" disabled hidden="hidden">DeleteCar (click twice)</button>
            </form>
            </div>
        </div>
    </div>
</main>
<script>
    // FETCH CARS FOR ADMIN PANEL
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/getAllCars_withModel', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(response => response.json())
            .then(data => {
                const carsList = document.getElementById('archive');
                data.forEach(car => {
                    const carItem = document.createElement('div');
                    carItem.className = 'car-item';
                    carItem.id = `car_${car.id}`;
                    carItem.innerHTML = `
                    <h3>${car.model_name}</h3>
                    <p>Year: ${car.year}</p>
                    <p>Price: $${car.price}</p>
                    <p>Status: ${car.status}</p>
                    <p>IsNew: ${car.is_new}</p>
                    <p>IsActive: ${car.is_active}</p>
                    <div class="car-item-buttons">
                        <button class="edit-car">Edit</button>
                        <button class="delete-car">Delete</button>
                        <button class="view-car"><a href="/car?id=${car.id}" target="_blank" rel="noreferrer">View</a></button>
                    </div>
                `;

                    carItem.querySelector('.edit-car').addEventListener('click', ()=>editCar(car.id));
                    carItem.querySelector('.delete-car').addEventListener('click', ()=>deleteCar(car.id));
                    carsList.appendChild(carItem);
                });
            })
            .catch(error => console.error('Error fetching cars:', error));
    });

    function editCar(carId) {
        fetch(`/api/getCarById_withDetails`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ car_id: carId })
        })
            .then(response => response.json())
            .then(car => {
                document.getElementById('car-id').value = car.id;
                document.getElementById('car-brand').value = car.brand_name;
                document.getElementById('car-model').value = car.model_name;
                document.getElementById('car-year').value = car.year;
                document.getElementById('car-price').value = car.price;
                document.getElementById('car-isNew').checked = car.is_new;
                document.getElementById('car-mileage').value = car.mileage;
                document.getElementById('car-fuel_type').value = car.fuel_type;
                document.getElementById('car-engine_size').value = car.engine_size;
                document.getElementById('car-horsepower').value = car.horsepower;
                document.getElementById('car-transmission').value = car.transmission;
                document.getElementById('car-color').value = car.color;
                document.getElementById('car-priority').value = car.priority;
                document.getElementById('car-status').value = car.status;
                document.getElementById('car-isActive').checked = car.is_active;
                document.getElementById('car-description').value = car.description;
                document.getElementById('car-title').value = car.title;

                document.getElementById('car-images').required = false;
                document.getElementById('car-images').hidden = true;
                document.getElementById('car-images').parentElement.style.display = "none";

                document.querySelector('.dashboard-modal-form').action = '/admin/updateCar';
                document.querySelector('.dashboard-modal h3').innerText = 'EDIT AN EXISTING CAR';
            })
            .catch(error => console.error('Error fetching car details:', error));
    }

    function deleteCar(carId) {
        fetch(`/api/deleteCar`, {
            method: "POST",
            headers: getSessionHeaders(),
            body: JSON.stringify({ car_id: carId })
        })
            .then(response => {
                if (response.ok) {
                    document.getElementById(`car_${carId}`).remove();
                } else {
                    window.alert('Error deleting car:', response.statusText);
                    console.error('Error deleting car:', response.statusText);
                }
            })
            .catch(error => {
                window.alert('Error deleting car:', error);
                console.error('Error deleting car:', error)
            });
    }

    function addNewCar(){
        document.getElementById('car-id').value = '';
        document.getElementById('car-brand').value = '';
        document.getElementById('car-model').value = '';
        document.getElementById('car-year').value = '';
        document.getElementById('car-price').value = '';
        document.getElementById('car-isNew').checked = false;
        document.getElementById('car-mileage').value = '';
        document.getElementById('car-fuel_type').value = 'Gasoline';
        document.getElementById('car-engine_size').value = '';
        document.getElementById('car-horsepower').value = '';
        document.getElementById('car-transmission').value = 'Manual';
        document.getElementById('car-color').value = '';
        document.getElementById('car-priority').value = '';
        document.getElementById('car-status').value = 'available';
        document.getElementById('car-isActive').checked = true;
        document.getElementById('car-description').value = '';
        document.getElementById('car-title').value = '';

        document.getElementById('car-images').required = true;
        document.getElementById('car-images').hidden = false;
        document.getElementById('car-images').parentElement.style.display = "grid";

        document.querySelector('.car-modal-form').action = '/admin/addCar';
        document.querySelector('#car-modal h3').innerText = 'ADD NEW CAR';
    }
</script>

<?php
ComponentLoader::load('footer');
?>
