<?php

namespace Yeelight;

class SocketServer {
    
    const YEELIGHT_MULTICAST_ADDRESS="239.255.255.250";
    const YEELIGHT_MULTICAST_PORT=1982;
    const MAX_LENGTH=1024;

    private $stream;

    public function __construct() {
    }

    public function run() {
        $this->stream=$this->runLocalDiscovery();
    }
    
    public function runLocalDiscovery() {
        $stream=stream_socket_server("udp://0.0.0.0:".self::YEELIGHT_MULTICAST_PORT,$errno,$errstr,STREAM_SERVER_BIND);
        if (!$stream) {
            throw new Exception("$errstr ($errno)");
        }
        $socket=socket_import_stream($stream);
        if (!$socket) {
            throw new Exception('Unable to import stream.');
        }
        if (!socket_set_option($socket,SOL_SOCKET,SO_REUSEADDR,1)) {
            throw new Exception('Unable to enable SO_REUSEADDR');
        }
        if (!socket_set_option($socket,IPPROTO_IP,MCAST_JOIN_GROUP,['group'=>self::YEELIGHT_MULTICAST_ADDRESS,'interface'=>0])) {
            throw new Exception('Unable to join multicast group');
        }
        return $stream;
    }

    public function getPacket(): YeelightPacket {
        $pkt=stream_socket_recvfrom($this->stream,self::MAX_LENGTH,0,$peer);
        return new YeelightPacket($pkt,$peer);
    }

    public function sendMessage($message,$address) {
        stream_socket_sendto($this->stream,$message,0,$address);
    }

    public function sendDiscover() {
        $message="M-SEARCH * HTTP/1.1\r\n".
                "HOST: 239.255.255.250:1982\r\n".
                "MAN: \"ssdp:discover\"\r\n".
                "ST: wifi_bulb\r\n";
        $this->sendMessage($message,self::YEELIGHT_MULTICAST_ADDRESS.':'.self::YEELIGHT_MULTICAST_PORT);
    }

    public function __destruct() {
        fclose($this->stream);
    }
}
