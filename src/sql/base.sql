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

-- Устройства
CREATE TABLE devices (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(64) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `name` text NOT NULL,
  `place_id` int(10) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `devices_places_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Датчики устройств
CREATE TABLE device_sensors (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_id` int(10) UNSIGNED NOT NULL,
  `property` varchar(64) NOT NULL,
  `property_name` text NOT NULL,
  `unit` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `device_sensor_devices_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- История показаний датчиков устройств
CREATE TABLE device_sensor_history (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_sensor_id` int(10) UNSIGNED NOT NULL,
  `place_id` int(10) UNSIGNED NOT NULL,
  `value` decimal(7,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `device_sensor_history_device_sensors_ibfk_1` FOREIGN KEY (`device_sensor_id`) REFERENCES `device_sensors` (`id`),
  CONSTRAINT `device_sensor_history_places_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

