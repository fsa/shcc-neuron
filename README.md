# Контроллер умного дома SHCC

Проект на стадии разработки. В настоящее время возможны значительные изменения в структуре базы данных, которые необходимо выполнять вручную, либо пересоздавать базу данных.

[Документация](docs/index.md)

## Цели проекта

Разработка простого сервера умного дома на языке PHP:

- поддержка оборудования, которое имеется у автора проекта;
- максимально простой и понятный код на PHP;
- возможность настраивать автоматизацию с помощью скриптов на языке PHP;
- низкие требования к аппаратному обеспечению с возможностью запуска сервера на одноплатных ПК.

Идея создания данного проекта родилась после использования проекта [MajorDoMo](https://github.com/sergejey/majordomo). Главной целью проекта - создание простого контроллера умного дома для людей, владеющих навыками программирования на PHP.

## Системные требования

- systemd;
- nginx 1.18;
- PHP 7.4;
- PostgreSQL 12;
- Redis 5.0.

## Уже реализовано

В текущем состоянии проект имеет следующий функционал:

- получение данных и частичное управление оборудованием Xiaomi и Yeelight;
- сбор данных и ведение журнала с датчиков температуры, влажности, давления и других аналоговых датчиков;
- сбор данных и ведение журнал с цифровых датчиков;
- глосовые уведомления с использованием Яндекс SpeechKi1t.

## Контакты

Обсудить проект можно [в Телеграм группе](https://t.me/shcc_ru).

Связаться с автором проекта можно по [E-mail: support@tavda.net](mailto:support@tavda.net).