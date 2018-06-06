<?php

namespace Yeelight;

class SocketServer {

    private $stream;
    private $ip;
    private $port;

    public function __construct(string $ip="239.255.255.250",int $port=1982) {
        $this->ip=$ip;
        $this->port=$port;
    }

    public function run() {
        $this->stream=stream_socket_server("udp://0.0.0.0:".$this->port,$errno,$errstr,STREAM_SERVER_BIND);
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
        if (!socket_set_option($socket,IPPROTO_IP,MCAST_JOIN_GROUP,['group'=>$this->ip,'interface'=>0])) {
            throw new Exception('Unable to join multicast group');
        }
    }

    public function getPacket(): YeelightPacket {
        $pkt=stream_socket_recvfrom($this->stream,1024,0,$peer);
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
        $this->sendMessage($message,$this->ip.':'.$this->port);
    }

    public function __destruct() {
        fclose($this->stream);
    }
}
