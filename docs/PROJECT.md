# Travel Planner — Backend

Academic project (Software Development II). Backend only — the frontend is a separate repo (React + Vite + Tailwind + DaisyUI) that will consume this API.

## 1. Overview

Travel Planner helps users discover travel destinations in **Bangladesh only** and plan a trip end-to-end (transport + accommodation + rough budget) in about 1–2 minutes. Two core features:

1. **Destination Explorer** — browse categories (Hills, Sea, Forest, etc.) → places within a category (e.g. Sea → Cox's Bazar, Kuakata) → full details of a place (things to do, food, shopping).
2. **Trip Planner** — pick a starting location + a destination place → pick transport → pick accommodation → set days/people → get an estimated total cost.

This is a **solo academic project**. The priority is a clean, simple, working Laravel + MySQL API — not a feature-rich production system. Plain Laravel MVC (Route → Controller → Model), no repositories, no service layers, no queues, no microservices.

## 2. Tech Stack

- **Framework:** Laravel (latest LTS)
- **Database:** MySQL
- **API style:** REST, JSON responses
- **Auth:** None for MVP (see "Out of Scope"). No login needed to browse or plan a trip.
- **Testing:** Basic Laravel feature tests (optional, only if time allows)

## 3. Scope

**In scope**

- Destinations limited to Bangladesh.
- Pre-seeded categories, places, transports, and accommodations (seeded via Laravel seeders — no admin panel needed).
- Read APIs for destination browsing.
- A calculation API for trip cost estimation.

**Out of scope (do not build these — keep it simple)**

- User accounts / authentication / login
- Payments or real bookings
- Admin dashboard / CMS for managing content
- Reviews, ratings, comments
- Real-time transport/hotel availability
- Real distance API (Google Maps etc.) — distances are pre-seeded numbers

## 4. Core Features

### 4.1 Destination Explorer

1. `GET` list of categories (Hills, Sea, Forest, Historical, etc.)
2. User selects a category → `GET` list of places under it (e.g. Sea → Cox's Bazar, Kuakata, Saint Martin)
3. User selects a place → `GET` full place details:
    - Description
    - Things to visit (attractions)
    - Food to try
    - Shopping
    - Cover image

All three detail types (attraction / food / shopping) are stored in **one** table (`place_experiences`) differentiated by a `type` column — avoids creating three near-identical tables.

### 4.2 Trip Planner

Single flow, minimal steps:

1. Choose starting **location** (a city/district in Bangladesh)
2. Choose destination **place** (from Destination Explorer)
3. Choose **transport** (Bus / Train / Car / Plane)
4. Choose **accommodation** (No hotel / 2-Star / 3-Star / 4-Star / 5-Star)
5. Enter **number of people** and **number of days**
6. `POST` these to a calculate endpoint → get back an **estimated total cost** breakdown (transport, accommodation, total)

Optionally, the final plan can be saved (`trip_plans` table) so it has a shareable ID, but no login is required to do this.

## 5. Database Design

Simple, flat schema. No unnecessary normalization.

### `categories`

| Column     | Type                         |
| ---------- | ---------------------------- |
| id         | bigint, PK                   |
| name       | string (e.g. "Sea", "Hills") |
| slug       | string, unique               |
| image      | string (URL/path), nullable  |
| timestamps |                              |

### `places`

| Column      | Type                        |
| ----------- | --------------------------- |
| id          | bigint, PK                  |
| category_id | FK → categories             |
| name        | string (e.g. "Cox's Bazar") |
| slug        | string, unique              |
| description | text                        |
| cover_image | string, nullable            |
| timestamps  |                             |

### `place_experiences`

| Column      | Type                                   |
| ----------- | -------------------------------------- |
| id          | bigint, PK                             |
| place_id    | FK → places                            |
| type        | enum: `attraction`, `food`, `shopping` |
| title       | string                                 |
| description | text, nullable                         |
| image       | string, nullable                       |

### `locations`

(Starting points a user can travel from)
| Column | Type |
|---|---|
| id | bigint, PK |
| name | string (e.g. "Dhaka", "Khulna") |

### `transports`

| Column       | Type                            |
| ------------ | ------------------------------- |
| id           | bigint, PK                      |
| name         | string (Bus, Train, Car, Plane) |
| price_per_km | decimal                         |
| icon         | string, nullable                |

### `accommodations`

| Column          | Type                                             |
| --------------- | ------------------------------------------------ |
| id              | bigint, PK                                       |
| name            | string (e.g. "No Accommodation", "2 Star Hotel") |
| star_rating     | tinyint, nullable                                |
| price_per_night | decimal                                          |

### `routes`

(Pre-seeded distance between a location and a place — avoids needing a maps API)
| Column | Type |
|---|---|
| id | bigint, PK |
| location_id | FK → locations |
| place_id | FK → places |
| distance_km | decimal |

### `trip_plans`

(Optional saved result of a planner session)
| Column | Type |
|---|---|
| id | bigint, PK |
| location_id | FK → locations |
| place_id | FK → places |
| transport_id | FK → transports |
| accommodation_id | FK → accommodations |
| people | integer |
| days | integer |
| transport_cost | decimal |
| accommodation_cost | decimal |
| total_cost | decimal |
| timestamps | |

## 6. Cost Calculation Logic

Kept intentionally simple — good enough for an academic estimate, not a pricing engine.

```
transport_cost      = distance_km * price_per_km * people * 2   // round trip
accommodation_cost  = price_per_night * days * ceil(people / 2) // 2 people per room
total_cost          = transport_cost + accommodation_cost
```

If accommodation is "No Accommodation", `price_per_night` is `0`, so `accommodation_cost` is naturally `0`. This logic lives in a single method on the `TripPlanController` (e.g. `calculate()`) — no separate service class needed for a formula this small.

## 7. API Endpoints

| Method | Endpoint                        | Purpose                                                         |
| ------ | ------------------------------- | --------------------------------------------------------------- |
| GET    | `/api/categories`               | List all destination categories                                 |
| GET    | `/api/categories/{slug}/places` | List places under a category                                    |
| GET    | `/api/places/{slug}`            | Full place details incl. experiences (attraction/food/shopping) |
| GET    | `/api/locations`                | List starting locations                                         |
| GET    | `/api/transports`               | List transport options                                          |
| GET    | `/api/accommodations`           | List accommodation options                                      |
| POST   | `/api/trip-plan/calculate`      | Calculate estimated cost (does not save)                        |
| POST   | `/api/trip-plans`               | Save a trip plan, returns an ID                                 |
| GET    | `/api/trip-plans/{id}`          | Retrieve a saved trip plan                                      |

Example request for `POST /api/trip-plan/calculate`:

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

Example response:

```json
{
    "transport_cost": 1600,
    "accommodation_cost": 4500,
    "total_cost": 6100
}
```

## 8. Folder Structure

Standard Laravel structure — nothing custom added.

```
app/
  Http/
    Controllers/
      Api/
        CategoryController.php
        PlaceController.php
        LocationController.php
        TransportController.php
        AccommodationController.php
        TripPlanController.php
    Resources/            (optional API Resources for clean JSON)
  Models/
    Category.php
    Place.php
    PlaceExperience.php
    Location.php
    Transport.php
    Accommodation.php
    Route.php
    TripPlan.php
database/
  migrations/
  seeders/
    CategorySeeder.php
    PlaceSeeder.php
    PlaceExperienceSeeder.php
    LocationSeeder.php
    TransportSeeder.php
    AccommodationSeeder.php
    RouteSeeder.php
routes/
  api.php
```

## 9. Development Plan (suggested order)

1. Set up Laravel project + MySQL connection
2. Create migrations for all 8 tables
3. Create Eloquent models with relationships (`Category hasMany Place`, `Place hasMany PlaceExperience`, etc.)
4. Write seeders with real Bangladesh data (categories, places, a handful of locations, transports, accommodations, routes)
5. Build Destination Explorer endpoints (categories → places → place details)
6. Build Trip Planner reference endpoints (locations, transports, accommodations)
7. Build `trip-plan/calculate` endpoint with the cost formula
8. (Optional) Build `trip-plans` save/fetch endpoints
9. Test all endpoints with Postman/Insomnia
10. Connect with the React frontend

## 10. Guiding Principles

- One table per concept — no premature splitting.
- No auth, no admin panel, no payment — not needed for the assignment goals.
- No service/repository layers — Controllers talking to Models directly is fine at this scale.
- Seed data instead of building content-management features.
- Every endpoint should return plain, predictable JSON the frontend can consume directly.
