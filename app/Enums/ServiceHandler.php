<?php

namespace App\Enums;

enum ServiceHandler: string
{
    case Generic = 'generic';
    case Dtsen = 'dtsen';
    case Pbi = 'pbi';

    /**
     * Get user-friendly Indonesian label for the service handler.
     */
    public function label(): string
    {
        return match ($this) {
            self::Generic => 'Pengajuan Umum',
            self::Dtsen => 'Surat Keterangan DTSEN',
            self::Pbi => 'Reaktivasi KIS / PBI-JK',
        };
    }
}
