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
        $suffix = (string) mt_rand(1000, 9999);

        $workUnit = WorkUnit::create(['name' => 'Unit Uji '.$suffix]);
        $this->assertDatabaseHas('work_units', ['id' => $workUnit->id]);

        $district = District::create(['code' => '99.'.$suffix, 'name' => 'Kecamatan Uji '.$suffix]);
        $village = Village::create(['district_id' => $district->id, 'code' => '99.'.$suffix.'.01', 'name' => 'Desa Uji '.$suffix]);
        $this->assertDatabaseHas('villages', ['id' => $village->id]);

        $user = User::create([
            'name' => 'Petugas Uji '.$suffix,
            'email' => 'petugas_'.$suffix.'@test.com',
            'password' => 'secret123',
            'phone' => '08129999'.$suffix,
            'nik' => '350599998888'.$suffix,
            'work_unit_id' => $workUnit->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $serviceType = ServiceType::create([
            'code' => 'TST-'.$suffix,
            'name' => 'Layanan Uji '.$suffix,
            'category' => 'Bantuan Sosial',
            'handler' => ServiceHandler::Dtsen,
            'sla_days' => 1,
        ]);
        $this->assertEquals(ServiceHandler::Dtsen, $serviceType->handler);

        $req = ServiceRequirement::create([
            'service_type_id' => $serviceType->id,
            'name' => 'Berkas Uji '.$suffix,
            'is_mandatory' => true,
        ]);
        $this->assertTrue($req->is_mandatory);

        $ticket = NumberSequence::generateNext('TST-'.$suffix);
        $this->assertStringStartsWith('TST-', $ticket);

        $serviceRequest = ServiceRequest::create([
            'request_number' => $ticket,
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Warga Uji',
            'applicant_nik' => '3505010101900002',
            'family_card_number' => '3505010101900001',
            'address' => 'Jl. Uji No. 1',
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
            'code' => 'purp_'.$suffix,
            'name' => 'Tujuan Uji '.$suffix,
            'max_decile' => 5,
        ]);
        $this->assertDatabaseHas('dtsen_purposes', ['id' => $purpose->id]);

        $dtsenCert = DtsenCertificate::create([
            'service_request_id' => $serviceRequest->id,
            'dtsen_purpose_id' => $purpose->id,
            'subject_name' => 'Anak Uji',
            'subject_nik' => '3505010101150001',
            'relationship_to_applicant' => 'Anak Kandung',
            'verification_code' => 'V-'.$suffix,
        ]);
        $this->assertDatabaseHas('dtsen_certificates', ['verification_code' => 'V-'.$suffix]);

        $pbiTicket = NumberSequence::generateNext('PBI-'.$suffix);
        $pbiRequest = ServiceRequest::create([
            'request_number' => $pbiTicket,
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Warga PBI Uji',
            'applicant_nik' => '3505010101900003',
            'family_card_number' => '3505010101900003',
            'address' => 'Jl. Uji PBI No. 5',
            'village_id' => $village->id,
            'phone' => '081298765433',
            'submitted_at' => now(),
            'status' => ServiceRequestStatus::Submitted,
        ]);

        $pbi = PbiReactivation::create([
            'service_request_id' => $pbiRequest->id,
            'participant_name' => 'Warga PBI Uji',
            'participant_nik' => '3505010101900003',
            'bpjs_card_number' => '0001234567890',
            'reason' => PbiReason::Emergency,
        ]);
        $this->assertEquals(PbiReason::Emergency, $pbi->reason);

        $clientCat = ClientCategory::create(['name' => 'Kategori PPKS Uji '.$suffix]);
        $client = Client::create([
            'name' => 'Klien Uji '.$suffix,
            'client_category_id' => $clientCat->id,
            'gender' => 'L',
            'address' => 'Dusun Krajan',
            'village_id' => $village->id,
        ]);
        $this->assertDatabaseHas('clients', ['id' => $client->id]);

        $caseNumber = NumberSequence::generateNext('RHS-'.$suffix);
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
            'result' => 'Kondisi fisik butuh perawatan',
            'service_needs' => 'Perawatan panti lansia',
            'recommendation' => 'Rujukan ke panti',
            'needs_referral' => true,
        ]);
        $this->assertTrue($assessment->needs_referral);

        $institution = ReferralInstitution::create([
            'name' => 'Panti Uji '.$suffix,
            'type' => 'panti',
        ]);
        $this->assertDatabaseHas('referral_institutions', ['id' => $institution->id]);

        $refNumber = NumberSequence::generateNext('RJK-'.$suffix);
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
            'progress' => 'Klien diterima baik di panti',
        ]);
        $this->assertDatabaseHas('monitoring_records', ['id' => $monitoring->id]);

        $complaintCat = ComplaintCategory::create(['name' => 'Aduan Uji '.$suffix]);
        $complaintNumber = NumberSequence::generateNext('ADU-'.$suffix);
        $complaint = Complaint::create([
            'complaint_number' => $complaintNumber,
            'complaint_category_id' => $complaintCat->id,
            'reporter_name' => 'Pelapor Uji',
            'reporter_phone' => '081234567811',
            'location_detail' => 'Lokasi Uji',
            'village_id' => $village->id,
            'description' => 'Deskripsi aduan uji',
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
            'title' => 'Panduan Uji '.$suffix,
            'slug' => 'panduan-uji-'.$suffix,
            'category' => 'program',
            'publish_status' => PublishStatus::Published,
            'published_at' => now(),
            'manager_id' => $user->id,
        ]);
        $this->assertEquals(PublishStatus::Published, $infoPage->publish_status);

        $form = DownloadableForm::create([
            'information_page_id' => $infoPage->id,
            'name' => 'Formulir Uji',
            'file_path' => 'forms/form-uji.pdf',
            'version' => '1.0',
        ]);
        $this->assertDatabaseHas('downloadable_forms', ['id' => $form->id]);

        $faq = Faq::create([
            'information_page_id' => $infoPage->id,
            'question' => 'Pertanyaan uji?',
            'answer' => 'Jawaban uji.',
        ]);
        $this->assertDatabaseHas('faqs', ['id' => $faq->id]);

        $visit = PageVisit::create([
            'information_page_id' => $infoPage->id,
            'visit_date' => now()->toDateString(),
            'visit_count' => 10,
        ]);
        $this->assertDatabaseHas('page_visits', ['id' => $visit->id]);

        $searchLog = SearchLog::create([
            'keyword' => 'Uji '.$suffix,
            'result_count' => 3,
            'searched_at' => now(),
        ]);
        $this->assertDatabaseHas('search_logs', ['id' => $searchLog->id]);

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
