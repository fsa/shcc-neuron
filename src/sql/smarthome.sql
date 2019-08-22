CREATE TABLE places (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    pid bigint REFERENCES places(id),
    name text NOT NULL
);
COMMENT ON TABLE places IS 'Места размещения устройств';

CREATE TABLE modules (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    name varchar(64) NOT NULL,
    namespace varchar(64) NOT NULL,
    daemon boolean DEFAULT false,
    settings boolean DEFAULT false,
    disabled boolean DEFAULT false
);
CREATE UNIQUE INDEX modules_name_idx ON modules (name);
COMMENT ON TABLE modules IS 'Модули';

CREATE TABLE devices (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    unique_name varchar(64) NOT NULL,
    module_id bigint REFERENCES modules(id),
    uid varchar(64) NOT NULL,
    description text,
    classname varchar(64),
    init_data jsonb,
    place_id bigint REFERENCES places(id),
    disabled boolean DEFAULT false
);
CREATE UNIQUE INDEX devices_unique_name_idx ON devices (unique_name);
CREATE UNIQUE INDEX devices_uid_idx ON devices (module_id, uid);
COMMENT ON TABLE devices IS 'Устройства';

CREATE TABLE meter_units (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    name text NOT NULL,
    unit varchar(64) NOT NULL
);
COMMENT ON TABLE meter_units IS 'Типы измеряемых параметров';

CREATE TABLE meters (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    device_id bigint REFERENCES devices(id),
    property varchar(64) NOT NULL,
    minimal float,
    maximal float,
    meter_unit_id bigint REFERENCES meter_units(id)
);
CREATE UNIQUE INDEX meters_property_idx ON meters (device_id, property);
COMMENT ON TABLE meters IS 'Измерительные датчики';

CREATE TABLE meter_history (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    meter_id bigint REFERENCES meters(id),
    place_id bigint REFERENCES places(id),
    meter_unit_id bigint REFERENCES meter_units(id),
    value float NOT NULL,
    timestamp timestamptz DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE meter_history IS 'Журнал измерений';

CREATE TABLE indicators (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    device_id bigint REFERENCES devices(id),
    property varchar(64) NOT NULL
);
CREATE UNIQUE INDEX indicators_property_idx ON indicators (device_id, property);
COMMENT ON TABLE indicators IS 'Контрольные датчики';

-- История срабатывания датчиков сигнализации устройств
CREATE TABLE indicator_history (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    indicator_id bigint REFERENCES indicators(id),
    place_id bigint REFERENCES places(id),
    value boolean NOT NULL,
    timestamp timestamptz DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE indicator_history IS 'Журнал контрольных датчиков';

CREATE TABLE variables (
    name varchar(64) NOT NULL,
    value text
);
CREATE UNIQUE INDEX variables_name_idx ON variables (name);
COMMENT ON TABLE variables IS 'Пользовательские и системные переменные';

CREATE TABLE tts_log (
    id bigint PRIMARY KEY GENERATED BY DEFAULT AS IDENTITY,
    timestamp timestamptz DEFAULT CURRENT_TIMESTAMP,
    text text
);
CREATE INDEX tts_log_timestamp_idx ON tts_log (timestamp);
COMMENT ON TABLE tts_log IS 'Журнал голосовых сообщений';

INSERT INTO modules (id, name, namespace, daemon, settings, disabled) VALUES
(1, 'xiaomi', 'Xiaomi', true, false, false),
(2, 'yeelight', 'Yeelight', true, false, false),
(3, 'yandex', 'Yandex', false, true, false);

INSERT INTO meter_units (id, name, unit) VALUES
(1, 'Температура воздуха', '&deg;C'),
(2, 'Относительная влажность', '%'),
(3, 'Атмосферное давление', 'мм.рт.ст.');
