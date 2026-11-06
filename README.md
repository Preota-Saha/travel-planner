# Travel Planner — Backend

This is the backend API for the Travel Planner project.
It is built with Laravel and uses MySQL (via XAMPP) for the database.
The frontend (React + Vite + Tailwind + DaisyUI) lives in a separate repo and talks to this API.

All destinations in this project are inside Bangladesh.

## Requirements

- PHP 8.3 or higher
- Composer
- XAMPP (only needed for the MySQL service)
- Git

## How to run the project locally

1. **Start MySQL from XAMPP**
   - Open the XAMPP Control Panel.
   - Click **Start** next to **MySQL** until the label shows green / "Running".
   - Apache is not required for this project. Laravel is run using the built-in PHP server.

2. **Install PHP dependencies**
   - In the project folder, run:
     ```
     composer install
     ```

3. **Set up the environment file (only needed once)**
   - If `.env` is missing, copy it from the example file:
     ```
     copy .env.example .env
     ```
   - Generate the app key:
     ```
     php artisan key:generate
     ```
   - The `.env` file is already configured for XAMPP defaults:
     - DB_HOST = 127.0.0.1
     - DB_PORT = 3306
     - DB_DATABASE = travel_planner
     - DB_USERNAME = root
     - DB_PASSWORD = (empty)

4. **Make sure the database exists (only needed once)**
   - Open `http://127.0.0.1/phpmyadmin` in your browser.
   - Click **New** on the left, type `travel_planner` as the database name, and click **Create**.
   - Or from a terminal:
     ```
     "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS travel_planner;"
     ```

5. **Build the tables and load the demo data**
   ```
   php artisan migrate:fresh --seed
   ```
   This drops all tables, recreates them, and loads all demo data (categories, places, experiences, locations, transports, accommodations, and routes).

6. **Start the Laravel server**
   ```
   php artisan serve
   ```
   Then visit **http://127.0.0.1:8000** in your browser.
   The API lives under **http://127.0.0.1:8000/api**.

## API endpoints

All endpoints are under `http://127.0.0.1:8000/api`.

### Destination Explorer (read-only)

| Method | Endpoint                        | Purpose                                              |
| ------ | ------------------------------- | ---------------------------------------------------- |
| GET    | `/categories`                   | List all destination categories                      |
| GET    | `/categories/{slug}/places`     | List places under one category                       |
| GET    | `/places/{slug}`                | Full place details with grouped experiences          |

### Trip Planner dropdowns (read-only)

| Method | Endpoint          | Purpose                                  |
| ------ | ----------------- | ---------------------------------------- |
| GET    | `/locations`      | List starting locations                  |
| GET    | `/transports`     | List transport options                   |
| GET    | `/accommodations` | List accommodation options               |

### Trip Planner (cost calculation + saving)

| Method | Endpoint                  | Purpose                                                         |
| ------ | ------------------------- | --------------------------------------------------------------- |
| POST   | `/trip-plan/calculate`    | Calculate estimated cost (does NOT save anything)               |
| POST   | `/trip-plans`             | Calculate cost and SAVE the plan, returns it with a new id      |
| GET    | `/trip-plans/{id}`        | Fetch one saved plan with its related location/place/transport/accommodation names |

### Cost formula

```
transport_cost     = distance_km * price_per_km * people * 2       // round trip
accommodation_cost = price_per_night * days * ceil(people / 2)    // 2 people per room
total_cost         = transport_cost + accommodation_cost
```

### Example POST body (calculate or save)

```json
{
    "location_id": 1,
    "place_id": 4,
    "transport_id": 2,
    "accommodation_id": 3,
    "people": 2,
    "days": 3
}
```

## Testing with Postman

A ready-made Postman collection is included in the project root:

```
travel-planner.postman_collection.json
```

To use it:
1. Open Postman.
2. Click **Import** and select that file.
3. The collection has a variable called `base_url` already set to `http://127.0.0.1:8000/api`, so all 9 requests work out of the box once the Laravel server is running.
4. Click **Send** on any request to test it.

## Project layout

- `app/Models/` — Eloquent models: Category, Place, PlaceExperience, Location, Transport, Accommodation, Route, TripPlan
- `app/Http/Controllers/Api/` — request handlers (one controller per resource)
- `routes/api.php` — REST API routes
- `routes/web.php` — basic web routes
- `database/migrations/` — database table definitions
- `database/seeders/` — demo data loaders (read from JSON files)
- `database/data/` — JSON files with demo data
- `config/` — Laravel config files (including `config/cors.php` for cross-origin access)

## Notes

- CORS is enabled for all origins (see `config/cors.php`) so the React frontend on `http://localhost:5173` can call this API.
- No login or authentication is required for any endpoint.
- No automated tests are included — this is an academic project, not a production app.