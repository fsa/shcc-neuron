CREATE TABLE users (
    uuid uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    login varchar(30) NOT NULL UNIQUE,
    password_hash text,
    name text NOT NULL,
    email text,
    scope text[],
    groups text[],
    disabled boolean NOT NULL DEFAULT false
);
COMMENT ON TABLE users IS 'Пользователи';

CREATE TABLE user_groups (
    name text NOT NULL PRIMARY KEY,
    scope text[],
    description text
);
COMMENT ON TABLE user_groups IS 'Группы доступа';

CREATE TABLE user_scopes (
    name text NOT NULL PRIMARY KEY,
    description text
);
COMMENT ON TABLE user_scopes IS 'Права доступа';

-- Не обязательная таблица, используется если указать SESSION_STORAGE=pdo
CREATE TABLE sessions (
    uid uuid PRIMARY KEY,
    token varchar(32) NOT NULL,
    expires timestamptz NOT NULL, # Возможно нужно заменить без TZ
    entity jsonb NOT NULL
);
CREATE INDEX sessions_expires_idx ON sessions (expires);
COMMENT ON TABLE sessions IS 'Сессии пользователей';
