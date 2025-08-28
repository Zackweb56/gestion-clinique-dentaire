<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Acte;
use App\Models\MedicalFile;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf; // Add at the top

class Invoices extends Component
{
    public $search = '';
    public $perPage = 5;
    public $statusFilter = '';
    public $invoiceId;
    public $acte_id;
    public $base_amount;
    public $tva_rate = 0.2;
    public $tva_amount;
    public $total_amount;
    public $paid_amount;
    public $status;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $isDownloadingPdf = false;

    protected $rules = [
        'acte_id' => 'required|exists:actes,id',
        'base_amount' => 'required|numeric|min:0',
        'tva_rate' => 'required|numeric|min:0',
        'tva_amount' => 'required|numeric|min:0',
        'total_amount' => 'required|numeric|min:0',
        'paid_amount' => 'required|numeric|min:0',
        'status' => 'required|string',
    ];

    public function render()
    {
        $invoices = Invoice::with(['acte.medicalFile.patient'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('acte.medicalFile.patient', function ($q2) {
                        $q2->where('patient_full_name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('acte', function ($q2) {
                        $q2->where('acte_number', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhere('base_amount', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.invoices.invoices', [
            'invoices' => $invoices,
        ]);
    }

    public function resetForm() { 
        $this->reset(['invoiceId','acte_id','base_amount','tva_rate','tva_amount','total_amount','paid_amount','status']); 
    }

    public function downloadPdf($id)
    {
        $this->isDownloadingPdf = true;
        $invoice = Invoice::with(['acte.medicalFile.patient'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice_theme', [
            'invoice' => $invoice,
        ]);
        $filename = $invoice->invoice_number . '.pdf';
        $this->isDownloadingPdf = false;
        return response()->streamDownload(
            fn () => print($pdf->stream()),
            $filename
        );
    }

    public function openPaymentModal($invoiceId)
    {
        return redirect()->route('payements', ['invoice_id' => $invoiceId]);
    }
}