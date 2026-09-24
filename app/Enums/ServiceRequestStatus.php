<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case Submitted = 'submitted';
    case DocumentCheck = 'document_check';
    case RevisionRequested = 'revision_requested';
    case DataVerification = 'data_verification';
    case EligibilityVerification = 'eligibility_verification';
    case Verification = 'verification';
    case Assessment = 'assessment';
    case InProcess = 'in_process';
    case AwaitingApproval = 'awaiting_approval';
    case Issued = 'issued';
    case RecommendationIssued = 'recommendation_issued';
    case ProposedToMinistry = 'proposed_to_ministry';
    case MinistryApproved = 'ministry_approved';
    case Reactivated = 'reactivated';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case MinistryRejected = 'ministry_rejected';

    /**
     * Get user-friendly Indonesian label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Diajukan',
            self::DocumentCheck => 'Pemeriksaan Berkas',
            self::RevisionRequested => 'Perlu Perbaikan Berkas',
            self::DataVerification => 'Verifikasi Data SIKS-NG',
            self::EligibilityVerification => 'Verifikasi Kelayakan',
            self::Verification => 'Verifikasi',
            self::Assessment => 'Assessment Lapangan',
            self::InProcess => 'Dalam Proses',
            self::AwaitingApproval => 'Menunggu Tanda Tangan',
            self::Issued => 'Surat Terbit',
            self::RecommendationIssued => 'Rekomendasi Diterbitkan',
            self::ProposedToMinistry => 'Diusulkan ke Kemensos',
            self::MinistryApproved => 'Disetujui Kemensos',
            self::Reactivated => 'Aktif Kembali',
            self::Completed => 'Selesai',
            self::Rejected => 'Ditolak',
            self::MinistryRejected => 'Ditolak Kemensos',
        };
    }

    /**
     * Determine if status is terminal (completed or rejected).
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Rejected, self::MinistryRejected], true);
    }
}
