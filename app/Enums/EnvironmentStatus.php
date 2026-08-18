<?php

namespace App\Enums;

enum EnvironmentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::Maintenance => 'Manutenção',
        };
    }
}
