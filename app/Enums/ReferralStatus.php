<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case InService = 'in_service';
    case Completed = 'completed';
    case Declined = 'declined';
    case Cancelled = 'cancelled';

    /**
     * Get user-friendly Indonesian label for the referral status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf Rujukan',
            self::Sent => 'Terkirim ke Lembaga',
            self::Accepted => 'Diterima Lembaga',
            self::InService => 'Dalam Pelayanan Lembaga',
            self::Completed => 'Pelayanan Rujukan Selesai',
            self::Declined => 'Ditolak Lembaga',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
