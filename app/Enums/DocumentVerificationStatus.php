<?php

namespace App\Enums;

enum DocumentVerificationStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case RevisionNeeded = 'revision_needed';

    /**
     * Get user-friendly Indonesian label for the verification status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Valid => 'Sesuai / Valid',
            self::RevisionNeeded => 'Perlu Perbaikan',
        };
    }
}
