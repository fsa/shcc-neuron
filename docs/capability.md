# Возможности устройств

Устройства могут реализовывать методы интерфейсов, т.е. есть возможность управлять единообразно различными типами оборудования.

# Интерфейс SmartHome\Device\Capability\PowerInterface
Методы интерфейса:
- setPower(bool $value) - включить или выключить устройство в зависимости от аргумента
- setPowerOn() - включить устройство
- setPowerOff() - выключить устройство

# Интерфейс SmartHome\Device\Capability\ColorHsvInterface
Методы интерфейса:
- setHSV($hue, $sat, $value) - установка цвета свечения лампы по модели HSV (тон, насыщенность, яркость).

# Интерфейс SmartHome\Device\Capability\ColorRgbInterface
Методы интерфейса:
- setRGB(int $value) - установка цвета свечения лампы по модели RGB (красный, зелёный, синий). В качестве параметра принимается число int соответствующее закодированному HEX значению (используйте функции PHP hexdec и dechex для преобразований, если потребуется).

# Интерфейс SmartHome\Device\Capability\ColorTInterface
Методы интерфейса:
- setCT(int $ct_value) - задаёт цветовую температуру источника света в кельвинах.

# Интерфейс SmartHome\Device\Capability\ColorTInterface
Методы интерфейса:
- setThermostat(string $value) - установка режима работы оборудования. Принимает значения: heat, cool, auto, eco, dry, fan_only.
- setFanSpeed(string $value) - установка скорости вращения вентилятора. Принимает значения: auto, low, medium, high.

# Интерфейс SmartHome\Device\Capability\ColorTInterface
Методы интерфейса:
- setMute(bool $value) - включение или выключение звука на устройстве в зависимости от значения параметра.