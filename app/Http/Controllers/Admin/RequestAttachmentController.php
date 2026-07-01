<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request as BuzonRequest;
use App\Models\RequestAttachment;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestAttachmentController extends Controller
{
    public function download(HttpRequest $httpRequest, BuzonRequest $solicitud, RequestAttachment $adjunto): StreamedResponse
    {
        abort_unless($adjunto->request_id === $solicitud->id, 404);
        abort_unless(Storage::disk('local')->exists($adjunto->file_path), 404);

        if ($httpRequest->boolean('preview') && $adjunto->isImage()) {
            return Storage::disk('local')->response($adjunto->file_path, $adjunto->original_name);
        }

        return Storage::disk('local')->download($adjunto->file_path, $adjunto->original_name);
    }
}
