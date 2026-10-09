<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelper;
use App\Services\Exports\BackgroundExportLauncher;
use App\Services\Exports\ExportProgress;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackgroundExportController extends Controller
{
    public function index()
    {
        $this->denyUnlessAllowed();

        $exports = [];
        foreach ($this->definitions() as $key => $definition) {
            if (! $definition['allowed']) {
                continue;
            }

            try {
                $status = ExportProgress::present($key);
            } catch (\Throwable $exception) {
                $status = [
                    'status' => 'failed',
                    'percent' => 0,
                    'processed' => 0,
                    'total' => 0,
                    'message' => 'Could not read this export.',
                    'error' => $exception->getMessage(),
                    'file_ready' => is_file(ExportProgress::filePath($key)),
                    'file_size_label' => '',
                    'finished_at' => null,
                ];
            }

            $exports[$key] = array_merge($definition, [
                'status' => $status,
            ]);
        }

        return view('admin.reports.background-exports', compact('exports'));
    }

    public function status(string $key): JsonResponse
    {
        $this->denyUnlessAllowed($key);

        return response()->json(ExportProgress::present($key));
    }

    public function start(string $key): JsonResponse
    {
        $this->denyUnlessAllowed($key);

        return response()->json(BackgroundExportLauncher::start($key));
    }

    public function download(string $key): BinaryFileResponse
    {
        $this->denyUnlessAllowed($key);

        $path = ExportProgress::filePath($key);
        if (is_file($path)) {
            @chmod($path, 0644);
        }

        abort_unless(is_file($path) && is_readable($path), 404, 'The Excel file is not ready yet.');

        return response()->download($path, ExportProgress::downloadName($key));
    }

    private function denyUnlessAllowed(?string $key = null): void
    {
        if ($key !== null && ! ExportProgress::isValidKey($key)) {
            abort(404);
        }

        if ($key === null) {
            if (! $this->canExportLeads() && ! $this->canExportCalls()) {
                abort(403, 'Access denied.');
            }

            return;
        }

        if ($key === ExportProgress::UNCONVERTED_LEADS && ! $this->canExportLeads()) {
            abort(403, 'Access denied.');
        }

        if (in_array($key, [ExportProgress::TELECALLER_WISE, ExportProgress::CALL_LOGS], true) && ! $this->canExportCalls()) {
            abort(403, 'Access denied.');
        }
    }

    private function canExportLeads(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_general_manager()
            || RoleHelper::is_senior_manager()
            || RoleHelper::is_auditor();
    }

    private function canExportCalls(): bool
    {
        return has_permission('admin/call-analytics/index');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function definitions(): array
    {
        return [
            ExportProgress::UNCONVERTED_LEADS => [
                'title' => 'Unconverted Leads',
                'description' => 'Every lead with is_converted = 0, from the start of the project through today. Same fields as the leads list: name, phone, status, source, course, telecaller, follow-up, remarks, and registration.',
                'command' => 'php artisan exports:unconverted-leads',
                'allowed' => $this->canExportLeads(),
            ],
            ExportProgress::TELECALLER_WISE => [
                'title' => 'Telecaller-wise Call Report',
                'description' => 'The telecaller-wise summary from Call Analytics, covering every call log from the start of the project through today.',
                'command' => 'php artisan exports:telecaller-wise-report',
                'allowed' => $this->canExportCalls(),
            ],
            ExportProgress::CALL_LOGS => [
                'title' => 'Call Logs',
                'description' => 'Every call log from 2024-01-01 through today, the same file as the long Call Analytics export. Large ranges run here instead of in the browser.',
                'command' => 'php artisan exports:call-logs --start=2024-01-01 --end='.now()->format('Y-m-d'),
                'allowed' => $this->canExportCalls(),
            ],
        ];
    }
}
