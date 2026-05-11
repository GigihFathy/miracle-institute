<?php

namespace App\Jobs;

use App\Events\CertificateGenerated;
use App\Models\Certificate;
use App\Services\CertificateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $certificateId;

    public string $queue = 'certificates';

    public function __construct(string $certificateId)
    {
        $this->certificateId = $certificateId;
    }

    public function handle(CertificateService $certificateService): void
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($this->certificateId);

        if ($certificate->status === 'issued' && !empty($certificate->file_path) && Storage::disk('public')->exists($certificate->file_path)) {
            return;
        }

        $path = $certificateService->pdfPathFor($certificate);

        $pdf = Pdf::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'course' => $certificate->course,
            'topic' => $certificate->topic,
            'issuedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $pdf->save($absolutePath);

        $certificate->update([
            'file_path' => $path,
            'issued_at' => now(),
            'status' => 'issued',
        ]);

        event(new CertificateGenerated($certificate->id));
    }
}