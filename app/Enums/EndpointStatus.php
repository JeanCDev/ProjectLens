<?php

namespace App\Enums;

enum EndpointStatus: string
{
    case Healthy = 'healthy';
    case Degraded = 'degraded';
    case Down = 'down';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Healthy => 'Saudável',
            self::Degraded => 'Degradado',
            self::Down => 'Fora do Ar',
            self::Unknown => 'Desconhecido',
        };
    }
}
