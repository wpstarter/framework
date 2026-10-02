<?php

namespace WpStarter\Filesystem;

use WpStarter\Http\Request;
use WpStarter\Support\Facades\Storage;
use League\Flysystem\PathTraversalDetected;

class ServeFile
{
    /**
     * Create a new invokable controller to serve files.
     */
    public function __construct(
        protected string $disk,
        protected array $config,
        protected bool $isProduction,
    ) {
        //
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $path)
    {
        ws_abort_unless(
            $this->hasValidSignature($request),
            $this->isProduction ? 404 : 403
        );
        try {
            ws_abort_unless(Storage::disk($this->disk)->exists($path), 404);

            $headers = [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            ];

            return ws_tap(
                Storage::disk($this->disk)->serve($request, $path, headers: $headers),
                function ($response) use ($headers) {
                    if (! $response->headers->has('Content-Security-Policy')) {
                        $response->headers->replace($headers);
                    }
                }
            );
        } catch (PathTraversalDetected $e) {
            ws_abort(404);
        }
    }

    /**
     * Determine if the request has a valid signature if applicable.
     */
    protected function hasValidSignature(Request $request): bool
    {
        return ! filter_var($request->query('upload', false), FILTER_VALIDATE_BOOLEAN) && (
            ($this->config['visibility'] ?? 'private') === 'public' ||
            $request->hasValidRelativeSignature()
        );
    }
}
