<h1 align="center">
 <img
  width="100%"
  alt="logo"
  src="https://raw.githubusercontent.com/zerolabnet/tor-bridges-proxy/main/docs/logo.jpg">
    <br/>
    Tor Bridges Proxy
</h1>

### Описание

Абсолютно минималистичный образ Tor с SOCKS 5 proxy сервером, TorDNS и веб-интерфейсом с авторизацией для добавления мостов. Без функции выходного узла, только SOCKS 5 proxy и TorDNS (через Tor выполняется разрешение только A-записей). При первом запуске автоматически генерируется пароль для доступа в админ-панель.

<p align="center">
 <img src="https://raw.githubusercontent.com/zerolabnet/tor-bridges-proxy/main/docs/01-scr.png" width="100%">
</p>

### Установка, используя docker

```bash
docker run -d \
--name tor-bridges-proxy \
--restart=unless-stopped \
-p 9150:9150/tcp \
-p 9151:9151/tcp \
-p 53:9053/udp \
zerolabnet/tor-bridges-proxy:latest
```

### Порты по умолчанию

```
9150 - порт SOCKS 5 proxy для трафика через сеть Tor
9151 - порт веб-сервера для доступа в админ-панель
9053 - порт для DNS запросов через сеть Tor
```
Переопределите по своему усмотрению.

### Пароль для авторизации в админ-панеле

После первого запуска смотрим лог контейнера, в нем вы найдете пароль для авторизации `Your login password:`.

```bash
docker logs tor-bridges-proxy
```

### Получаем мост через tg-bot:

https://t.me/GetBridgesBot

### Заходим в админ-панель и прописываем мост:

http://YOUR_IP:9151
