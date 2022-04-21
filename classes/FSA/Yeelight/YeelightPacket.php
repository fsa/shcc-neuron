<?php

namespace FSA\Yeelight;

class YeelightPacket
{
    private $peer;
    private $header;
    private $params;
    private $isValid;

    public function __construct(string $packet, string $peer)
    {
        $this->params = [];
        $this->isValid = true;
        $this->peer = $peer;
        $lines = explode("\r\n", $packet);
        $this->header = array_shift($lines);
        foreach ($lines as $line) {
            $parts = explode(": ", $line, 2);
            if (sizeof($parts) == 2) {
                $this->params[$parts[0]] = $parts[1];
            } else {
                $error = true;
            }
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getPeer(): string
    {
        return $this->peer;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParamsJson(): string
    {
        return json_encode($this->params);
    }
}
