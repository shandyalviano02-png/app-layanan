<?php

namespace App\Enums;

enum AttachmentType: string
{
    case Photo = 'photo';
    case Document = 'document';

    /**
     * Get user-friendly Indonesian label for the attachment type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Photo => 'Foto / Gambar',
            self::Document => 'Dokumen / Berkas',
        };
    }
}
