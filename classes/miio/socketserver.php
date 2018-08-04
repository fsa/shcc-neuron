<?php

namespace miIO;

class SocketServer {

    const MIIO_PORT=54321;
    const HELLO_MSG='21310020ffffffffffffffffffffffffffffffffffffffffffffffffffffffff';
    const DISCOVERY_TIMEOUT=2;

    private $socket;

    public function createSocket() {
        $stream=stream_socket_server("udp://0.0.0.0:".self::MIIO_PORT,$errno,$errstr,STREAM_SERVER_BIND);
        if (!$stream) {
            throw new Exception("$errstr ($errno)");
        }
        $this->socket=socket_import_stream($stream);
        if (!$this->socket) {
            throw new Exception('Unable to import stream.');
        }
        if (!socket_set_option($this->socket,SOL_SOCKET,SO_REUSEADDR,1)) {
            throw new Exception('Unable to enable SO_REUSEADDR');
        }
    }

    public function setTimeoutSocket(int $timeout) {
        if (!socket_set_option($this->socket,SOL_SOCKET,SO_RCVTIMEO,["sec"=>$timeout,"usec"=>0])) {
            throw new Exception('Unable to set SO_RCVTIMEO');
        }
    }

    public function setBroadcastSocket() {
        if (!socket_set_option($this->socket,SOL_SOCKET,SO_BROADCAST,1)) {
            throw new Exception('Unable to set SO_BROADCAST');
        }
    }
    
    public function sendTo($ip,$data) {
        return socket_sendto($this->socket,$data,strlen($data),0,$ip,self::MIIO_PORT);
    }
    
    public function receiveFrom() {
        $bytes=@socket_recvfrom($this->socket,$buf,4096,0,$remote_ip,$remote_port);
        if($bytes==0) {
            return false;
        }
        if($buf=='') {
            return true;
        }
        $mipacket=new MiPacket();
        $mipacket->parseMessage(bin2hex($buf));
        $mipacket->setRemoteAddr($remote_ip,$remote_port);
        return $mipacket;
    }

    public static function discovery() {
        $server=new self;
        $server->createSocket();
        $server->setBroadcastSocket();
        $server->setTimeoutSocket(self::DISCOVERY_TIMEOUT);
        $server->sendTo('255.255.255.255',hex2bin(self::HELLO_MSG));
        $result=[];
        while ($mipacket=$server->receiveFrom()) {
            if ($mipacket===true) {
                continue;
            }
            if($mipacket->getDeviceId()!='ffffffffffffffff') {
                $result[$mipacket->getDeviceId()]=$mipacket->getRemoteIp();
            }
        }
        return $result;
    }

}
