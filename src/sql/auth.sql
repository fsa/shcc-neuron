CREATE TABLE oauth_clients (
    uuid uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    client_id varchar NOT NULL UNIQUE,
    client_secret varchar NOT NULL,
    redirect_uris varchar[],
    description varchar
);
COMMENT ON TABLE oauth_clients IS 'Клиенты сервера OAuth 2.0';

CREATE TABLE oauth_tokens (
    code varchar NOT NULL UNIQUE,
    access_token varchar UNIQUE,
    token_expires_on timestamptz,
    refresh_token varchar UNIQUE,
    client_uuid uuid REFERENCES oauth_clients (uuid),
    user_uuid uuid REFERENCES users (uuid),
    scope text[],
    created timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated timestamptz NOT NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE auth_tokens IS 'Токены OAuth 2.0';
