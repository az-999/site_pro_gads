# Nginx — sitepro.avatars-meta.com

Проксирует HTTPS на Docker-приложение Yii2 (`127.0.0.1:6003`).

## Установка на сервере

1. DNS: A-запись `sitepro.avatars-meta.com` → IP сервера.

2. Сертификаты — см. [ssl/README.md](ssl/README.md).

3. Подключить конфиг (путь к проекту замените при необходимости):

```bash
sudo ln -sf /opt/site_pro_gads/nginx/sitepro.avatars-meta.com.conf \
  /etc/nginx/sites-enabled/sitepro.avatars-meta.com.conf

sudo nginx -t
sudo systemctl reload nginx
```

4. Убедитесь, что контейнер слушает порт 6003:

```bash
cd /opt/site_pro_gads && docker compose ps
curl -I http://127.0.0.1:6003/login
```

5. Откройте https://sitepro.avatars-meta.com/login

## Структура

```
nginx/
├── sitepro.avatars-meta.com.conf   # конфиг виртуального хоста
├── ssl/
│   └── sitepro.avatars-meta.com/
│       ├── fullchain.pem
│       └── privkey.pem
└── README.md
```

## Пути в конфиге

Если проект лежит не в `/opt/site_pro_gads`, замените пути к `ssl_certificate` в `.conf` на актуальные.

Если используете certbot без копирования, можно указать:

```nginx
ssl_certificate     /etc/letsencrypt/live/sitepro.avatars-meta.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/sitepro.avatars-meta.com/privkey.pem;
```
