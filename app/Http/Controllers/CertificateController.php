<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function claimCourse(string $locale, Course $course, CertificateService $service)
    {
        abort_unless(auth()->check(), 401);

        try {
            $certificate = $service->issueCourseCertificate($course, auth()->user());

            return redirect()->to(localized_route('certificates.download', $certificate->id));
            
        } catch (\RuntimeException $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    public function download(string $locale, Certificate $certificate, CertificateService $service)
    {
        abort_unless(auth()->check(), 401);

        abort_unless(
            auth()->id() == $certificate->user_id || auth()->user()->can('manage_certificates'),
            403
        );

        abort_unless($certificate->status === 'issued', 404);

        $filename = Str::slug($certificate->certificate_number) . '.pdf';

        return $service->downloadCourseCertificate($certificate, $filename);
    }
}