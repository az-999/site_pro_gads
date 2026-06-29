# Site.pro GAds — платформа автоматизации ключевых слов

Тестовое задание: импорт ключевых слов из CSV/JSON, очистка, подготовка и экспорт кампании для Google Ads Editor.

## Быстрый старт

```bash
docker compose up -d --build
docker compose exec yii2 php yii migrate --interactive=0
```

Первый запуск может занять 1–2 минуты (установка Composer-зависимостей внутри контейнера).

## URL и доступ

| Сервис | URL |
|--------|-----|
| **Админка** | http://localhost:5000/ |
| **Вход** | http://localhost:5000/login |
| **phpMyAdmin** | http://localhost:5009 |
| **MySQL** | `localhost:5006` (user: `app`, password: `app`, db: `site_pro_gads`) |
| **Memcached** | `localhost:5001` |

**Логин:** `alex@site.pro` / `111`

## Test plan

1. Откройте http://localhost:5000/login и войдите.
2. Перейдите в **Импорт**, выберите тип источника и загрузите файлы из `yii2/data/samples/` (CSV или JSON).
3. На **Дашборде** нажмите **Обработать всё** — запустится очистка и подготовка.
4. Откройте **Ключевые слова** — проверьте фильтры по статусу и причине отклонения (`junk`, `brand`, `duplicate`, `low_volume`).
5. Откройте **Превью** — группы по языкам (RU/EN) с заголовками и URL.
6. На странице **Экспорт** скачайте CSV и откройте в Google Ads Editor.

## Логика очистки (п.2 ТЗ)

После импорта ключевые слова проходят пайплайн:

- **Мусор** — слишком короткие запросы, стоп-слова (`login`, `www`, `торрент` и т.д.).
- **Бренды** — `site.pro`, `wix`, `tilda` и др. (настраивается в **Настройках**).
- **Дубликаты** — повторы после нормализации (lowercase, trim, ё→е).
- **Частота** — volume/impressions ниже порога (по умолчанию 50).

Отклонённые слова не удаляются из БД — им присваивается статус `rejected` с полем `reject_reason`.

## Подготовка для Google Ads (п.3 ТЗ)

- Исключаются уже использованные (источник `google_ads`) и запрещённые слова.
- Оставшиеся группируются по языку (кириллица → RU, латиница → EN).
- Формируются объявления с целевым URL (`site.pro/` / `site.pro/ru/`).
- Экспорт — CSV для Google Ads Editor.

## Структура проекта

```
site_pro_gads/
├── docker-compose.yml
├── yii2/                  # Yii2 + Bootstrap 5
│   ├── data/samples/      # мок-данные
│   ├── services/          # Import, Clean, Prepare, Export
│   └── web/js/            # Simple-Ajax-Uploader
└── tz.md
```

## Мок-данные

| Файл | Назначение |
|------|------------|
| `google_ads_keywords.csv` | Уже используемые ключевые слова |
| `search_console_queries.csv` / `.json` | Запросы из Search Console |
| `ahrefs_organic_keywords.csv` | Органика блога |
| `ahrefs_paid_keywords.csv` | Ключевые слова конкурентов |

## Остановка

```bash
docker compose down
```

Данные MySQL сохраняются в Docker volume `mysql_data`.
