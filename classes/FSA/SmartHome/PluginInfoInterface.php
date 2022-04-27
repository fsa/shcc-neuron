<?php

namespace FSA\SmartHome;

interface PluginInfoInterface
{
    public function getName(): string;
    public function getDescription(): string;
    public function getDaemonInfo(): ?array;
}
