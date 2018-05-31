<?php

namespace Xiaomi;

class XiaomiPacket {

    private $pkt;
    private $peer;

    public function __construct(string $pkt,string $peer) {
        $this->pkt=json_decode($pkt);
        $this->pkt->data=json_decode($this->pkt->data,true);
        $this->peer=new \stdClass;
        $this->peer->host=parse_url($peer,PHP_URL_HOST);
        $this->peer->port=parse_url($peer,PHP_URL_PORT);
    }

    public function getAllData(): \stdClass {
        return $this->pkt;
    }

    public function getCmd(): string {
        return $this->pkt->cmd;
    }

    public function getModel(): string {
        return $this->pkt->model;
    }

    public function getSid(): string {
        return $this->pkt->sid;
    }

    public function getShortId(): string {
        return $this->pkt->short_id;
    }

    public function getData(): array {
        return $this->pkt->data;
    }

    public function getPeer(): \stdClass {
        return $this->peer;
    }

}
