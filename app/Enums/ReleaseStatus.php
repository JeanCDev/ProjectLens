<?php

namespace App\Enums;

enum ReleaseStatus: string
{
    case Draft = 'draft';
    case PreRelease = 'pre_release';
    case Stable = 'stable';
    case Deprecated = 'deprecated';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::PreRelease => 'Pré-lançamento',
            self::Stable => 'Estável',
            self::Deprecated => 'Descontinuado',
        };
    }
}
