<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .header, .footer { width: 100%; }
        .header {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #eee;
            padding: 10px 0 8px 0;
            min-height: 90px;
        }
        .header-left { width: 55%; vertical-align: top; }
        .header-right { width: 45%; text-align: right; vertical-align: top; }
        .logo {width: 70px; margin-bottom: 5px;}
        .profile-info { font-size: 10px; margin-top: 5px; line-height: 1.4; }
        .invoice-meta { font-size: 11px; }
        .invoice-meta h2 { font-size: 15px; margin: 0 0 5px 0; }
        .section-title { text-align: start; font-size: 13px; font-weight: bold; margin: 7px 0; }
        .info-table, .services-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .info-table td, .info-table th, .services-table td, .services-table th { border: 1px solid #ddd; padding: 5px; }
        .info-table th, .services-table th { background: #f5f5f5; }
        .services-table th { text-align: left; }
        .totals { margin-top: 8px; width: 100%; }
        .totals td { padding: 4px 6px; }
        .totals .label { text-align: right; font-weight: bold; }
        .signature-section { margin-top: 28px; display: flex; justify-content: space-between; }
        .signature-block { width: 45%; text-align: center; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #888; border-top: 1px solid #eee; padding: 6px 0; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    @php
        $profile = \App\Models\DentalProfile::first();
        $patient = $invoice->acte->medicalFile->patient ?? null;
        $acte = $invoice->acte;
        $services = $acte->acteServices()->with('service')->get();
    @endphp

    <!-- Header -->
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @if($profile && $profile->logo)
                        <img src="{{ public_path('storage/' . $profile->logo) }}" class="logo" alt="Logo" />
                    @endif
                    <div class="profile-info">
                        <strong>{{ $profile->clinic_name ?? '' }}</strong><br>
                        {{ $profile->address ?? '' }}, {{ $profile->city ?? '' }}<br>
                        Tél: {{ $profile->phone_01 ?? '' }}@if($profile && $profile->phone_02) / {{ $profile->phone_02 }}@endif<br>
                        Email: {{ $profile->email ?? '' }}<br>
                        ICE: {{ $profile->ICE ?? '' }} | IF: {{ $profile->IF ?? '' }}<br>
                        @if($profile && $profile->TVA) TVA: {{ $profile->TVA }}%<br>@endif
                    </div>
                </td>
                <td style="width: 45%; text-align: right; vertical-align: top;">
                    <div class="invoice-meta">
                        <h2 style="margin-bottom: 5px;">Facture</h2>
                        <strong>N°: {{ $invoice->invoice_number }}</strong><br>
                        Date: {{ $invoice->created_at ? $invoice->created_at->format('d/m/Y') : '' }}<br>
                        Acte N°: {{ $acte->acte_number ?? '' }}<br>
                        @php
                            $status = strtolower($invoice->status);
                            $ribbonStyle = 'display:inline-block;padding:6px 16px;border-radius:20px;font-weight:bold;font-size:11px;color:white;';
                            if ($status === 'payé') {
                                $ribbonStyle .= 'background-color:#38b2ac;';
                            } elseif ($status === 'partiel') {
                                $ribbonStyle .= 'background-color:#3490dc;';
                            } else {
                                $ribbonStyle .= 'background-color:#e3342f;';
                            }
                        @endphp
                        <span style="{{ $ribbonStyle }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </td>                
            </tr>
        </table>
    </div>    

    <!-- Patient Info -->
    <div class="section-title">Informations du Patient</div>
    <table class="info-table">
        <tr>
            <th>Nom complet</th>
            <th>Téléphone</th>
            <th>Date de naissance</th>
            <th>Adresse</th>
            <th>Assurance</th>
            <th>Dossier Médical</th>
        </tr>
        <tr>
            <td>{{ $patient->patient_full_name ?? '' }}</td>
            <td>{{ $patient->phone ?? '' }}</td>
            <td>{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : '' }}</td>
            <td>{{ $patient->address ?? '' }}</td>
            <td>{{ $patient->insurance_type ?? '' }}</td>
            <td>{{ $acte->medicalFile->file_number ?? '' }}</td>
        </tr>
    </table>

    <!-- Acte Info -->
    <div class="section-title">Détails de l'Acte</div>
    <table class="info-table">
        <tr>
            <th>Date de l'acte</th>
            <th>Statut</th>
            <th>Statut Paiement</th>
            <th>Notes</th>
        </tr>
        <tr>
            <td>{{ $acte->acte_date ? \Carbon\Carbon::parse($acte->acte_date)->format('d/m/Y') : '' }}</td>
            <td>{{ ucfirst($acte->status) }}</td>
            <td>{{ $acte->payment_status }}</td>
            <td>{{ $acte->notes ?? '' }}</td>
        </tr>
    </table>

    <!-- Services Table -->
    <div class="section-title">Prestations / Services</div>
    <table class="services-table">
        <thead>
            <tr>
                <th>Service</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Dent</th>
                <th>Libellé</th>
                <th>Notes</th>
                <th>PU</th>
                <th>PT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $i => $as)
                <tr>
                    <td>{{ $as->service->name ?? '' }}</td>
                    <td>{{ $as->service->description ?? '' }}</td>
                    <td>1</td>
                    <td>{{ $as->tooth_number ?? '' }}</td>
                    <td>{{ $as->libelle ?? '' }}</td>
                    <td>{{ $as->notes ?? '' }}</td>
                    <td>{{ number_format($as->price, 2) }} MAD</td>
                    <td>{{ number_format($as->price, 2) }} MAD</td>
                </tr>
            @endforeach
            <!-- Summary Row -->
            <tr>
                <th colspan="6" style="text-align:right; background:#f5f5f5;">Montant HT</th>
                <td colspan="2">{{ number_format($invoice->base_amount, 2) }} MAD</td>
            </tr>
            @if($invoice->tva_rate && $invoice->tva_amount)
            <tr>
                <th colspan="6" style="text-align:right; background:#f5f5f5;">TVA ({{ $invoice->tva_rate * 100 }}%)</th>
                <td colspan="2">{{ number_format($invoice->tva_amount, 2) }} MAD</td>
            </tr>
            @endif
            <tr>
                <th colspan="6" style="text-align:right; background:#f5f5f5;">Total TTC</th>
                <td colspan="2">{{ number_format($invoice->total_amount, 2) }} MAD</td>
            </tr>
            <tr>
                <th colspan="6" style="text-align:right; background:#f5f5f5;">Montant Payé</th>
                <td colspan="2">{{ number_format($invoice->paid_amount, 2) }} MAD</td>
            </tr>
            <tr>
                <th colspan="6" style="text-align:right; background:#f5f5f5;">Reste à payer</th>
                <td colspan="2">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} MAD</td>
            </tr>
        </tbody>
    </table>

    <!-- Notes & Signature Section as Table -->
    <table style="width:100%; margin-top:32px;">
        <tr>
            <td style="width:60%; vertical-align:top; padding-right:16px;">
                <div style="font-weight:bold; margin-bottom:6px;">Notes</div>
                <div style="min-height:40px; border:1px solid #eee; border-radius:6px; padding:8px; background:#fafafa; font-size:11px;">
                    {{ $acte->notes ?? '__' }}
                </div>
            </td>
            <td style="width:40%; vertical-align:top; text-align:center;">
                <div style="font-weight:bold; margin-bottom:6px;">Signature</div>
                @if($profile && $profile->signature)
                    <img src="{{ public_path('storage/' . $profile->signature) }}" alt="Signature" style="height: 85px; margin-top: 8px;">
                @else
                    <div style="height:85px; border:1px dashed #ccc; border-radius:6px; display:inline-block; width:120px;"></div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Page <span class="page-number"></span>
    </div>
</body>
</html>