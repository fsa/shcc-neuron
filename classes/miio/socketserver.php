<?php

namespace miIO;

class SocketServer {

    const MIIO_PORT=54321;
    const DISCOVERY_TIMEOUT=2;

    private $stream;
    private $socket;

    public function __construct() {
        $this->stream=stream_socket_server("udp://0.0.0.0:".self::MIIO_PORT,$errno,$errstr,STREAM_SERVER_BIND);
        if (!$this->stream) {
            throw new Exception("$errstr ($errno)");
        }
        $this->socket=socket_import_stream($this->stream);
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
    
    public function sendTo($peer,$data) {
        return stream_socket_sendto($this->stream,$data,0,$peer);
    }
    
    public function getPacket(): MiPacket {
        $pkt=stream_socket_recvfrom($this->stream,4096,0,$peer);
        return new MiPacket($pkt, $peer);
    }

    public static function sendDiscovery() {
        $server=new self;
        $server->setBroadcastSocket();
        $server->setTimeoutSocket(self::DISCOVERY_TIMEOUT);
        $server->sendTo('255.255.255.255:'.self::MIIO_PORT, MiPacket::getHelloMessage());
    }

}
