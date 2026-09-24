<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Received = 'received';
    case Verification = 'verification';
    case ClarificationRequested = 'clarification_requested';
    case Dispatched = 'dispatched';
    case InHandling = 'in_handling';
    case Resolved = 'resolved';
    case Duplicate = 'duplicate';
    case Invalid = 'invalid';

    /**
     * Get user-friendly Indonesian label for the complaint status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Received => 'Laporan Diterima',
            self::Verification => 'Verifikasi Awal',
            self::ClarificationRequested => 'Menunggu Klarifikasi Pelapor',
            self::Dispatched => 'Didisposisikan ke Petugas/Unit',
            self::InHandling => 'Dalam Penanganan',
            self::Resolved => 'Selesai Ditangani',
            self::Duplicate => 'Laporan Duplikat',
            self::Invalid => 'Laporan Tidak Valid',
        };
    }

    /**
     * Check if complaint is resolved or closed.
     */
    public function isClosed(): bool
    {
        return in_array($this, [self::Resolved, self::Duplicate, self::Invalid], true);
    }
}
