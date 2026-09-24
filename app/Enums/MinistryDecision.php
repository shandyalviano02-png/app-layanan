<?php

namespace App\Enums;

enum MinistryDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /**
     * Get user-friendly Indonesian label for the ministry decision.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Keputusan',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }
}
