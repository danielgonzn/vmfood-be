<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryReportExportController extends Controller
{
    public function csv(): StreamedResponse
    {
        $fileName = 'reporte-consultas-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, $this->headers());

            $this->rows()->each(function (array $row) use ($handle): void {
                fputcsv($handle, $row);
            });

            fclose($handle);
        }, $fileName, $headers);
    }

    public function xlsx(): JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $rows = $this->rows();

        if ($rows->isEmpty()) {
            return response()->json([
                'message' => 'No hay consultas para exportar.',
            ], 422);
        }

        $fileName = 'reporte-consultas-'.now()->format('Ymd-His').'.xlsx';
        $tempPath = storage_path('app/private/'.$fileName);

        $writer = new XlsxWriter();
        $writer->openToFile($tempPath);
        $writer->addRow(Row::fromValues($this->headers()));

        $rows->each(function (array $row) use ($writer): void {
            $writer->addRow(Row::fromValues($row));
        });

        $writer->close();

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * @return array<int, string>
     */
    private function headers(): array
    {
        return [
            'ID',
            'Fecha',
            'Estado',
            'Nombre',
            'Empresa',
            'Correo',
            'Telefono',
            'Producto',
            'Mensaje',
        ];
    }

    /**
     * @return Collection<int, array<int, string>>
     */
    private function rows(): Collection
    {
        return Inquiry::query()
            ->with('product:id,title')
            ->latest('created_at')
            ->get()
            ->map(function (Inquiry $inquiry): array {
                return [
                    (string) $inquiry->id,
                    $inquiry->created_at?->format('d/m/Y H:i') ?? '',
                    match ($inquiry->status) {
                        'new' => 'Nueva',
                        'contacted' => 'Contactada',
                        'closed' => 'Cerrada',
                        default => $inquiry->status,
                    },
                    (string) $inquiry->name,
                    (string) ($inquiry->company ?? ''),
                    (string) $inquiry->email,
                    (string) $inquiry->phone,
                    (string) ($inquiry->product?->title ?? 'General'),
                    (string) $inquiry->message,
                ];
            });
    }
}
