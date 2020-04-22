# История изменений

## v0.6.0, 22.04.2020

Таблица модулей стала ненужна. Необходимо удалить поле module_id из таблицы
devices:
```sql
ALTER TABLE devices DROP COLUMN module_id;
```
После этого можно удалить таблицу:
```sql
DROP TABLE modules;
После обновления системы необходимо перейти в настройки модулей и активировать
необходимые демоны. После активации необходимо перезапустить сервис shcc.

Добавлен новый модуль для получение текущей погоды с Gismeteo.ru.

## v0.5.0, 26.03.2020

1. Изменение формата таблицы auth_sessions. Поле user_entity теперь имеет
формат text NOT NULL. Для обновлениия необходимо пересоздать таблицу:
```sql
DROP TABLE auth_sessions;
CREATE TABLE auth_sessions (
    uid varchar(32) NOT NULL,
    token varchar(32) NOT NULL,
    expires timestamptz NOT NULL,
    user_entity text NOT NULL,
    ip inet DEFAULT NULL,
    browser text DEFAULT NULL,
    created timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX auth_sessions_uid_idx ON auth_sessions (uid);
CREATE UNIQUE INDEX auth_sessions_token_idx ON auth_sessions (token);
CREATE INDEX auth_sessions_expires_idx ON auth_sessions (expires);
COMMENT ON TABLE auth_sessions IS 'Сессии пользователей системы';
```
При этом будет произведён выход всех активных пользователей из системы.

2. Для увеличения гибкости, изменён формат файла настроек. Вместо JSON теперь
используется php скрипт. Формат данных - ассоциативные массивы (ранее были
объекты). При использовании данных из файла настроек в пользовательских
скриптах необходимо скорректировать сслылки на свойства объекта на ссылки
на индексы массива.

3. Класс HTML был удалён, его функционал теперь выполняет httpResponse. При
использовании класса HTML в файлах /custom/*, необходимо их скорректировать.

## v0.4.0, 21.10.2019

Проект переименован.