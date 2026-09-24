<?php

namespace App\Enums;

enum ApprovalDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Returned = 'returned';

    /**
     * Get user-friendly Indonesian label for the approval decision.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Keputusan',
            self::Approved => 'Disetujui / Diparaf',
            self::Returned => 'Dikembalikan / Ditolak',
        };
    }
}
