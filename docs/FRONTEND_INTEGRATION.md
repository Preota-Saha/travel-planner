# Travel Planner — Frontend Integration Guide

Everything you need to call this API from your React + Vite + Tailwind + DaisyUI frontend.
This document is self-contained — you do not need to read any other file.

---

## 1. Getting Started

### Starting the backend

1. Open **XAMPP Control Panel** and click **Start** next to **MySQL**. Apache is not needed.
2. Open a terminal in this project's root folder.
3. If this is your first time running the project:
   ```
   composer install
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
4. Start the Laravel dev server:
   ```
   php artisan serve
   ```
5. The backend is now running at `http://127.0.0.1:8000`.

### Base URL

All API calls go to:

```
http://127.0.0.1:8000/api
```

### Required headers for every request

```
Content-Type: application/json
Accept:        application/json
```

`fetch()` in browsers sets `Accept` automatically when you pass JSON, but it's safe to send both explicitly.

### Authentication

**There is none.** Every endpoint is public. No API keys, no tokens, no login.

### CORS

CORS is already enabled for all origins (`Access-Control-Allow-Origin: *`).
You can call this API from `http://localhost:5173` (Vite default), `http://localhost:3000`, or anywhere else — no proxy needed.

---

## 2. Quick Reference

| Method | Path                              | What it does                                                                  |
| ------ | --------------------------------- | ----------------------------------------------------------------------------- |
| GET    | `/categories`                     | List all destination categories (Sea, Hills, Forest, ...)                     |
| GET    | `/categories/{slug}/places`       | List places under one category (e.g. `/categories/sea/places`)                |
| GET    | `/places/{slug}`                  | Full place details with attractions, food, and shopping                       |
| GET    | `/locations`                      | List all starting locations (Dhaka, Khulna, ...)                              |
| GET    | `/transports`                     | List all transport options (Bus AC, Train, Car, ...)                          |
| GET    | `/accommodations`                 | List all accommodation options (No Accommodation, 2-Star, ...)               |
| POST   | `/trip-plan/calculate`            | Calculate estimated trip cost (does **not** save)                             |
| POST   | `/trip-plans`                     | Calculate + **save** the trip plan, returns it with a new `id`                |
| GET    | `/trip-plans/{id}`                | Fetch one saved trip plan by its id                                           |

All paths are relative to `http://127.0.0.1:8000/api`.

---

## 3. Endpoint Details

### GET `/categories`

Returns all 5 destination categories. Call this on app load to build the home screen.

**Path parameters:** none

**Query parameters:** none

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/categories
```

**Success response (200 OK):**

```json
[
    { "id": 1, "name": "Sea",       "slug": "sea",       "image": "https://placehold.co/600x400/0077b6/ffffff?text=Sea" },
    { "id": 2, "name": "Hills",     "slug": "hills",     "image": "https://placehold.co/600x400/2a9d8f/ffffff?text=Hills" },
    { "id": 3, "name": "Forest",    "slug": "forest",    "image": "https://placehold.co/600x400/1b4332/ffffff?text=Forest" },
    { "id": 4, "name": "Historical","slug": "historical","image": "https://placehold.co/600x400/8d6e63/ffffff?text=Historical" },
    { "id": 5, "name": "Village",   "slug": "village",   "image": "https://placehold.co/600x400/6a994e/ffffff?text=Village" }
]
```

Each object has: `id` (number), `name` (string), `slug` (string, URL-safe), `image` (string URL or null).

---

### GET `/categories/{slug}/places`

Returns all places that belong to one category. Call this when the user clicks a category card.

**Path parameters:**

| Name   | Type   | Required | Example       |
| ------ | ------ | -------- | ------------- |
| `slug` | string | yes      | `sea`, `hills`, `forest`, `historical`, `village` |

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/categories/hills/places
```

**Success response (200 OK):**

```json
[
    {
        "id": 4,
        "name": "Bandarban",
        "slug": "bandarban",
        "description": "A scenic hill district with green valleys, rivers and the highest peaks in Bangladesh. It is a popular spot for trekking and nature lovers.",
        "cover_image": "https://placehold.co/600x400/2a9d8f/ffffff?text=Bandarban"
    },
    {
        "id": 5,
        "name": "Sajek Valley",
        "slug": "sajek-valley",
        "description": "Often called the roof of Rangamati, Sajek Valley is famous for its clouds floating below the hills. It is a calm and beautiful hill retreat near the Mizoram border.",
        "cover_image": "https://placehold.co/600x400/2a9d8f/ffffff?text=Sajek+Valley"
    },
    {
        "id": 6,
        "name": "Khagrachari",
        "slug": "khagrachari",
        "description": "A peaceful hill district known for its rolling green hills, indigenous culture and waterfalls. Alutila Cave is one of its most popular attractions.",
        "cover_image": "https://placehold.co/600x400/2a9d8f/ffffff?text=Khagrachari"
    }
]
```

Each object has: `id`, `name`, `slug`, `description`, `cover_image` (URL or null).

**Error response (404 Not Found):**

```json
{ "message": "Category not found" }
```

---

### GET `/places/{slug}`

Returns one place plus its experiences grouped by type. Call this when the user opens a place's detail page.

**Path parameters:**

| Name   | Type   | Required | Example         |
| ------ | ------ | -------- | --------------- |
| `slug` | string | yes      | `coxs-bazar`, `sreemangal`, `sundarbans` |

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/places/sreemangal
```

**Success response (200 OK):**

```json
{
    "id": 12,
    "category_id": 5,
    "name": "Sreemangal",
    "slug": "sreemangal",
    "description": "A small tea-growing town surrounded by lush green tea gardens. It is the tea capital of Bangladesh and known for its calm, slow village life.",
    "cover_image": "https://placehold.co/600x400/6a994e/ffffff?text=Sreemangal",
    "experiences": {
        "attractions": [
            { "id": 58, "title": "Tea Garden Walking",   "description": "Long walks through endless rows of bright green tea bushes.", "image": null },
            { "id": 59, "title": "Madhabpur Lake",      "description": "A small lake inside the forest with lily pads and turtles.", "image": null },
            { "id": 60, "title": "Lawachara Border Trek","description": "Short forest trek at the edge of Lawachara National Park.", "image": null }
        ],
        "foods": [
            { "id": 61, "title": "Seven Layer Tea", "description": "Famous seven colour tea made by local tea stalls.", "image": null },
            { "id": 62, "title": "Akhoni Pitha",    "description": "Traditional tea-time rice cake eaten with sugar.",    "image": null }
        ],
        "shopping": [
            { "id": 63, "title": "Local Tea Packets", "description": "Fresh loose leaf tea bought straight from the factory outlet.", "image": null }
        ]
    }
}
```

**Field guide for the frontend:**

| Field                     | Type                | Notes                                                            |
| ------------------------- | ------------------- | ---------------------------------------------------------------- |
| `id`                      | number              | Place's internal id — use this for `place_id` in trip-plan calls |
| `category_id`             | number              | Internal category id                                             |
| `name` / `slug`           | string              | Display name and URL-safe identifier                             |
| `description`             | string              | Short paragraph about the place                                  |
| `cover_image`             | string or null      | Image URL — handle `null` with a fallback                        |
| `experiences.attractions` | array of objects     | Things to visit / do at the place                                |
| `experiences.foods`       | array of objects     | Local food recommendations                                       |
| `experiences.shopping`    | array of objects     | Things to buy                                                    |
| each experience: `id`     | number              | Internal id                                                      |
| each experience: `title`  | string              | Short heading                                                    |
| each experience: `description` | string         | One short sentence                                               |
| each experience: `image`  | string or null      | Image URL — handle `null`                                        |

Each experience item always has the same shape regardless of section — only the array it lives in (`attractions`, `foods`, `shopping`) tells the frontend which kind it is.

**Error response (404 Not Found):**

```json
{ "message": "Place not found" }
```

---

### GET `/locations`

Returns all starting locations for the trip planner dropdown. Call this when the trip planner page loads.

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/locations
```

**Success response (200 OK):**

```json
[
    { "id": 1, "name": "Dhaka" },
    { "id": 2, "name": "Khulna" },
    { "id": 3, "name": "Chittagong" },
    { "id": 4, "name": "Rajshahi" },
    { "id": 5, "name": "Sylhet" },
    { "id": 6, "name": "Barisal" },
    { "id": 7, "name": "Rangpur" },
    { "id": 8, "name": "Mymensingh" },
    { "id": 9, "name": "Comilla" },
    { "id": 10, "name": "Jessore" }
]
```

Each object has: `id` (number to send as `location_id`) and `name` (city name to show in the dropdown).

---

### GET `/transports`

Returns all transport options for the trip planner dropdown.

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/transports
```

**Success response (200 OK):**

```json
[
    { "id": 1, "name": "Bus (Non-AC)",   "price_per_km": "2.50", "icon": "bus"   },
    { "id": 2, "name": "Bus (AC)",       "price_per_km": "4.00", "icon": "bus"   },
    { "id": 3, "name": "Train (Shovon)", "price_per_km": "1.50", "icon": "train" },
    { "id": 4, "name": "Train (AC)",     "price_per_km": "3.50", "icon": "train" },
    { "id": 5, "name": "Microbus",       "price_per_km": "8.00", "icon": "van"   },
    { "id": 6, "name": "Private Car",    "price_per_km": "12.00","icon": "car"   }
]
```

| Field          | Type   | Notes                                                       |
| -------------- | ------ | ----------------------------------------------------------- |
| `id`           | number | Send as `transport_id` when planning a trip                 |
| `name`         | string | Display name                                                |
| `price_per_km` | string | **Sent as a string, not a number** (e.g. `"2.50"`). Parse to number if you need to do client-side math |
| `icon`         | string | A short hint like `"bus"`, `"train"`, `"van"`, `"car"` — useful for picking an icon component |

---

### GET `/accommodations`

Returns all accommodation options for the trip planner dropdown.

**Request body:** none

**Example request:**

```
GET http://127.0.0.1:8000/api/accommodations
```

**Success response (200 OK):**

```json
[
    { "id": 1, "name": "No Accommodation", "star_rating": null, "price_per_night": "0.00"     },
    { "id": 2, "name": "2 Star Hotel",     "star_rating": 2,    "price_per_night": "1500.00"  },
    { "id": 3, "name": "3 Star Hotel",     "star_rating": 3,    "price_per_night": "3000.00"  },
    { "id": 4, "name": "4 Star Hotel",     "star_rating": 4,    "price_per_night": "6000.00"  },
    { "id": 5, "name": "5 Star Resort",    "star_rating": 5,    "price_per_night": "12000.00" }
]
```

| Field             | Type           | Notes                                                                                  |
| ----------------- | -------------- | -------------------------------------------------------------------------------------- |
| `id`              | number         | Send as `accommodation_id` when planning a trip                                        |
| `name`            | string         | Display name                                                                           |
| `star_rating`     | number or null | `null` means "No Accommodation" (id `1`). Otherwise `2`, `3`, `4`, or `5`             |
| `price_per_night` | string         | **Sent as a string** (e.g. `"1500.00"`). Parse to number if needed                     |

---

### POST `/trip-plan/calculate`

Calculates the estimated trip cost without saving anything. Use this for a live "estimate" view on the trip planner page (e.g. when the user changes any input, just call this and show the breakdown).

**Request body (JSON):**

| Field              | Type   | Required | Notes                                                  |
| ------------------ | ------ | -------- | ------------------------------------------------------ |
| `location_id`      | number | yes      | From `/locations`                                       |
| `place_id`         | number | yes      | From `/categories/{slug}/places` — use `id`, not `slug`|
| `transport_id`     | number | yes      | From `/transports`                                      |
| `accommodation_id` | number | yes      | From `/accommodations`                                  |
| `people`           | number | yes      | Must be at least `1`                                    |
| `days`             | number | yes      | Must be at least `1`                                    |

**Example request:**

```
POST http://127.0.0.1:8000/api/trip-plan/calculate
Content-Type: application/json
```

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

This means: **Dhaka → Bandarban**, by **Bus (AC)**, stay in a **3 Star Hotel**, **2 people, 3 days**.

**Success response (200 OK):**

```json
{
    "location_id": 1,
    "place_id": 4,
    "transport_id": 2,
    "accommodation_id": 3,
    "people": 2,
    "days": 3,
    "distance_km": 350,
    "transport_cost": 5600,
    "accommodation_cost": 9000,
    "total_cost": 14600
}
```

| Field                | Type   | Notes                                                                  |
| -------------------- | ------ | ---------------------------------------------------------------------- |
| `distance_km`        | number | One-way distance from the chosen location to the chosen place          |
| `transport_cost`     | number | Cost for **all** people, **round trip**                                |
| `accommodation_cost` | number | Cost for **all** rooms, **all nights**                                 |
| `total_cost`         | number | `transport_cost + accommodation_cost`                                  |

See section **4. Trip Cost Calculation** for the formula details so you can show a breakdown to the user.

**Error response — no route (404 Not Found):**

This happens when the chosen `location_id` + `place_id` combination doesn't have a distance row in the database (every seeded location has a route to every place, so this only fires if the frontend sends bad ids).

```json
{ "message": "No route found for this location and place" }
```

**Error response — validation (422 Unprocessable Entity):**

This fires if any required field is missing, is not a number, or if `people`/`days` is less than 1.

```json
{
    "message": "The transport id field is required. (and 3 more errors)",
    "errors": {
        "transport_id":     ["The transport id field is required."],
        "accommodation_id": ["The accommodation id field is required."],
        "people":           ["The people field is required."],
        "days":             ["The days field is required."]
    }
}
```

The `errors` object has one key per bad field, and the value is an array of human-readable messages.

---

### POST `/trip-plans`

Same calculation as `/trip-plan/calculate`, but it also **saves** the result and returns the new plan (including its generated `id`). Use this when the user clicks a "Save this trip" button on the planner page.

**Request body:** exactly the same shape as `/trip-plan/calculate` (see above).

**Example request:**

```
POST http://127.0.0.1:8000/api/trip-plans
Content-Type: application/json
```

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

**Success response (201 Created):**

```json
{
    "id": 1,
    "location_id": 1,
    "place_id": 4,
    "transport_id": 2,
    "accommodation_id": 3,
    "people": 2,
    "days": 3,
    "transport_cost": 5600,
    "accommodation_cost": 9000,
    "total_cost": 14600,
    "created_at": "2026-08-26T16:59:40.000000Z",
    "updated_at": "2026-08-26T16:59:40.000000Z",
    "location":      { "id": 1, "name": "Dhaka" },
    "place":         { "id": 4, "name": "Bandarban", "slug": "bandarban" },
    "transport":     { "id": 2, "name": "Bus (AC)" },
    "accommodation": { "id": 3, "name": "3 Star Hotel" }
}
```

The 4 nested objects (`location`, `place`, `transport`, `accommodation`) are the resolved names so the frontend doesn't have to look them up separately. `place` also includes its `slug` for navigation.

**Error responses:** same as `/trip-plan/calculate` (404 for missing route, 422 for validation).

---

### GET `/trip-plans/{id}`

Fetches one previously-saved trip plan by its id. Use this if you want to show a "shared view" of a trip (e.g. the user gets a link like `/trips/1` and you load it from this endpoint).

**Path parameters:**

| Name | Type   | Required | Example |
| ---- | ------ | -------- | ------- |
| `id` | number | yes      | `1`     |

`id` must be a positive integer. Non-numeric ids are rejected by the router with a 404.

**Example request:**

```
GET http://127.0.0.1:8000/api/trip-plans/1
```

**Success response (200 OK):**

Same shape as the success response from `POST /trip-plans` — see the JSON block above.

**Error response (404 Not Found):**

```json
{ "message": "Trip plan not found" }
```

---

## 4. Trip Cost Calculation

The backend computes three numbers using simple, transparent rules. The frontend can show these formulas to the user for clarity.

```
transport_cost     = distance_km × price_per_km × people × 2
accommodation_cost = price_per_night × days × ceil(people / 2)
total_cost         = transport_cost + accommodation_cost
```

Plain English:

- **distance_km** — the one-way distance from the chosen starting location to the chosen place. The same number is used twice (going and coming back), which is the `× 2` in the transport formula.
- **transport_cost** — every person pays for a round-trip ticket. So if 2 people travel 350 km by Bus (AC) at ৳4/km, the cost is `350 × 4 × 2 × 2 = 5600`.
- **accommodation_cost** — rooms hold 2 people each. `ceil(people / 2)` rounds up: 1 person = 1 room, 2 people = 1 room, 3 people = 2 rooms, 5 people = 3 rooms. For 2 people × 3 nights in a 3-Star Hotel at ৳3000/night: `3000 × 3 × 1 = 9000`.
- **total_cost** — the simple sum of the two.

The **"No Accommodation"** option has `price_per_night = 0`, so `accommodation_cost` naturally becomes `0` — you don't need any special-case logic.

All three money fields (`transport_cost`, `accommodation_cost`, `total_cost`) come back as **plain numbers** (already rounded to 2 decimal places by the backend).

### Suggested frontend breakdown UI

```
Trip cost breakdown
─────────────────────────────
Starting from:  Dhaka
Going to:       Bandarban
People:         2
Days:           3
Transport:      Bus (AC)
Hotel:          3 Star Hotel

One-way distance: 350 km

Transport cost:      ৳ 5,600
  (350 km × ৳4.00/km × 2 people × 2 [round trip])

Accommodation cost:  ৳ 9,000
  (৳3,000/night × 3 nights × 1 room)

Total estimated cost: ৳ 14,600
```

---

## 5. Reference Data Snapshot

This is exactly what is currently in the seeded database. Use these ids and slugs directly in your frontend examples and tests — they will work without any extra setup.

### Categories

| id | slug         | name        |
| -- | ------------ | ----------- |
| 1  | `sea`        | Sea         |
| 2  | `hills`      | Hills       |
| 3  | `forest`     | Forest      |
| 4  | `historical` | Historical  |
| 5  | `village`    | Village     |

### Places grouped by category

**Sea (`/categories/sea/places`)**

| id | slug           | name         |
| -- | -------------- | ------------ |
| 1  | `coxs-bazar`   | Cox's Bazar  |
| 2  | `kuakata`      | Kuakata      |
| 3  | `saint-martin` | Saint Martin |

**Hills (`/categories/hills/places`)**

| id | slug           | name         |
| -- | -------------- | ------------ |
| 4  | `bandarban`    | Bandarban    |
| 5  | `sajek-valley` | Sajek Valley |
| 6  | `khagrachari`  | Khagrachari  |

**Forest (`/categories/forest/places`)**

| id | slug        | name                     |
| -- | ----------- | ------------------------ |
| 7  | `sundarbans` | Sundarbans              |
| 8  | `lawachara`  | Lawachara National Park |

**Historical (`/categories/historical/places`)**

| id | slug             | name                          |
| -- | ---------------- | ----------------------------- |
| 9  | `sonargaon`      | Sonargaon                     |
| 10 | `mahasthangarh`  | Mahasthangarh                 |
| 11 | `paharpur`       | Paharpur (Somapura Mahavihara)|

**Village (`/categories/village/places`)**

| id | slug           | name                   |
| -- | -------------- | ---------------------- |
| 12 | `sreemangal`   | Sreemangal             |
| 13 | `ratargul`     | Ratargul Swamp Forest  |
| 14 | `jaflong`      | Jaflong                |

### Locations

| id | name        |
| -- | ----------- |
| 1  | Dhaka       |
| 2  | Khulna      |
| 3  | Chittagong  |
| 4  | Rajshahi    |
| 5  | Sylhet      |
| 6  | Barisal     |
| 7  | Rangpur     |
| 8  | Mymensingh  |
| 9  | Comilla     |
| 10 | Jessore     |

### Transports

| id | name              | price_per_km (৳) | icon    |
| -- | ----------------- | ---------------- | ------- |
| 1  | Bus (Non-AC)      | 2.50             | `bus`   |
| 2  | Bus (AC)          | 4.00             | `bus`   |
| 3  | Train (Shovon)    | 1.50             | `train` |
| 4  | Train (AC)        | 3.50             | `train` |
| 5  | Microbus          | 8.00             | `van`   |
| 6  | Private Car       | 12.00            | `car`   |

### Accommodations

| id | name               | star_rating | price_per_night (৳) |
| -- | ------------------ | ----------- | ------------------- |
| 1  | No Accommodation   | null        | 0                   |
| 2  | 2 Star Hotel       | 2           | 1,500               |
| 3  | 3 Star Hotel       | 3           | 3,000               |
| 4  | 4 Star Hotel       | 4           | 6,000               |
| 5  | 5 Star Resort      | 5           | 12,000              |

---

## 6. Common Errors

The API uses two error shapes. Handle them once in your fetch helper and you're covered for every endpoint.

### 404 — Not Found

Used by: `GET /categories/{slug}/places`, `GET /places/{slug}`, `GET /trip-plans/{id}`, and the two POST endpoints when the chosen location+place pair has no distance row.

```json
{ "message": "Category not found" }
```

```json
{ "message": "Place not found" }
```

```json
{ "message": "Trip plan not found" }
```

```json
{ "message": "No route found for this location and place" }
```

The `message` field is a short, human-readable string — show it directly to the user, or map it to your own copy.

### 422 — Validation Failed

Used by the two POST endpoints when a required field is missing or invalid. This is Laravel's default validation shape.

```json
{
    "message": "The transport id field is required. (and 3 more errors)",
    "errors": {
        "transport_id":     ["The transport id field is required."],
        "accommodation_id": ["The accommodation id field is required."],
        "people":           ["The people field is required."],
        "days":             ["The days field is required."]
    }
}
```

For trip-plan POSTs, validation rules are:

| Field              | Rule                              |
| ------------------ | --------------------------------- |
| `location_id`      | required, must be an integer      |
| `place_id`         | required, must be an integer      |
| `transport_id`     | required, must be an integer      |
| `accommodation_id` | required, must be an integer      |
| `people`           | required, must be an integer, ≥ 1 |
| `days`             | required, must be an integer, ≥ 1 |

The `errors` object has one key per invalid field. The value is **always an array** of message strings (even if there's just one message). Use the first message, or join them, or display them inline next to each input.

### Suggested error handling pattern (any framework)

```js
if (!response.ok) {
    const data = await response.json().catch(() => ({}));
    if (response.status === 422 && data.errors) {
        // show field-level errors: data.errors[fieldName] is an array
    } else {
        // show data.message, or a generic fallback
    }
}
```

---

## 7. Example Frontend Setup (React + Vite)

Two files you can drop straight into a fresh Vite React project.

### `.env.local` (project root)

Create this file in your frontend project root (where `package.json` lives). Vite reads any env variable prefixed with `VITE_` and exposes it via `import.meta.env`.

```
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

Restart `npm run dev` after creating or changing this file.

### `src/api.js` (small reusable fetch helper)

```js
// src/api.js
const BASE_URL = import.meta.env.VITE_API_BASE_URL;

async function request(path, { method = 'GET', body } = {}) {
    const res = await fetch(`${BASE_URL}${path}`, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        body: body ? JSON.stringify(body) : undefined,
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
        // attach the parsed body so callers can read .message or .errors
        const error = new Error(data.message || `HTTP ${res.status}`);
        error.status = res.status;
        error.body = data;
        throw error;
    }

    return data;
}

// Destination Explorer
export const getCategories   = ()                          => request('/categories');
export const getPlacesByCategory = (slug)                  => request(`/categories/${slug}/places`);
export const getPlace        = (slug)                      => request(`/places/${slug}`);

// Trip Planner dropdowns
export const getLocations    = ()                          => request('/locations');
export const getTransports   = ()                          => request('/transports');
export const getAccommodations = ()                       => request('/accommodations');

// Trip cost
export const calculateTrip   = (payload)                   => request('/trip-plan/calculate', { method: 'POST', body: payload });
export const saveTripPlan    = (payload)                   => request('/trip-plans',         { method: 'POST', body: payload });
export const getTripPlan     = (id)                        => request(`/trip-plans/${id}`);
```

### Example usage in a component

```jsx
import { useEffect, useState } from 'react';
import { getCategories, calculateTrip } from './api';

function App() {
    const [categories, setCategories] = useState([]);

    useEffect(() => {
        getCategories().then(setCategories).catch(console.error);
    }, []);

    async function handleEstimate() {
        try {
            const result = await calculateTrip({
                location_id: 1,
                place_id: 4,
                transport_id: 2,
                accommodation_id: 3,
                people: 2,
                days: 3,
            });
            console.log(result);
            // { distance_km: 350, transport_cost: 5600, accommodation_cost: 9000, total_cost: 14600, ... }
        } catch (err) {
            if (err.status === 422 && err.body.errors) {
                // show field-level validation messages
                console.warn(err.body.errors);
            } else {
                // err.message contains the friendly backend message
                console.warn(err.message);
            }
        }
    }

    return (
        <div>
            <button onClick={handleEstimate}>Estimate Dhaka → Bandarban</button>
            <ul>
                {categories.map((c) => (
                    <li key={c.id}>{c.name}</li>
                ))}
            </ul>
        </div>
    );
}
```

---

## Quick checklist for your first run

1. Start XAMPP MySQL.
2. In this backend folder: `php artisan migrate:fresh --seed` (only first time).
3. In this backend folder: `php artisan serve`.
4. In your frontend folder: create `.env.local` with `VITE_API_BASE_URL=http://127.0.0.1:8000/api`.
5. In your frontend folder: `npm run dev`.
6. Open `http://localhost:5173` — your React app should be able to call the backend on port 8000 without any proxy.
