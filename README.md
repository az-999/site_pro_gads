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
| **Админка** | http://localhost:6003/ |
| **Вход** | http://localhost:6003/login |
| **phpMyAdmin** | http://localhost:5009 |
| **MySQL** | `localhost:5006` (user: `app`, password: `app`, db: `site_pro_gads`) |
| **Memcached** | `localhost:5001` |

**Логин:** `alex@site.pro` / `111`

## Test plan

1. Откройте http://localhost:6003/login и войдите.
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

## Деплой через GitHub Actions (только SSH-ключ)

В репозитории: **Settings → Secrets and variables → Actions → New repository secret**

| Secret | Куда что класть |
|--------|-----------------|
| `PROD_HOST` | IP или домен сервера |
| `PROD_USER` | SSH-пользователь (`root`, `deploy`, …) |
| `PROD_PATH` | `/opt/site_pro_gads` |
| `PROD_PORT` | `22` |
| `PROD_SSH_KEY` | **Приватный** ключ (секретный!) |

### Публичный или приватный?

| Ключ | Файл | Куда |
|------|------|------|
| **Приватный** | `deploy_key` (без `.pub`) | секрет `PROD_SSH_KEY` в GitHub |
| **Публичный** | `deploy_key.pub` | `~/.ssh/authorized_keys` на сервере |

**На GitHub всегда приватный.** Публичный на GitHub не кладут — им только открывают доступ на сервере.

### Настройка за 3 шага

**1. На своём компьютере** — создать пару ключей:
```bash
ssh-keygen -t ed25519 -C "github-actions-site-pro-gads" -f deploy_key -N ""
```

**2. На сервере** — добавить публичный ключ:
```bash
mkdir -p ~/.ssh && chmod 700 ~/.ssh
echo "ВСТАВЬТЕ_СОДЕРЖИМОЕ_deploy_key.pub" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```
Или одной командой с вашего ПК: `ssh-copy-id -i deploy_key.pub USER@HOST`

**3. В GitHub** — секрет `PROD_SSH_KEY`:
```bash
cat deploy_key
```
Скопируйте вывод целиком, включая строки `-----BEGIN OPENSSH PRIVATE KEY-----` и `-----END OPENSSH PRIVATE KEY-----`.

Проверка с ПК:
```bash
ssh -i deploy_key -p 22 USER@HOST "echo ok"
```

После push в `main` workflow выполнит `docker compose up -d --build` и миграции.
