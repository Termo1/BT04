# BT04 - REST API pre správu poznámok

## Notes CRUD

### GET /api/notes - Zoznam všetkých poznámok

![GET notes](docs/screenshots/screenshot-2026-03-09_10-23-29.png)

### GET /api/notes/1 - Detail poznámky

![GET note](docs/screenshots/screenshot-2026-03-09_10-23-57.png)

### POST /api/notes - Vytvorenie poznámky

![POST note](docs/screenshots/screenshot-2026-03-09_10-24-36.png)

### PUT /api/notes/1 - Aktualizácia poznámky

![PUT note](docs/screenshots/screenshot-2026-03-09_10-25-12.png)

### DELETE /api/notes/3 - Odstránenie poznámky (soft delete)

![DELETE note](docs/screenshots/screenshot-2026-03-09_10-25-40.png)

---

## Notes - vlastné endpointy

### GET /api/notes/stats/status - Štatistiky podľa statusu

![Stats](docs/screenshots/screenshot-2026-03-09_10-26-20.png)

### PATCH /api/notes/actions/archive-old-drafts - Archivácia starých konceptov

![Archive](docs/screenshots/screenshot-2026-03-09_10-27-09.png)

### PATCH /api/notes/1/toggle-pin - Prepnutie pripnutia poznámky

![Toggle pin](docs/screenshots/screenshot-2026-03-09_10-27-52.png)

### GET /api/users/2/notes - Poznámky používateľa s kategóriami

![User notes](docs/screenshots/screenshot-2026-03-09_10-28-20.png)

### GET /api/notes-actions/search?q=Laravel - Vyhľadávanie v poznámkach

![Search](docs/screenshots/screenshot-2026-03-09_10-28-42.png)

---

## Categories CRUD

### GET /api/categories - Zoznam všetkých kategórií

![GET categories](docs/screenshots/screenshot-2026-03-09_13-10-33.png)

### GET /api/categories/1 - Detail kategórie

![GET category](docs/screenshots/screenshot-2026-03-09_13-11-22.png)

### POST /api/categories - Vytvorenie kategórie

![POST category](docs/screenshots/screenshot-2026-03-09_13-11-58.png)

### PUT /api/categories/1 - Aktualizácia kategórie

![PUT category](docs/screenshots/screenshot-2026-03-09_13-12-27.png)

### DELETE /api/categories/5 - Odstránenie kategórie

![DELETE category](docs/screenshots/screenshot-2026-03-09_13-12-46.png)
