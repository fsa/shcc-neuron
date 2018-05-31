<?php

namespace Xiaomi;

class SocketServer {
    
    private $stream;

    public function __construct($ip="224.0.0.50", $port=9898) {
        $this->stream=stream_socket_server("udp://0.0.0.0:".$port,$errno,$errstr,STREAM_SERVER_BIND);
        
        if (!$this->stream) {
            throw new Exception("$errstr ($errno)");
        }
        $socket=socket_import_stream($this->stream);
        if (!$socket) {
            throw new Exception('Unable to import stream.');
        }
        if (!socket_set_option($socket,SOL_SOCKET,SO_REUSEADDR,1)) {
            throw new Exception('Unable to enable SO_REUSEADDR');
        }
        if (!socket_set_option($socket,IPPROTO_IP,MCAST_JOIN_GROUP,['group'=>$ip,'interface'=>0])) {
            throw new Exception('Unable to join multicast group');
        }
    }

    public function getPacket() {
        $pkt=stream_socket_recvfrom($this->stream,1024,0,$peer);
        return new XiaomiPacket($pkt,$peer);
    }

}
