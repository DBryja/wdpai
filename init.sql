CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role VARCHAR(50) CHECK (role IN ('admin', 'user', 'guest')) NOT NULL,
    session_token VARCHAR(255)
);

CREATE TABLE brands (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE models (
    id SERIAL PRIMARY KEY,
    brand_id INT REFERENCES brands(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE (brand_id, name)
);

CREATE TABLE cars (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    model_id INT REFERENCES models(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    year INT CHECK (year > 1900 AND year <= EXTRACT(YEAR FROM CURRENT_DATE)) NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    priority INT DEFAULT 0,
    status VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    is_new BOOLEAN DEFAULT FALSE,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE car_details (
    id SERIAL PRIMARY KEY,
    car_id UUID REFERENCES cars(id) ON DELETE CASCADE NOT NULL,
    mileage INT CHECK (mileage >= 0) NOT NULL,
    fuel_type VARCHAR(50) NOT NULL,
    engine_size NUMERIC(4,1) CHECK (engine_size > 0) NOT NULL,
    horsepower INT CHECK (horsepower > 0),
    transmission VARCHAR(50),
    color VARCHAR(50),
    description TEXT
);
ALTER TABLE car_details ADD CONSTRAINT unique_car_id UNIQUE (car_id);

CREATE TABLE images (
    id SERIAL PRIMARY KEY,
    car_id UUID REFERENCES cars(id) ON DELETE CASCADE NOT NULL,
    image_url TEXT NOT NULL,
    alt_text VARCHAR(255)
);

CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE car_tags (
    car_id UUID REFERENCES cars(id) ON DELETE CASCADE,
    tag_id INT REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (car_id, tag_id)
);

CREATE OR REPLACE VIEW cars_with_model AS
SELECT
    cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    models.name as model_name
FROM cars
JOIN models ON cars.model_id = models.id;

CREATE OR REPLACE VIEW cars_full_details AS
SELECT 
    cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    car_details.mileage,
    car_details.fuel_type,
    car_details.engine_size,
    car_details.horsepower,
    car_details.transmission,
    car_details.color,
    car_details.description,
    brands.name as brand_name,
    models.name as model_name
FROM cars
JOIN car_details ON cars.id = car_details.car_id
JOIN models ON cars.model_id = models.id
JOIN brands ON models.brand_id = brands.id;

CREATE OR REPLACE VIEW cars_search_view AS
SELECT 
    cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    brands.name as brand_name,
    models.name as model_name,
    car_details.mileage,
    car_details.horsepower as hp
FROM cars
JOIN models ON cars.model_id = models.id
JOIN brands ON models.brand_id = brands.id
JOIN car_details ON cars.id = car_details.car_id;

CREATE OR REPLACE VIEW car_detail_view AS
SELECT
    cars.id,
    cars.model_id,
    cars.user_id,
    cars.title,
    cars.year,
    cars.price,
    cars.priority,
    cars.status,
    cars.is_active,
    cars.is_new,
    cars.added_at,
    car_details.color,
    car_details.description,
    car_details.engine_size,
    car_details.fuel_type,
    car_details.horsepower,
    car_details.mileage,
    car_details.transmission,
    brands.name as brand_name,
    models.name as model_name
FROM cars
JOIN car_details ON cars.id = car_details.car_id
JOIN models ON cars.model_id = models.id
JOIN brands ON models.brand_id = brands.id;

CREATE OR REPLACE VIEW cars_with_tags AS
SELECT 
    c.id,
    c.title,
    c.year,
    c.price,
    c.priority,
    c.status,
    c.is_active,
    c.is_new,
    b.name as brand_name,
    m.name as model_name,
    cd.horsepower,
    cd.mileage,
    STRING_AGG(t.name, ', ' ORDER BY t.name) as tags
FROM cars c
JOIN models m ON c.model_id = m.id
JOIN brands b ON m.brand_id = b.id
LEFT JOIN car_details cd ON c.id = cd.car_id
LEFT JOIN car_tags ct ON c.id = ct.car_id
LEFT JOIN tags t ON ct.tag_id = t.id
GROUP BY c.id, c.title, c.year, c.price, c.priority, c.status, c.is_active, c.is_new, 
         b.name, m.name, cd.horsepower, cd.mileage
ORDER BY c.priority DESC, c.added_at DESC;

INSERT INTO tags (name, description) VALUES 
('sport', 'High performance sports cars'),
('luxury', 'Luxury vehicles with premium features'),
('electric', 'Electric vehicles'),
('hybrid', 'Hybrid vehicles'),
('suv', 'Sport Utility Vehicles'),
('sedan', 'Sedan body type'),
('compact', 'Compact cars'),
('economy', 'Economical fuel-efficient cars')
ON CONFLICT (name) DO NOTHING;

-- Funkcja do automatycznego ustawiania priorytetu na podstawie statusu i parametrów
CREATE OR REPLACE FUNCTION auto_set_priority()
RETURNS TRIGGER AS $$
DECLARE
    calculated_priority INT := 0;
    car_details_rec RECORD;
    brand_name VARCHAR;
BEGIN
    SELECT * INTO car_details_rec 
    FROM car_details 
    WHERE car_id = NEW.id;
    
    SELECT b.name INTO brand_name
    FROM brands b
    JOIN models m ON b.id = m.brand_id
    WHERE m.id = NEW.model_id;
    
    CASE NEW.status
        WHEN 'available' THEN calculated_priority := 3;
        WHEN 'reserved' THEN calculated_priority := 2;
        WHEN 'sold' THEN calculated_priority := 1;
        ELSE calculated_priority := 1;
    END CASE;
    
    IF NEW.is_new = TRUE THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    IF brand_name IN ('BMW', 'Mercedes-Benz', 'Audi', 'Porsche', 'Ferrari', 'Lamborghini') THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    IF car_details_rec.horsepower > 300 THEN
        calculated_priority := calculated_priority + 1;
    END IF;
    
    calculated_priority := GREATEST(1, LEAST(5, calculated_priority));
    
    NEW.priority := calculated_priority;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Wyzwalacz na automatyczne ustawianie priorytetu przy wstawianiu/aktualizacji samochodu
CREATE OR REPLACE TRIGGER trigger_auto_priority
    BEFORE INSERT OR UPDATE ON cars
    FOR EACH ROW
    EXECUTE FUNCTION auto_set_priority();
