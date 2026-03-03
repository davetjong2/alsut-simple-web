@extends('layouts.sawit')

@section('title', 'Kebun Sawitku')

@section('content')

<div class="row g-4">

    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div class="col-12">
        <h2 class="fw-800 mb-0" style="font-weight:800; color:#1b5e20;">
            Kebun Sawitku
        </h2>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
    </div>

    {{-- ── 3 Stat Cards ────────────────────────────────────────── --}}
    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="stat-number" style="color:#f57f17;">{{ number_format($user->coin) }}</div>
            <div class="stat-label">Jumlah Coin</div>
            <div class="mt-3">
                <a href="{{ route('sawit.berkebun') }}" class="btn btn-warning btn-sm w-100">
                    Pergi Berkebun →
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="stat-number" style="color:#1565c0;"> {{ number_format($sedangDitanam) }}</div>
            <div class="stat-label">Sawit Sedang Tumbuh</div>
            <div class="mt-3 text-muted" style="font-size:0.8rem;">Tunggu 1 menit untuk panen</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card h-100">
            <div class="stat-number" style="color:#2e7d32;"> {{ number_format($bisaDipanen) }}</div>
            <div class="stat-label">Sawit Siap Dipanen</div>
            @if($bisaDipanen > 0)
            <div class="mt-3">
                <a href="{{ route('sawit.berkebun') }}#panen" class="btn btn-success btn-sm w-100">
                    Panen Sekarang! 🎉
                </a>
            </div>
            @else
            <div class="mt-3 text-muted" style="font-size:0.8rem;">Belum ada yang siap</div>
            @endif
        </div>
    </div>

    {{-- ── History / Log ────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background: linear-gradient(135deg,#2e7d32,#1b5e20); color:white;">
                <span>📋 History Aktivitas</span>
                <small class="opacity-75">20 transaksi terakhir</small>
            </div>

            @if($history->isEmpty())
            <div class="card-body text-center py-5 text-muted">
                <div style="font-size:3rem;"></div>
                <p class="mt-2 mb-0">Belum ada aktivitas. <a href="{{ route('sawit.berkebun') }}">Mulai berkebun!</a></p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Coin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $tx)
                        <tr>
                            <td class="text-muted" style="font-size:0.85rem; white-space:nowrap;">
                                {{ $tx->created_at->format('d M Y, H:i') }}
                            </td>
                            <td>
                                @if($tx->type === 'tanam')
                                    <span class="badge text-bg-primary"> Tanam</span>
                                @else
                                    <span class="badge text-bg-success"> Panen</span>
                                @endif
                            </td>
                            <td class="text-center fw-600">{{ $tx->quantity }}</td>
                            <td class="text-end fw-700">
                                @if($tx->coin_flow === 'out')
                                    <span class="text-danger">-{{ number_format($tx->amount) }}</span>
                                @else
                                    <span class="text-success">+{{ number_format($tx->amount) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection