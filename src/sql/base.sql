CREATE DATABASE IF NOT EXISTS `phpmd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `phpmd`;

-- Объекты недвижимости или их части
CREATE TABLE `objects` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Места размещения устройств
CREATE TABLE `places` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `object_id` int(10) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `root_place_id` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`root_place_id`),
  CONSTRAINT `places_objects_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Модули
CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `namespace` varchar(64) NOT NULL,
  `daemon_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Устройства
CREATE TABLE devices (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unique_name` varchar(64) NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `uid` varchar(64) NOT NULL,
  `name` text NOT NULL,
  `classname` varchar(64) NULL DEFAULT NULL,
  `init_data` text NULL DEFAULT NULL,
  `place_id` int(10) UNSIGNED NOT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE (`unique_name`),
  UNIQUE (`module_id`,`uid`),
  CONSTRAINT `devices_places_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`),
  CONSTRAINT `devices_modules_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Типы измеряемых параметров, сохраняемых в истории
CREATE TABLE measures (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `unit` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Измерительные датчики
CREATE TABLE meters (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` int(10) UNSIGNED NOT NULL,
  `property` varchar(64) NOT NULL,
  `measure_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `meters_devices_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
  CONSTRAINT `meters_measures_ibfk_1` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- История показаний измерительных датчиков устройств
CREATE TABLE meter_history (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `meter_id` int(10) UNSIGNED NOT NULL,
  `place_id` int(10) UNSIGNED NOT NULL,
  `measure_id` int(10) UNSIGNED NOT NULL,
  `value` float NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `meter_history_meters_ibfk_1` FOREIGN KEY (`meter_id`) REFERENCES `meters` (`id`),
  CONSTRAINT `meter_history_places_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`),
  CONSTRAINT `meter_history_measures_ibfk_1` FOREIGN KEY (`measure_id`) REFERENCES `measures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Сигнализационные датчики
CREATE TABLE indicators (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` int(10) UNSIGNED NOT NULL,
  `property` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `indicators_devices_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- История срабатывания датчиков сигнализации устройств
CREATE TABLE indicator_history (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `indicator_id` int(10) UNSIGNED NOT NULL,
  `place_id` int(10) UNSIGNED NOT NULL,
  `value` tinyint(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `indicator_history_sensors_ibfk_1` FOREIGN KEY (`indicator_id`) REFERENCES `indicators` (`id`),
  CONSTRAINT `indicator_history_places_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
