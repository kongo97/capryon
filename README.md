 # CAPRYON

```
docker build -t capryon .
docker run -d -p 8000:8000 -v "$PWD/src":/var/www/html --name capryon capryon
docker exec -it capryon bash

sudo chown -R simone:simone .
```

## Create capryon DB

Follow these commands:

```
sudo -u postgres psql

postgres=# create database capryon;
postgres=# grant all privileges on database capryon to ipp;

```

## Start cron

```
php artisan schedule:work
```