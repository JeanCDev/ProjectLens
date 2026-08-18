<?php

namespace App\Enums;

enum TeamMemberRole: string
{
    case Admin = 'admin';
    case Developer = 'developer';
    case Designer = 'designer';
    case DevOps = 'devops';
    case Manager = 'manager';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Developer => 'Desenvolvedor',
            self::Designer => 'Designer',
            self::DevOps => 'DevOps',
            self::Manager => 'Gerente',
            self::Viewer => 'Visualizador',
        };
    }
}
