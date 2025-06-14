# Streaming Service Web (Pure PHP + Nginx + PostgreSQL + HTML + CSS + JS)

Bu notlar, saf PHP ile geliştirdiğim `streamingservice` adlı web projesinin sıfırdan kurulumu içindir.

---

## Ubuntu Server Kurulumu

- VirtualBox üzerinden GUI’siz Ubuntu Server kuruldu.
- Sistem güncellenerek gerekli teknolojiler kuruldu

```bash
    sudo apt update && sudo apt upgrade -y
    sudo apt install nginx php php-fpm php-pgsql postgresql postgresql-contrib git unzip curl composer ufw rsync -y
```

- `ssh` ile erişim için sunucuya bağlantı denendi.

```bash
    sudo systemctl status ssh
    sudo ufw allow OpenSSH
    sudo systemctl enable ssh
```

---

## Apache Kaldırıldı

Varsayılan olarak gelen Apache web sunucusu kaldırıldı:

```bash
    sudo systemctl stop apache2
    sudo apt purge apache2 apache2-utils apache2-bin apache2.2-common -y
    sudo apt autoremove -y
    sudo systemctl disable apache2
```

## NGINX Yapılandırılması

- `nginx.conf` dosyası doğrudan düzenlendi.
- Gerekli konfigürasyonlar yapıldı.

```bash
    sudo nginx -t
    sudo systemctl restart nginx
```

## Yardımcı Scriptlerin Yazılması

- MVC yapısı ve gerekli izinler için `php_mvc.sh` yazıldı.
- Sunucuya web dosyalarını gönderebilmek için `web_update.sh` yazıldı.

### Proje Dizin yapısı

---

#### Proje Adı: _streamingservice_

#### Konum: _/var/www/streamingservice_

```
streamingservice/
    app/
    ├── controllers/
    │     └── HomeController.php 
    ├── models/
    ├── views/
    │     ├── home/
    │     │     ├── index.php 
    │     ├── layouts/
    │     │     ├── header.php 
    │     │     └── footer.php 
    ├── config/
    │     ├── definitions.php 
    │     ├── db.php
    │     └── bootstrap.php
    ├── core/
    │       ├── Logger.php
    │       ├── helpers.php
    │       └── env_loader.php
    ├── public/
    │       ├── css/
    │       ├── fonts/
    │       ├── img/
    │       ├── js/
    │       ├── sass/
    │       ├── Source/
    │       ├── videos/
    │       └── index.php
    ├── router.php
    ├── logs/
    .env
    .gitignore
    web_update.sh
    php_mvc.sh
```

## Veritabanı Oluşturuldu

```sql
    CREATE DATABASE streamingservice;
    CREATE USER root WITH PASSWORD 'şifreniz';
    GRANT ALL PRIVILEGES ON DATABASE streamingservice TO root;
```

> `.env` dosyasındaki bilgiler veritabanı bağlantısı için kullanılmaktadır. Şifre aynı olmalıdır.

## Yapı Geliştirildi

- `app/views/layouts/` dizini oluşturuldu. Ortak kullanılan `header.php` ve `footer.php` buraya taşındı.
- `core/helpers.php` dosyası oluşturuldu.
  - `asset()` fonksiyonu eklendi. CSS/JS dosyalarının yolunu dinamik oluşturmak için kullanılır
- `config/definitions.php` içerisine `BASE_URL` tanımı eklendi.
- Tema projeye başarıyla entegre edildi. Tüm CSS, JS, resim gibi varlıklar `public/` altına yerleştirildi.
  - `public/` dizini altına şu klasörler eklendi:
    - `css/`, `js/`, `fonts/`, `img/`, `videos/`, `sass/`, `Source/`
- Ana sayfa `HomeController` üzerinden çalışacak şekilde yönlendirildi.
  - View dosyası: `app/views/home/index.php`
- Layout sistemi ile sayfalar artık `header.php` ve `footer.php` dosyalarıyla çevreleniyor.
- `web_update.sh` scripti geliştirildi:
  - `scp` ile dosyalar uzak sunucuya gönderiliyor.
  - Gönderim öncesi yedek alınıyor ve en fazla 5 yedek tutuluyor.
  - Dosya gönderimi sonrası uzak sunucuda `chown` ve `chmod` otomatik uygulanıyor.

---

## Genel Durum

- [x] Ubuntu Server kuruldu  
- [x] Apache kaldırıldı  
- [x] Nginx + PHP yapılandırıldı  
- [x] Proje dizin yapısı script ile kuruldu  
- [x] Veritabanı PostgreSQL ile tanımlandı  
- [x] Web projesine tarayıcıdan erişildi
- [x] MVC yapılandırmasına uygun layout sistemi (`header.php`, `footer.php`) oluşturuldu
- [x] Tema entegre edildi, varlık dosyaları (`css`, `js`, `img`, vb.) `public/` altına yerleştirildi
- [X] Yardımcı Scriptler yazıldı

---

