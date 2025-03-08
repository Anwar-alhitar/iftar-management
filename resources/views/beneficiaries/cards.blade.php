@foreach($beneficiaries as $beneficiary)
<div class="card print-card">
    <div class="card-header">
        <h3>{{ $beneficiary->full_name }}</h3>
    </div>
    <div class="card-body">
        <div class="qr-code">
            {!! QrCode::size(100)->generate($beneficiary->serial_number) !!}
        </div>
        <div class="details">
            <p>الرقم التسلسلي: {{ $beneficiary->serial_number }}</p>
            <p>رقم الهوية: {{ $beneficiary->id_number }}</p>
        </div>
    </div>
</div>

<style>
.print-card {
    width: 300px;
    border: 1px solid #ddd;
    margin: 10px;
    padding: 15px;
    page-break-inside: avoid;
}

.qr-code svg {
    width: 100% !important;
    height: auto !important;
}

@media print {
    .print-card {
        width: 100%;
        margin: 0;
        border: none;
    }
}
</style>
@endforeach