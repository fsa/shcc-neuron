CREATE TABLE yandex_devices (
    uuid varchar PRIMARY KEY DEFAULT gen_random_uuid(),
    name varchar,
    description text,
    room varchar,
    type varchar NOT NULL,
    capabilities jsonb NOT NULL,
    action jsonb NOT NULL
);
COMMENT ON TABLE yandex_devices IS 'Виртуальные устройсва для умного дома Яндекс';
