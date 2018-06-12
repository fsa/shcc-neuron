INSERT INTO `objects` (`id`, `name`) VALUES
(1, 'Квартира');

INSERT INTO `places` (`id`, `object_id`, `name`, `root_place_id`) VALUES
(1, 1, 'Не установлен', NULL),
(2, 1, 'Комната', NULL),
(3, 1, 'Кухня', NULL),
(4, 1, 'Прихожая', NULL),
(5, 1, 'Коридор', NULL),
(6, 1, 'Ванная', NULL),
(7, 1, 'Туалет', NULL),
(8, 1, 'Кладовка', 4),
(9, 1, 'Дверь на балкон', 1),
(10, 1, 'Окно на кухне', 2),
(11, 1, 'Входная дверь', 3);

INSERT INTO `devices` (`id`, `module`, `uid`, `name`, `place_id`) VALUES
(1, 'xiaomi', 'f0b4299a72d0', 'Шлюз', 5),
(2, 'xiaomi', '158d0001f50bba', 'Aqara, датчик температуры и влажности', 2),
(3, 'xiaomi', '158d00010e3a1a', 'Xiaomi, датчик температуры и влажности', 3),
(4, 'xiaomi', '158d00015a89a8', 'Датчик движения Xiaomi', 4);


INSERT INTO `device_sensors` (`id`, `device_id`, `property`, `property_name`, `unit`) VALUES
(1, 2, 'temperature', 'Температура воздуха', '&deg;C'),
(2, 2, 'humidity', 'Относительная влажность', '%'),
(3, 2, 'pressure', 'Атмосферное давление', 'мм.рт.ст.'),
(4, 3, 'temperature', 'Температура воздуха', '&deg;C'),
(5, 3, 'humidity', 'Относительная влажность', '%');

