<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateSignatory;
use App\Models\Course;
use App\Services\CertificateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function claimCourse(Request $request, CertificateService $service)
    {
        $courseId = (string) $request->route('courseId', '');
        $course = Course::query()
            ->whereKey($courseId)
            ->first();

        abort_unless($course instanceof Course, 404);

        abort_unless(auth()->check(), 401);

        try {
            $certificate = $service->issueCourseCertificate($course, auth()->user());

            return redirect()->to(localized_route('certificates.download', $certificate->id));
            
        } catch (\RuntimeException $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    public function download(Request $request, CertificateService $service)
    {
        $certificateId = (string) $request->route('certificateId', '');
        $certificate = Certificate::query()
            ->whereKey($certificateId)
            ->first();

        abort_unless($certificate instanceof Certificate, 404);

        abort_unless(auth()->check(), 401);

        abort_unless(
            auth()->id() == $certificate->user_id || auth()->user()->can('manage_certificates'),
            403
        );

        abort_unless($certificate->status === 'issued', 404);

        $filename = Str::slug($certificate->certificate_number) . '.pdf';

        return $service->downloadCourseCertificate($certificate, $filename);
    }

    public function previewSignatories()
    {
        abort_unless(auth()->check() && auth()->user()->can('manage_certificates'), 403);

        $fontPath = storage_path('fonts');
        if (!is_dir($fontPath)) {
            mkdir($fontPath, 0755, true);
        }

        $issuedAt = now();

        $dbSignatories = CertificateSignatory::activeAt(Carbon::instance($issuedAt));
        $signatures = $dbSignatories->isNotEmpty()
            ? $dbSignatories->map(fn ($s) => [
                'name'  => $s->name,
                'title' => $s->title,
                'image' => $s->signatureDataUri(),
            ])->all()
            : null;

        $course = (object) ['title' => '[Preview] Kursus Contoh'];
        $user   = (object) ['full_name' => 'Nama Peserta Contoh', 'name' => 'Nama Peserta Contoh'];

        $pdf = Pdf::loadView('pdf.certificates.course', [
            'certificateNumber'  => 'PREVIEW/000/MI/06/2026',
            'certificate'        => null,
            'course'             => $course,
            'user'               => $user,
            'issuedAt'           => $issuedAt,
            'frontDate'          => $issuedAt->locale('id')->isoFormat('D MMMM Y'),
            'sequenceLabel'      => '00000',
            'frontSummary'       => [],
            'backTopics'         => [
                ['topic_name' => 'Topik Contoh 1', 'topic_status' => 'Present'],
                ['topic_name' => 'Topik Contoh 2', 'topic_status' => 'Online'],
            ],
            'achievementSummary' => [
                'assessment_score'  => null,
                'assessment_passed' => false,
            ],
            'background'     => null,
            'backgroundBack' => null,
            'signatures'     => $signatures,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('preview-sertifikat.pdf');
    }
}
