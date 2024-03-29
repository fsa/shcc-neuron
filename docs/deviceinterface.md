# Интерфейс устройств Smarthome\DeviceInterface

Каждое устройство, доступное в системе умного дома обязано поддерживать методы интерфейса Smarthome\DeviceInterface. В их число входит:

- init($device_id, $init_data) - минимальная инициализация объекта для возможности управления устройством, где $device_id - уникальный идентификатор устройства, $init_data - массив с параметрами устройства (ключ=>значение). Необходим для внутреннего использования.
- getDeviceDescription(): string - возвращает описание устройства.
- getInitDataList(): array - возвращает список свойств объекта, которые будут присвоены при минимальной инициализации.
- getInitDataValues(): array - возвращает текущие свойства объекта, которые нужны при инициализации.
- getDeviceId(): string - возвращает идентификатор устройства, уникальный внутри модуля.
- getModuleName(): string - возвращает наименование модуля устройства.
- getDeviceStatus(): string - возвращает детальную информацию о состоянии устройства в текстовом виде.
- getLastUpdate(): int - возвращает дату и время последнего обновления данных устройства в формате timestamp.
