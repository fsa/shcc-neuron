<?php

namespace FSA\Yeelight;

class Socket
{
    const YEELIGHT_MULTICAST_ADDRESS = "239.255.255.250";
    const YEELIGHT_MULTICAST_PORT = 43210;
    const YEELIGHT_SSDP_PORT = 1982;
    const MAX_LENGTH = 1024;

    private $stream;

    public function __construct()
    {
    }

    public function run()
    {
        $this->stream = $this->runLocalDiscovery();
    }

    public function runLocalDiscovery()
    {
        $stream = stream_socket_server("udp://0.0.0.0:" . self::YEELIGHT_MULTICAST_PORT, $errno, $errstr, STREAM_SERVER_BIND);
        if (!$stream) {
            throw new Exception("$errstr ($errno)");
        }
        $socket = socket_import_stream($stream);
        if (!$socket) {
            throw new Exception('Unable to import stream.');
        }
        if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            throw new Exception('Unable to enable SO_REUSEADDR');
        }
        if (!socket_set_option($socket, IPPROTO_IP, MCAST_JOIN_GROUP, ['group' => self::YEELIGHT_MULTICAST_ADDRESS, 'interface' => 0])) {
            throw new Exception('Unable to join multicast group');
        }
        return $stream;
    }

    public function getPacket(): YeelightPacket
    {
        $pkt = stream_socket_recvfrom($this->stream, self::MAX_LENGTH, 0, $peer);
        return new YeelightPacket($pkt, $peer);
    }

    public function waitPacket(string $addr, int $cycles = 5, $timeout = 500000): ?YeelightPacket
    {
        for ($i = 0; $i < $cycles; $i++) {
            $r = [$this->stream];
            $w = null;
            $e = null;
            if (false === ($num_changed_streams = @stream_select($r, $w, $e, 0, $timeout / $cycles))) {
                throw new Exception('stream_select error');
            } elseif ($num_changed_streams > 0) {
                $pkt = stream_socket_recvfrom($this->stream, self::MAX_LENGTH, 0, $peer);
                if ($addr == parse_url($peer, PHP_URL_HOST)) {
                    return new YeelightPacket($pkt, $peer);
                }
            }
        }
        return null;
    }

    public function sendMessage($message, $address)
    {
        if (is_null($this->stream)) {
            $this->run();
        }
        stream_socket_sendto($this->stream, $message, 0, $address);
    }

    public function sendDiscover($addr = self::YEELIGHT_MULTICAST_ADDRESS, $port = self::YEELIGHT_SSDP_PORT)
    {
        $message = "M-SEARCH * HTTP/1.1\r\n" .
            "HOST: 239.255.255.250:1982\r\n" .
            "MAN: \"ssdp:discover\"\r\n" .
            "ST: wifi_bulb\r\n";
        $this->sendMessage($message, $addr . ':' . $port);
    }

    public function __destruct()
    {
        fclose($this->stream);
    }
}
