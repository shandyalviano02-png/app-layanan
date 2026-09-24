<?php

namespace App\Enums;

enum HandlingType: string
{
    case Direct = 'direct';
    case Referral = 'referral';
    case Both = 'both';

    /**
     * Get user-friendly Indonesian label for the handling type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Direct => 'Pelayanan Langsung',
            self::Referral => 'Rujukan ke Lembaga',
            self::Both => 'Pelayanan Langsung & Rujukan',
        };
    }
}
