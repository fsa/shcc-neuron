INSERT INTO `objects` (`id`, `name`) VALUES
(1, 'Квартира');

INSERT INTO `places` (`id`, `object_id`, `name`, `root_place_id`) VALUES
(1, 1, 'Комната', NULL),
(2, 1, 'Кухня', NULL),
(3, 1, 'Прихожая', NULL),
(4, 1, 'Коридор', NULL),
(5, 1, 'Ванная', NULL),
(6, 1, 'Туалет', NULL),
(7, 1, 'Кладовка', 4),
(8, 1, 'Дверь на балкон', 1),
(9, 1, 'Окно на кухне', 2),
(10, 1, 'Входная дверь', 3);
