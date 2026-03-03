@extends('layouts.sawit')

@section('title', 'Berkebun')

@push('styles')
<style>
    .price-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 0.88rem;
    }
    .plant-badge {
        background: #e8f5e9;
        border: 1.5px solid #a5d6a7;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .countdown {
        font-family: monospace;
        font-weight: 700;
        color: #1565c0;
        font-size: 0.95rem;
    }
    .section-title {
        font-weight: 800;
        color: #1b5e20;
        font-size: 1.3rem;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
    .form-control { border-radius: 10px; }
    .coin-preview {
        font-size: 1.1rem;
        font-weight: 700;
        color: #f57f17;
        min-height: 1.5em;
    }
    .progress { border-radius: 10px; height: 8px; }
</style>
@endpush

@section('content')

<div class="row g-4">

    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div class="col-12">
        <h2 class="mb-0 section-title">Berkebun</h2>
        <p class="text-muted">
            Coin kamu: <strong class="text-warning"> {{ number_format(Auth::user()->coin) }}</strong>
            &nbsp;|&nbsp;
            Harga tanam: <strong>{{ \App\Http\Controllers\SawitController::HARGA_TANAM }} coin/sawit</strong>
            &nbsp;|&nbsp;
            Hasil panen: <strong class="text-success">{{ \App\Http\Controllers\SawitController::HARGA_PANEN }} coin/sawit</strong>
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TANAM
    ══════════════════════════════════════════════════════════════ --}}
    <div class="col-lg-6" id="tanam">
        <div class="card h-100">
            <div class="card-header" style="background:linear-gradient(135deg,#1565c0,#0d47a1);color:white;">
                 Tanam Sawit
            </div>
            <div class="card-body">

                <div class="price-info mb-3">
                     Harga tanam: <strong>{{ \App\Http\Controllers\SawitController::HARGA_TANAM }} coin</strong> per pohon.
                    Sawit akan siap dipanen <strong>setelah 1 menit</strong>.
                </div>

                <form action="{{ route('sawit.tanam') }}" method="POST" id="formTanam">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-600">Jumlah Sawit yang Ditanam</label>
                        <input
                            type="number"
                            name="quantity"
                            id="tanamQty"
                            class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                            min="1"
                            placeholder="Masukkan jumlah..."
                            value="{{ old('quantity') }}"
                        >
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 p-3" style="background:#fff8e1;border-radius:10px;">
                        <div class="text-muted" style="font-size:0.85rem;">Total biaya:</div>
                        <div class="coin-preview" id="tanamPreview">— coin</div>
                        <div id="tanamWarning" class="text-danger mt-1" style="font-size:0.82rem; display:none;">
                            ⚠️ Coin tidak cukup!
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="btnTanam">
                         Tanam Sekarang
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PANEN
    ══════════════════════════════════════════════════════════════ --}}
    <div class="col-lg-6" id="panen">
        <div class="card h-100">
            <div class="card-header" style="background:linear-gradient(135deg,#2e7d32,#1b5e20);color:white;">
                 Panen Sawit
            </div>
            <div class="card-body">

                @if($totalBisaDipanen === 0)
                    <div class="text-center py-4 text-muted">
                        <div style="font-size:3rem;"></div>
                        <p class="mt-2">
                            @if($totalSedangDitanam > 0)
                                <strong>{{ $totalSedangDitanam }} sawit</strong> sedang tumbuh.<br>
                                Tunggu 1 menit dari waktu tanam.
                            @else
                                Belum ada sawit yang ditanam. <br>
                                <a href="#tanam">Tanam sekarang!</a>
                            @endif
                        </p>
                    </div>
                @else
                    <div class="price-info mb-3">
                         Hasil panen: <strong class="text-success">{{ \App\Http\Controllers\SawitController::HARGA_PANEN }} coin</strong> per pohon.
                        Tersedia <strong>{{ $totalBisaDipanen }}</strong> sawit siap panen.
                    </div>

                    <form action="{{ route('sawit.panen') }}" method="POST" id="formPanen">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-600">Jumlah yang Dipanen</label>
                            <div class="d-flex gap-2 mb-1">
                                <input
                                    type="number"
                                    name="quantity"
                                    id="panenQty"
                                    class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                                    min="1"
                                    max="{{ $totalBisaDipanen }}"
                                    placeholder="Masukkan jumlah..."
                                    value="{{ old('quantity') }}"
                                >
                                <button type="button" class="btn btn-outline-success"
                                    onclick="document.getElementById('panenQty').value={{ $totalBisaDipanen }}; updatePanenPreview();">
                                    Max
                                </button>
                            </div>
                            <small class="text-muted">Maks: {{ $totalBisaDipanen }} sawit</small>
                            @error('quantity')
                                <div class="text-danger" style="font-size:0.85rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 p-3" style="background:#f1f8e9;border-radius:10px;">
                            <div class="text-muted" style="font-size:0.85rem;">Coin yang didapat:</div>
                            <div class="coin-preview text-success" id="panenPreview">— coin</div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100">
                             Panen Sekarang
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         STATUS KEBUN (batch yang sedang tumbuh)
    ══════════════════════════════════════════════════════════════ --}}
    @if($growingPlants->isNotEmpty() || $readyPlants->isNotEmpty())
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background:#fff8e1; color:#6d4c41; font-weight:700;">
                 Status Kebun Kamu
            </div>
            <div class="card-body">
                <div class="row g-3">

                    {{-- Ready to harvest --}}
                    @foreach($readyPlants as $plant)
                    <div class="col-md-4 col-sm-6">
                        <div style="background:#e8f5e9; border:1.5px solid #a5d6a7; border-radius:12px; padding:14px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:1.5rem;"></span>
                                <span class="badge text-bg-success">Siap Panen</span>
                            </div>
                            <div class="fw-700 mt-1">{{ $plant->remaining_count }} pohon</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                Ditanam {{ $plant->planted_at->format('H:i:s') }}
                            </div>
                        </div>
                    </div>
                    @endforeach

                    {{-- Still growing --}}
                    @foreach($growingPlants as $plant)
                    <div class="col-md-4 col-sm-6">
                        <div style="background:#e3f2fd; border:1.5px solid #90caf9; border-radius:12px; padding:14px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:1.5rem;"></span>
                                <span class="badge text-bg-primary">Tumbuh</span>
                            </div>
                            <div class="fw-700 mt-1">{{ $plant->remaining_count }} pohon</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                Siap dalam:
                                <span class="countdown" data-seconds="{{ $plant->seconds_until_ready }}">
                                    {{ gmdate('i:s', $plant->seconds_until_ready) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    const HARGA_TANAM  = {{ \App\Http\Controllers\SawitController::HARGA_TANAM }};
    const HARGA_PANEN  = {{ \App\Http\Controllers\SawitController::HARGA_PANEN }};
    const USER_COINS   = {{ Auth::user()->coin }};

    // ── Preview biaya tanam ────────────────────────────────────────
    const tanamInput   = document.getElementById('tanamQty');
    const tanamPreview = document.getElementById('tanamPreview');
    const tanamWarning = document.getElementById('tanamWarning');
    const btnTanam     = document.getElementById('btnTanam');

    function updateTanamPreview() {
        const qty   = parseInt(tanamInput?.value) || 0;
        const total = qty * HARGA_TANAM;
        if (qty > 0) {
            tanamPreview.textContent = ` ${total.toLocaleString('id-ID')} coin`;
            const cukup = USER_COINS >= total;
            tanamWarning.style.display = cukup ? 'none' : 'block';
            btnTanam.disabled          = !cukup;
        } else {
            tanamPreview.textContent   = '— coin';
            tanamWarning.style.display = 'none';
            btnTanam.disabled          = false;
        }
    }

    tanamInput?.addEventListener('input', updateTanamPreview);

    // ── Preview hasil panen ────────────────────────────────────────
    const panenInput   = document.getElementById('panenQty');
    const panenPreview = document.getElementById('panenPreview');

    function updatePanenPreview() {
        const qty   = parseInt(panenInput?.value) || 0;
        const total = qty * HARGA_PANEN;
        panenPreview.textContent = qty > 0
            ? `${total.toLocaleString('id-ID')} coin`
            : '— coin';
    }

    panenInput?.addEventListener('input', updatePanenPreview);

    // ── Countdown timers ───────────────────────────────────────────
    document.querySelectorAll('.countdown').forEach(el => {
        let secs = parseInt(el.dataset.seconds);

        const tick = () => {
            if (secs <= 0) {
                el.textContent = 'Siap!';
                el.style.color = '#2e7d32';
                // Auto refresh halaman agar card pindah ke ready
                setTimeout(() => location.reload(), 1200);
                return;
            }
            const m = String(Math.floor(secs / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            el.textContent = `${m}:${s}`;
            secs--;
            setTimeout(tick, 1000);
        };

        tick();
    });
</script>
@endpush