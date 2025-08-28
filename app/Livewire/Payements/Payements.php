<?php

namespace App\Livewire\Payements;

use Flux\Flux;
use App\Models\Acte;
use App\Models\Invoice;
use App\Models\Service;
use Livewire\Component;
use App\Models\Payement;
use App\Models\MedicalFile;
use App\Helpers\FlashHelper;
use App\Models\Consultation;
use App\Models\DentalProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class Payements extends Component
{
    use WithPagination;

    public $payementId;
    public $invoice_id;
    public $amount;
    public $payment_method;
    public $paid_at;
    public $notes;

    public $details_invoice_number;
    public $details_amount;
    public $details_payment_method;
    public $details_paid_at;
    public $details_notes;
    public $details_created_by;

    public $filter_payment_method = '';
    public $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $isCreating = false;

    protected $rules = [
        'invoice_id' => 'required|exists:invoices,id',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|in:espece,carte_bancaire,cheque,virement',
        'paid_at' => 'required|date',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'invoice_id.required' => 'La facture est requise.',
        'invoice_id.exists' => 'La facture sélectionnée est invalide.',
        'amount.required' => 'Le montant est requis.',
        'amount.numeric' => 'Le montant doit être un nombre.',
        'amount.min' => 'Le montant doit être supérieur à 0.',
        'payment_method.required' => 'La méthode de paiement est requise.',
        'payment_method.in' => 'La méthode de paiement sélectionnée est invalide.',
        'paid_at.required' => 'La date de paiement est requise.',
        'paid_at.date' => 'La date de paiement doit être une date valide.',
        'notes.string' => 'Les notes doivent être une chaîne de caractères.',
    ];

    public function getSelectedInvoiceProperty()
    {
        if ($this->invoice_id) {
            return Invoice::find($this->invoice_id);
        }
        return null;
    }

    public function mount()
    {
        // Check if invoice_id is passed in URL
        if (request()->has('invoice_id')) {
            $this->invoice_id = request()->get('invoice_id');
            $this->prepareCreate();
        }
        
        // $this->loadPayements(); // Removed as per edit hint
    }

    // Removed loadPayements()

    public function closeModal($modalId)
    {
        if ($modalId === 'create-payement-modal') {
            $this->showCreateModal = false;
        } elseif ($modalId === 'edit-payement-modal') {
            $this->showEditModal = false;
        } elseif ($modalId === 'delete-payement-modal') {
            $this->showDeleteModal = false;
        }
        
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['payementId', 'invoice_id', 'amount', 'payment_method', 'paid_at', 'notes']);
    }

    public function prepareCreate()
    {
        $this->isCreating = true;
        $this->resetForm();
        
        // Keep the invoice_id if it was passed
        if (request()->has('invoice_id') && !$this->invoice_id) {
            $this->invoice_id = request()->get('invoice_id');
        }
        
        $this->showCreateModal = true;
        $this->showEditModal = false;
        $this->showDeleteModal = false;

        $this->paid_at = now()->setTimezone('Africa/Casablanca')->format('Y-m-d\TH:i');
        // $this->loadPayements(); // Removed as per edit hint
        $this->isCreating = false;
    }

    public function openPaymentModal($invoiceId)
    {
        $this->invoice_id = $invoiceId;
        $this->prepareCreate();
    }

    #[On('openPaymentModal')]
    public function handleOpenPaymentModal($invoiceId)
    {
        $this->invoice_id = $invoiceId;
        $this->prepareCreate();
    }

    public function store()
    {
        $this->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:espece,carte_bancaire,cheque,virement',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get the invoice to check remaining amount
        $invoice = Invoice::find($this->invoice_id);
        $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
        
        // Check if payment amount exceeds remaining amount
        if ($this->amount > $remainingAmount) {
            $this->addError('amount', "Le montant du paiement ({$this->amount} MAD) ne peut pas dépasser le montant restant ({$remainingAmount} MAD) sur cette facture.");
            return;
        }

        try {
            DB::beginTransaction();

            // Create the payment
            $payement = Payement::create([
                'invoice_id' => $this->invoice_id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'paid_at' => $this->paid_at,
                'notes' => $this->notes ?? '',
                'created_by' => Auth::id(),
            ]);

            // Update invoice paid amount
            $invoice->paid_amount += $this->amount;
            
            // Update invoice status based on payment progress
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'payé';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partiel';
            } else {
                $invoice->status = 'impayé';
            }
            
            $invoice->save();

            // Update related acte status
            $acte = $invoice->acte;
            if ($acte) {
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $acte->status = 'payé';
                } elseif ($invoice->paid_amount > 0) {
                    $acte->status = 'partiel';
                } else {
                    $acte->status = 'impayé';
                }
                $acte->save();
            }

            DB::commit();

            $this->resetForm();
            $this->closeModal('create-payement-modal');
            // $this->loadPayements(); // Removed as per edit hint
            
            FlashHelper::success('Paiement créé avec succès.');
            
            // Dispatch event to refresh the component
            $this->dispatch('payement-created');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Erreur lors de la création du paiement.');
        }
    }

    public function edit($id)
    {
        $payement = Payement::findOrFail($id);
        $this->payementId = $payement->id;
        $this->invoice_id = $payement->invoice_id;
        $this->amount = $payement->amount;
        $this->payment_method = $payement->payment_method;
        $this->paid_at = $payement->paid_at ? Carbon::parse($payement->paid_at)->setTimezone('Africa/Casablanca')->format('Y-m-d\\TH:i') : '';
        $this->notes = $payement->notes;

        $this->showCreateModal = false;
        $this->showEditModal = true;
        $this->showDeleteModal = false;
    }

    public function update()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:espece,carte_bancaire,cheque,virement',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $payement = Payement::findOrFail($this->payementId);
        $invoice = Invoice::findOrFail($this->invoice_id);
        $oldAmount = $payement->amount;
        $newAmount = $this->amount;
        $paidAmountBefore = $invoice->paid_amount;
        $paidAmountAfter = $paidAmountBefore - $oldAmount + $newAmount;
        $remainingAmount = $invoice->total_amount - $paidAmountAfter;

        // Prevent overpayment
        if ($paidAmountAfter > $invoice->total_amount) {
            $this->addError('amount', "Le montant du paiement ({$newAmount} MAD) ne peut pas dépasser le montant restant (" . number_format($invoice->total_amount - ($paidAmountBefore - $oldAmount), 2) . " MAD) sur cette facture.");
            return;
        }

        try {
            DB::beginTransaction();

            // Update payment
            $payement->update([
                'amount' => $newAmount,
                'payment_method' => $this->payment_method,
                'paid_at' => $this->paid_at,
                'notes' => $this->notes ?? '',
            ]);

            // Update invoice paid amount
            $invoice->paid_amount = $paidAmountAfter;
            // Update invoice status
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'payé';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partiel';
            } else {
                $invoice->status = 'impayé';
            }
            $invoice->save();

            // Update acte status
            $acte = $invoice->acte;
            if ($acte) {
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $acte->status = 'payé';
                } elseif ($invoice->paid_amount > 0) {
                    $acte->status = 'partiel';
                } else {
                    $acte->status = 'impayé';
                }
                $acte->save();
            }

            DB::commit();

            $this->resetForm();
            $this->closeModal('edit-payement-modal');
            FlashHelper::success('Paiement mis à jour avec succès.');
            $this->dispatch('payement-updated');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Erreur lors de la mise à jour du paiement.');
        }
    }

    public function confirmDelete($id)
    {
        $this->payementId = $id;
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $payement = Payement::findOrFail($this->payementId);
        $deletedAmount = $payement->amount;
        
        // Get the invoice and its related acte
        $invoice = Invoice::findOrFail($payement->invoice_id);
        $acte = $invoice->acte;

        // Calculate new payment progress after deletion
        $currentPaidAmount = $invoice->paid_amount ?? 0;
        $newPaidAmount = $currentPaidAmount - $deletedAmount;
        $totalAmount = $invoice->total_amount;
        $remainingAmount = $totalAmount - $newPaidAmount;

        // Determine invoice status based on payment progress
        $invoiceStatus = 'impayé';
        if ($newPaidAmount >= $totalAmount) {
            $invoiceStatus = 'payé';
        } elseif ($newPaidAmount > 0) {
            $invoiceStatus = 'partiel';
        }

        // Determine acte status (similar logic)
        $acteStatus = 'impayé';
        if ($newPaidAmount >= $totalAmount) {
            $acteStatus = 'payé';
        } elseif ($newPaidAmount > 0) {
            $acteStatus = 'partiel';
        }

        // Delete the payment
        $payement->delete();

        // Update invoice with new payment information
        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'status' => $invoiceStatus,
        ]);

        // Update acte status if it exists
        if ($acte) {
            $acte->update([
                'status' => $acteStatus,
            ]);
        }

        $this->showDeleteModal = false;
        $this->resetForm();
        // $this->loadPayements(); // Removed as per edit hint
        
        // Show success message with payment details
        $message = "Paiement supprimé avec succès.";
        if ($remainingAmount > 0) {
            $message .= " Reste à payer: " . number_format($remainingAmount, 2) . " MAD.";
        } else {
            $message .= " Facture entièrement payée.";
        }
        
        FlashHelper::success($message);
        
        // Dispatch event to refresh the component
        $this->dispatch('payement-deleted');
    }

    #[On('payement-created')]
    public function handlePayementCreated()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        // $this->loadPayements(); // Removed as per edit hint
    }

    #[On('payement-updated')]
    public function handlePayementUpdated()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        // $this->loadPayements(); // Removed as per edit hint
    }

    #[On('payement-deleted')]
    public function handlePayementDeleted()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        // $this->loadPayements(); // Removed as per edit hint
    }

    public function render()
    {
        $payementsQuery = Payement::with(['invoice', 'user'])
            ->orderByDesc('id');

        if ($this->filter_payment_method) {
            $payementsQuery->where('payment_method', $this->filter_payment_method);
        }

        if ($this->search) {
            $search = $this->search;
            $payementsQuery->where(function($query) use ($search) {
                $query->whereHas('invoice', function($q) use ($search) {
                    $q->where('invoice_number', 'like', "%$search%")
                      ->orWhere('total_amount', 'like', "%$search%")
                      ->orWhere('paid_amount', 'like', "%$search%")
                      ->orWhere('status', 'like', "%$search%")
                      ;
                })
                ->orWhere('amount', 'like', "%$search%")
                ->orWhere('payment_method', 'like', "%$search%")
                ->orWhere('notes', 'like', "%$search%")
                ->orWhereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ;
                });
            });
        }

        $payements = $payementsQuery->get()->map(function ($payement) {
            return [
                'id' => $payement->id,
                'invoice_number' => $payement->invoice ? $payement->invoice->invoice_number : 'N/A',
                'amount' => $payement->amount,
                'payment_method' => $payement->payment_method,
                'paid_at' => $payement->paid_at ? Carbon::parse($payement->paid_at)->format('d/m/Y H:i') : 'N/A',
                'notes' => $payement->notes,
                'created_by' => $payement->user ? $payement->user->name : 'Utilisateur inconnu',
                'created_at' => $payement->created_at ? $payement->created_at->format('d/m/Y H:i') : 'N/A',
            ];
        });

        $invoices = Invoice::with('acte.medicalFile.patient')
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [
            'espece' => 'Espèce',
            'carte_bancaire' => 'Carte bancaire',
            'cheque' => 'Chèque',
            'virement' => 'Virement'
        ];

        return view('livewire.payements.payements', [
            'payements' => $payements,
            'invoices' => $invoices,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}