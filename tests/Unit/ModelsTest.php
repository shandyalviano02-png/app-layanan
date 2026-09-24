<?php

namespace Tests\Unit;

use App\Enums\ComplaintStatus;
use App\Enums\HandlingType;
use App\Enums\PbiReason;
use App\Enums\PublishStatus;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceHandler;
use App\Enums\ServiceRequestStatus;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\District;
use App\Models\DownloadableForm;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\PageVisit;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\SearchLog;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that all created models can be instantiated and persisted.
     */
    public function test_all_models_can_be_instantiated(): void
    {
        $workUnit = WorkUnit::create(['name' => 'Bidang Perlindungan dan Jaminan Sosial']);
        $this->assertDatabaseHas('work_units', ['name' => 'Bidang Perlindungan dan Jaminan Sosial']);

        $district = District::create(['code' => '35.05.01', 'name' => 'Kanigoro']);
        $village = Village::create(['district_id' => $district->id, 'code' => '35.05.01.2001', 'name' => 'Kanigoro']);
        $this->assertDatabaseHas('villages', ['name' => 'Kanigoro']);

        $user = User::create([
            'name' => 'Petugas Pelayanan',
            'email' => 'petugas@dinsos.blitarkab.go.id',
            'password' => 'secret123',
            'phone' => '081234567890',
            'nik' => '3505010101900001',
            'work_unit_id' => $workUnit->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ]);
        $this->assertDatabaseHas('users', ['email' => 'petugas@dinsos.blitarkab.go.id']);

        $serviceType = ServiceType::create([
            'code' => 'DTSEN',
            'name' => 'Surat Keterangan DTSEN',
            'category' => 'Bantuan Sosial',
            'handler' => ServiceHandler::Dtsen,
            'sla_days' => 1,
        ]);
        $this->assertEquals(ServiceHandler::Dtsen, $serviceType->handler);

        $req = ServiceRequirement::create([
            'service_type_id' => $serviceType->id,
            'name' => 'KTP & KK Pemohon',
            'is_mandatory' => true,
        ]);
        $this->assertTrue($req->is_mandatory);

        $ticket = NumberSequence::generateNext('DTSEN');
        $this->assertStringStartsWith('DTSEN-', $ticket);

        $serviceRequest = ServiceRequest::create([
            'request_number' => $ticket,
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Budi Santoso',
            'applicant_nik' => '3505010101900002',
            'family_card_number' => '3505010101900001',
            'address' => 'Jl. Merdeka No. 1',
            'village_id' => $village->id,
            'phone' => '081298765432',
            'submitted_at' => now(),
            'status' => ServiceRequestStatus::Submitted,
        ]);
        $this->assertEquals(ServiceRequestStatus::Submitted, $serviceRequest->status);

        $doc = ServiceRequestDocument::create([
            'service_request_id' => $serviceRequest->id,
            'service_requirement_id' => $req->id,
            'file_path' => 'documents/ktp.pdf',
            'original_name' => 'ktp.pdf',
        ]);
        $this->assertDatabaseHas('service_request_documents', ['id' => $doc->id]);

        $purpose = DtsenPurpose::create([
            'code' => 'spmb',
            'name' => 'SPMB Jalur Afirmasi',
            'max_decile' => 5,
        ]);
        $this->assertDatabaseHas('dtsen_purposes', ['code' => 'spmb']);

        $dtsenCert = DtsenCertificate::create([
            'service_request_id' => $serviceRequest->id,
            'dtsen_purpose_id' => $purpose->id,
            'subject_name' => 'Anak Budi',
            'subject_nik' => '3505010101150001',
            'relationship_to_applicant' => 'Anak Kandung',
            'verification_code' => 'V-TEST123',
        ]);
        $this->assertDatabaseHas('dtsen_certificates', ['verification_code' => 'V-TEST123']);

        $pbiTicket = NumberSequence::generateNext('PBI');
        $pbiRequest = ServiceRequest::create([
            'request_number' => $pbiTicket,
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Siti Aminah',
            'applicant_nik' => '3505010101900003',
            'family_card_number' => '3505010101900003',
            'address' => 'Jl. Sudirman No. 5',
            'village_id' => $village->id,
            'phone' => '081298765433',
            'submitted_at' => now(),
            'status' => ServiceRequestStatus::Submitted,
        ]);

        $pbi = PbiReactivation::create([
            'service_request_id' => $pbiRequest->id,
            'participant_name' => 'Siti Aminah',
            'participant_nik' => '3505010101900003',
            'bpjs_card_number' => '0001234567890',
            'reason' => PbiReason::Emergency,
        ]);
        $this->assertEquals(PbiReason::Emergency, $pbi->reason);

        $clientCat = ClientCategory::create(['name' => 'Lanjut Usia Terlantar']);
        $client = Client::create([
            'name' => 'Mbah Rejo',
            'client_category_id' => $clientCat->id,
            'gender' => 'L',
            'address' => 'Dusun Krajan',
            'village_id' => $village->id,
        ]);
        $this->assertDatabaseHas('clients', ['name' => 'Mbah Rejo']);

        $caseNumber = NumberSequence::generateNext('RHS');
        $rehabCase = RehabilitationCase::create([
            'case_number' => $caseNumber,
            'client_id' => $client->id,
            'officer_id' => $user->id,
            'handling_type' => HandlingType::Direct,
            'status' => RehabilitationCaseStatus::Received,
            'received_at' => now(),
        ]);
        $this->assertEquals(RehabilitationCaseStatus::Received, $rehabCase->status);

        $assessment = Assessment::create([
            'rehabilitation_case_id' => $rehabCase->id,
            'officer_id' => $user->id,
            'assessment_date' => now()->toDateString(),
            'result' => 'Kondisi fisik lemah butuh perawatan',
            'service_needs' => 'Perawatan panti lansia',
            'recommendation' => 'Rujukan ke panti',
            'needs_referral' => true,
        ]);
        $this->assertTrue($assessment->needs_referral);

        $institution = ReferralInstitution::create([
            'name' => 'Panti Werdha Blitar',
            'type' => 'panti',
        ]);
        $this->assertDatabaseHas('referral_institutions', ['name' => 'Panti Werdha Blitar']);

        $refNumber = NumberSequence::generateNext('RJK');
        $referral = Referral::create([
            'referral_number' => $refNumber,
            'rehabilitation_case_id' => $rehabCase->id,
            'assessment_id' => $assessment->id,
            'referral_institution_id' => $institution->id,
            'officer_id' => $user->id,
            'referral_date' => now()->toDateString(),
            'status' => ReferralStatus::Draft,
        ]);
        $this->assertEquals(ReferralStatus::Draft, $referral->status);

        $monitoring = MonitoringRecord::create([
            'rehabilitation_case_id' => $rehabCase->id,
            'referral_id' => $referral->id,
            'officer_id' => $user->id,
            'monitoring_date' => now()->toDateString(),
            'progress' => 'Klien telah diterima dengan baik di panti',
        ]);
        $this->assertDatabaseHas('monitoring_records', ['id' => $monitoring->id]);

        $complaintCat = ComplaintCategory::create(['name' => 'Orang Terlantar']);
        $complaintNumber = NumberSequence::generateNext('ADU');
        $complaint = Complaint::create([
            'complaint_number' => $complaintNumber,
            'complaint_category_id' => $complaintCat->id,
            'reporter_name' => 'Agus',
            'reporter_phone' => '081234567811',
            'location_detail' => 'Depan pasar Kanigoro',
            'village_id' => $village->id,
            'description' => 'Ada lansia terlantar tidak bisa jalan',
            'reported_at' => now(),
            'status' => ComplaintStatus::Received,
        ]);
        $this->assertEquals(ComplaintStatus::Received, $complaint->status);

        $attachment = ComplaintAttachment::create([
            'complaint_id' => $complaint->id,
            'file_path' => 'complaints/foto.jpg',
            'type' => 'photo',
        ]);
        $this->assertDatabaseHas('complaint_attachments', ['id' => $attachment->id]);

        $infoPage = InformationPage::create([
            'title' => 'Panduan Pengajuan DTSEN',
            'slug' => 'panduan-dtsen',
            'category' => 'program',
            'publish_status' => PublishStatus::Published,
            'published_at' => now(),
            'manager_id' => $user->id,
        ]);
        $this->assertEquals(PublishStatus::Published, $infoPage->publish_status);

        $form = DownloadableForm::create([
            'information_page_id' => $infoPage->id,
            'name' => 'Formulir Pernyataan',
            'file_path' => 'forms/form-dtsen.pdf',
            'version' => '1.0',
        ]);
        $this->assertDatabaseHas('downloadable_forms', ['id' => $form->id]);

        $faq = Faq::create([
            'information_page_id' => $infoPage->id,
            'question' => 'Berapa lama proses pembuatan surat?',
            'answer' => 'Maksimal 1 hari kerja',
        ]);
        $this->assertDatabaseHas('faqs', ['id' => $faq->id]);

        $visit = PageVisit::create([
            'information_page_id' => $infoPage->id,
            'visit_date' => now()->toDateString(),
            'visit_count' => 10,
        ]);
        $this->assertDatabaseHas('page_visits', ['id' => $visit->id]);

        $searchLog = SearchLog::create([
            'keyword' => 'DTSEN SPMB',
            'result_count' => 3,
            'searched_at' => now(),
        ]);
        $this->assertDatabaseHas('search_logs', ['keyword' => 'DTSEN SPMB']);

        $statusHistory = StatusHistory::create([
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequest->id,
            'from_status' => null,
            'to_status' => ServiceRequestStatus::Submitted->value,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('status_histories', ['id' => $statusHistory->id]);

        $disposition = Disposition::create([
            'dispositionable_type' => ServiceRequest::class,
            'dispositionable_id' => $serviceRequest->id,
            'from_user_id' => $user->id,
            'to_work_unit_id' => $workUnit->id,
            'disposed_at' => now(),
        ]);
        $this->assertDatabaseHas('dispositions', ['id' => $disposition->id]);
    }
}
