<?php

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\AttachmentType;
use App\Enums\ComplaintStatus;
use App\Enums\DocumentVerificationStatus;
use App\Enums\HandlingType;
use App\Enums\MinistryDecision;
use App\Enums\PbiReason;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warga = User::where('email', 'masyarakat@gmail.com')->first();
        $petugasLinjamsos = User::where('email', 'petugas.linjamsos@dinsos.blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first();
        $kabid = User::where('email', 'kabid.linjamsos@dinsos.blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();

        $kanigoroDistrict = District::where('name', 'Kanigoro')->first();
        $satreyanVillage = Village::where('name', 'Satreyan')->first();
        $kanigoroVillage = Village::where('name', 'Kanigoro')->first();

        $linjamsosUnit = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehsos%')->first();

        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();

        // ---------------------------------------------------------------------
        // 1. Layanan 1: SK DTSEN (Selesai / Terbit)
        // ---------------------------------------------------------------------
        $certificateNumber = '400.9/128/409.105/2026';
        if (! DtsenCertificate::where('certificate_number', $certificateNumber)->exists()) {
            $spmbPurpose = DtsenPurpose::where('code', 'spmb')->first();
            $dtsenNumber = NumberSequence::generateNext('DTSEN');

            $req1 = ServiceRequest::create([
                'request_number' => $dtsenNumber,
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $warga?->id,
                'applicant_name' => 'Budi Santoso',
                'applicant_nik' => '3505011010900008',
                'family_card_number' => '3505011010900001',
                'address' => 'RT 02 RW 03, Dusun Satreyan Lor',
                'village_id' => $satreyanVillage->id,
                'phone' => '081298765432',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::Issued,
                'is_priority' => false,
                'verification_result' => 'Data pemohon dan subjek terdaftar di SIKS-NG desil 2. Memenuhi kriteria SPMB Afirmasi (desil <= 5).',
                'officer_notes' => 'Berkas KTP dan KK valid. Surat siap diterbitkan.',
                'service_result' => 'Surat Keterangan DTSEN resmi telah diterbitkan dengan nomor '.$certificateNumber.'.',
                'completed_at' => Carbon::now()->subHours(5),
            ]);

            // Dokumen persyaratan
            $dtsenReqs = ServiceRequirement::where('service_type_id', $dtsenType->id)->get();
            foreach ($dtsenReqs as $requirement) {
                ServiceRequestDocument::create([
                    'service_request_id' => $req1->id,
                    'service_requirement_id' => $requirement->id,
                    'file_path' => 'documents/demo-ktp-kk.pdf',
                    'original_name' => str_contains($requirement->name, 'KTP') ? 'KTP_Budi.pdf' : 'KK_Budi.pdf',
                    'verification_status' => DocumentVerificationStatus::Valid,
                    'notes' => 'Dokumen asli terbaca jelas',
                ]);
            }

            // Detail Sertifikat DTSEN
            $cert = DtsenCertificate::create([
                'service_request_id' => $req1->id,
                'dtsen_purpose_id' => $spmbPurpose->id,
                'purpose_description' => 'Persyaratan pendaftaran SPMB Jalur Afirmasi SMAN 1 Talun',
                'subject_name' => 'Dimas Arya Santoso',
                'subject_nik' => '3505011505080002',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 2,
                'checked_at' => Carbon::now()->subDays(1),
                'checker_id' => $petugasLinjamsos?->id,
                'certificate_number' => $certificateNumber,
                'issued_at' => Carbon::now()->subHours(5),
                'valid_until' => Carbon::now()->addDays(30)->toDateString(),
                'signer_id' => $kadis?->id,
                'file_path' => 'certificates/SK-DTSEN-2026-00001.pdf',
                'verification_code' => 'DTSEN-V-'.strtoupper(substr(md5($dtsenNumber), 0, 10)),
            ]);

            // Approval berjenjang (Kabid -> Kadis)
            Approval::create([
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert->id,
                'step' => 1,
                'approver_id' => $kabid?->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Data sesuai verifikasi SIKS-NG desil 2. Draf disetujui untuk tanda tangan Kepala Dinas.',
                'decided_at' => Carbon::now()->subHours(8),
            ]);

            Approval::create([
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert->id,
                'step' => 2,
                'approver_id' => $kadis?->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Disetujui dan ditandatangani secara digital.',
                'decided_at' => Carbon::now()->subHours(5),
            ]);

            // Riwayat status
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $req1->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::Submitted->value,
                'notes' => 'Pengajuan berhasil dikirim oleh pemohon',
                'user_id' => $warga?->id,
                'created_at' => Carbon::now()->subDays(2),
            ]);

            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $req1->id,
                'from_status' => ServiceRequestStatus::Submitted->value,
                'to_status' => ServiceRequestStatus::DataVerification->value,
                'notes' => 'Pengecekan data pada SIKS-NG DTSEN oleh petugas',
                'user_id' => $petugasLinjamsos?->id,
                'created_at' => Carbon::now()->subDays(1),
            ]);

            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $req1->id,
                'from_status' => ServiceRequestStatus::DataVerification->value,
                'to_status' => ServiceRequestStatus::Issued->value,
                'notes' => 'Surat Keterangan DTSEN resmi telah terbit',
                'user_id' => $kadis?->id,
                'created_at' => Carbon::now()->subHours(5),
            ]);
        }

        // ---------------------------------------------------------------------
        // 2. Layanan 2: Reaktivasi PBI-JK (Kondisi Darurat Medis / Dalam Proses)
        // ---------------------------------------------------------------------
        $recommendationNumber = '400.9/89/REK-PBI/2026';
        if (! PbiReactivation::where('recommendation_number', $recommendationNumber)->exists()) {
            $pbiNumber = NumberSequence::generateNext('PBI');
            $req2 = ServiceRequest::create([
                'request_number' => $pbiNumber,
                'service_type_id' => $pbiType->id,
                'submitter_id' => $warga?->id,
                'applicant_name' => 'Siti Aminah',
                'applicant_nik' => '3505015504880005',
                'family_card_number' => '3505011010900001',
                'address' => 'Jl. Anggrek No. 14, Kanigoro',
                'village_id' => $kanigoroVillage->id,
                'phone' => '081298765432',
                'submitted_at' => Carbon::now()->subDays(1),
                'officer_id' => $petugasLinjamsos?->id,
                'work_unit_id' => $linjamsosUnit?->id,
                'status' => ServiceRequestStatus::ProposedToMinistry,
                'is_priority' => true,
                'verification_result' => 'Pasien dirawat inap darurat di RSUD Ngudi Waluyo Wlingi. Desil 1 terdaftar di DTKS. Masa nonaktif 3 bulan (memenuhi syarat < 6 bulan).',
                'officer_notes' => 'Rekomendasi Kadis telah terbit dan telah diinput ke SIKS-NG Kemensos.',
                'service_result' => null,
                'completed_at' => null,
            ]);

            PbiReactivation::create([
                'service_request_id' => $req2->id,
                'participant_name' => 'Siti Aminah',
                'participant_nik' => '3505015504880005',
                'bpjs_card_number' => '0001827364521',
                'deactivated_date' => Carbon::now()->subMonths(3)->toDateString(),
                'reason' => PbiReason::Emergency,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/892/RSNW/2026',
                'decile' => 1,
                'eligibility_notes' => 'Kondisi membutuhkan tindakan operasi mendesak, kepesertaan terdaftar PBI-JK.',
                'recommendation_number' => $recommendationNumber,
                'recommendation_issued_at' => Carbon::now()->subHours(12),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subHours(4),
                'ministry_decision' => MinistryDecision::Pending,
            ]);
        }

        // ---------------------------------------------------------------------
        // 3. Layanan 3: Kasus Rehabilitasi Sosial (Klien Lansia Terlantar)
        // ---------------------------------------------------------------------
        $clientNik = '3505010101450001';
        if (! Client::where('nik', $clientNik)->exists()) {
            $lansiaCategory = ClientCategory::where('name', 'like', '%Lanjut Usia%')->first();
            $pstwInstitution = ReferralInstitution::where('type', 'panti')->first();

            $client = Client::create([
                'name' => 'Mbah Wagimin',
                'client_category_id' => $lansiaCategory->id,
                'nik' => $clientNik,
                'birth_date' => '1945-08-17',
                'gender' => 'L',
                'address' => 'Dusun Krajan, RT 01 RW 01',
                'village_id' => $kanigoroVillage->id,
                'phone' => null,
            ]);

            $rhsNumber = NumberSequence::generateNext('RHS');
            $case = RehabilitationCase::create([
                'case_number' => $rhsNumber,
                'client_id' => $client->id,
                'service_request_id' => null,
                'complaint_id' => null,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => HandlingType::Referral,
                'status' => RehabilitationCaseStatus::InService,
                'handling_result' => 'Klien telah didampingi dan diantar menuju UPT PSTW Blitar untuk perawatan lanjut.',
                'received_at' => Carbon::now()->subDays(5),
                'closed_at' => null,
            ]);

            $assessment = Assessment::create([
                'rehabilitation_case_id' => $case->id,
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(4)->toDateString(),
                'result' => 'Lansia berusia 81 tahun tidak memiliki keluarga yang mengurus. Mengalami keterbatasan mobilitas dan kebutuhan nutrisi tidak terpenuhi.',
                'service_needs' => 'Perawatan residensial jangka panjang di panti jompo (tresna werdha), pemeriksaan kesehatan rutin, bantuan pakaian dan permakanan.',
                'recommendation' => 'Rujukan ke UPT PSTW Blitar milik Dinas Sosial Provinsi Jawa Timur.',
                'needs_referral' => true,
            ]);

            $rjkNumber = NumberSequence::generateNext('RJK');
            $referral = Referral::create([
                'referral_number' => $rjkNumber,
                'rehabilitation_case_id' => $case->id,
                'assessment_id' => $assessment->id,
                'referral_institution_id' => $pstwInstitution->id,
                'officer_id' => $petugasRehsos?->id,
                'referral_date' => Carbon::now()->subDays(3)->toDateString(),
                'status' => ReferralStatus::InService,
                'service_result' => 'Klien diterima oleh pengurus panti dan menempati Wisma Dahlia kamar 2.',
                'completed_at' => null,
            ]);

            MonitoringRecord::create([
                'rehabilitation_case_id' => $case->id,
                'referral_id' => $referral->id,
                'officer_id' => $petugasRehsos?->id,
                'monitoring_date' => Carbon::now()->subDays(1)->toDateString(),
                'progress' => 'Kondisi kesehatan klien terpantau stabil, nafsu makan membaik, dan dapat bersosialisasi dengan sesama lansia di panti.',
                'result_notes' => 'Petugas panti akan menjadwalkan pemeriksaan kesehatan umum minggu depan.',
            ]);
        }

        // ---------------------------------------------------------------------
        // 4. Layanan 5: Pengaduan Sosial Masyarakat (Selesai Ditangani)
        // ---------------------------------------------------------------------
        if (! Complaint::where('reporter_name', 'Budi Santoso')->where('description', 'like', '%lansia terlantar%')->exists()) {
            $complaintCat = ComplaintCategory::where('name', 'like', '%Kedaruratan%')->first();
            $aduNumber = NumberSequence::generateNext('ADU');

            $complaint = Complaint::create([
                'complaint_number' => $aduNumber,
                'complaint_category_id' => $complaintCat->id,
                'reporter_id' => $warga?->id,
                'reporter_name' => 'Budi Santoso',
                'reporter_phone' => '081298765432',
                'location_detail' => 'Kompleks Ruko depan Pasar Kanigoro samping pos kamling',
                'village_id' => $kanigoroVillage->id,
                'description' => 'Ditemukan seorang lansia terlantar sedang sakit dan tidak dapat berjalan sendiri, tidak memiliki tempat berteduh.',
                'reported_at' => Carbon::now()->subDays(6),
                'officer_id' => $petugasRehsos?->id,
                'status' => ComplaintStatus::Resolved,
                'verification_result' => 'Laporan valid, petugas tim reaksi cepat (TRC) Dinsos telah turun ke lokasi Pasar Kanigoro.',
                'action_taken' => 'Lansia telah dievakuasi ke RSUD untuk pertolongan medis pertama dan selanjutnya dibuatkan kasus rehabilitasi sosial.',
                'duplicate_of_id' => null,
                'resolved_at' => Carbon::now()->subDays(5),
            ]);

            ComplaintAttachment::create([
                'complaint_id' => $complaint->id,
                'file_path' => 'complaints/foto-lansia-pasar.jpg',
                'type' => AttachmentType::Photo,
            ]);

            Disposition::create([
                'dispositionable_type' => Complaint::class,
                'dispositionable_id' => $complaint->id,
                'from_user_id' => $kabid?->id,
                'to_work_unit_id' => $rehsosUnit->id,
                'to_user_id' => $petugasRehsos?->id,
                'instructions' => 'Segera terjunkan tim TRC untuk evakuasi awal dan koordinasi dengan pihak desa/kelurahan setempat.',
                'disposed_at' => Carbon::now()->subDays(6)->addHours(2),
            ]);
        }
    }
}
