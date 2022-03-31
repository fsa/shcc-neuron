<?php

namespace miIO;

class GenericDevice implements \SmartHome\DeviceInterface {

    private $uid;
    private $location;
    private $token;
    private $timediff;
    private $connection;
    private $updated;
    private $data="";

    public function __construct(GenericDevice $device=null) {
        if (!is_null($device)) {
            $this->uid=$device->getHwid();
            $this->location=$device->getDeviceAddr();
            $this->token=$device->getDeviceToken();
            $this->timediff=$device->getDeviceTimeDiff();
        }
    }

    public function getDescription(): string {
        return "Неизвестное устройство";
    }

    public function getState(): array {
        return ['locatation'=>$this->location];
    }

    public function __toString(): string {
        return sprintf('Токен: %s. Адрес: %s', $this->token?'указан':'не указан', $this->location)??'Нет данных';
    }

    public function getEventsList(): array {
        return [];
    }

    public function getHwid(): string {
        return $this->uid;
    }

    public function getInitDataList(): array {
        return ['token'=>'Токен'];
    }

    public function getInitDataValues(): array {
        return ['token'=>$this->token];
    }

    public function getLastUpdate(): int {
        return $this->updated??0;
    }

    public function init($device_id, $init_data): void {
        $this->uid=$device_id;
        foreach ($init_data as $key=> $value) {
            $this->$key=$value;
        }
    }

    public function getDeviceAddr(): ?string {
        return $this->location;
    }

    public function getDeviceToken(): ?string {
        return $this->token;
    }

    public function setDeviceToken(string $token): void {
        $this->token=$token;
    }

    public function getDeviceTimeDiff(): ?int {
        return $this->timediff;
    }

    public function update(MiPacket $pkt) {
        if ($pkt->isHelloPacket()) {
            $this->uid=$pkt->getDeviceId();
            $this->location=$pkt->getDeviceAddr();
            $this->timediff=$pkt->getDeviceTimestamp()-time();
        } else {
            $pkt->setToken($this->token);
            $this->data.=$pkt->decryptMessage();
        }
        $this->updated=time();
    }

    public function sendCommand(string $method, array $params=[]): string {
        #TODO: проверка token и location
        $id=time();
        $cmd=[
            'id'=>$id,
            'method'=>$method,
            'params'=>$params
        ];
        $cmd=json_encode($cmd)."\r\n";
        $connection=$this->getConnection();
        $pkt=new MiPacket();
        $pkt->setDeviceId($this->uid);
        $pkt->setToken($this->token);
        $connection->sendTo($this->location, $pkt->buildMessage($cmd, time()+$this->timediff));
        $responce=$connection->getPacket();
        $responce->setToken($this->token);
        return $responce->decryptMessage();
    }

    public function getResponse(): MiPacket {
        $connection=$this->getConnection();
        return $connection->getPacket();
    }

    private function getConnection(): SocketServer {
        if (!is_null($this->connection)) {
            return $this->connection;
        }
        $this->connection=new SocketServer;
        $this->connection->setTimeoutSocket(2);
        return $this->connection;
    }

    public function disconnect(): void {
        $this->connection=null;
    }

}
