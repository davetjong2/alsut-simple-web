<?php

namespace App\Http\Controllers;

use App\Models\SawitPlant;
use App\Models\SawitTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SawitController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Harga
    |--------------------------------------------------------------------------
    */
    const HARGA_TANAM  = 10;  // coin per sawit
    const HARGA_PANEN  = 15;  // coin per sawit

    /*
    |--------------------------------------------------------------------------
    | GET /kebun  →  Dashboard "Kebun Sawitku"
    |--------------------------------------------------------------------------
    */
    public function kebun()
    {
        $user = Auth::user();

        // Jumlah sawit yang masih tumbuh (belum 1 menit)
        $sedangDitanam = $user->sawitPlants()
            ->growing()
            ->sum(DB::raw('quantity - quantity_harvested'));

        // Jumlah sawit yang siap dipanen (sudah >= 1 menit)
        $bisaDipanen = $user->sawitPlants()
            ->ready()
            ->sum(DB::raw('quantity - quantity_harvested'));

        // History 20 transaksi terakhir
        $history = $user->sawitTransactions()
            ->latest()
            ->limit(20)
            ->get();

        return view('sawit.kebun', compact(
            'user',
            'sedangDitanam',
            'bisaDipanen',
            'history'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | GET /berkebun  →  Form Tanam & Panen
    |--------------------------------------------------------------------------
    */
    public function berkebun()
    {
        $user = Auth::user();

        // Batch yang siap dipanen beserta sisa masing-masing
        $readyPlants = $user->sawitPlants()
            ->ready()
            ->get()
            ->map(function ($plant) {
                $plant->remaining_count = $plant->remaining;
                return $plant;
            });

        $totalBisaDipanen = $readyPlants->sum('remaining_count');

        // Batch yang masih tumbuh (untuk info countdown)
        $growingPlants = $user->sawitPlants()
            ->growing()
            ->orderBy('planted_at')
            ->get()
            ->map(function ($plant) {
                $plant->remaining_count       = $plant->remaining;
                $plant->seconds_until_ready   = $plant->seconds_until_ready;
                return $plant;
            });

        $totalSedangDitanam = $growingPlants->sum('remaining_count');

        return view('sawit.berkebun', compact(
            'user',
            'readyPlants',
            'growingPlants',
            'totalBisaDipanen',
            'totalSedangDitanam'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | POST /berkebun/tanam  →  Proses Tanam
    |--------------------------------------------------------------------------
    */
    public function tanam(Request $request)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $user     = Auth::user();
        $quantity = (int) $request->quantity;
        $totalBiaya = $quantity * self::HARGA_TANAM;

        // Validasi koin cukup
        if ($user->coin < $totalBiaya) {
            return back()
                ->withErrors(['quantity' => "Coin tidak cukup! Kamu butuh {$totalBiaya} coin, tapi hanya punya {$user->coin} coin."])
                ->withInput();
        }

        DB::transaction(function () use ($user, $quantity, $totalBiaya) {
            // Buat batch tanam
            $plant = SawitPlant::create([
                'user_id'    => $user->id,
                'quantity'   => $quantity,
                'cost'       => $totalBiaya,
                'planted_at' => Carbon::now(),
                'status'     => 'growing',
            ]);

            // Catat transaksi
            SawitTransaction::create([
                'user_id'       => $user->id,
                'sawit_plant_id'=> $plant->id,
                'type'          => 'tanam',
                'quantity'      => $quantity,
                'amount'        => $totalBiaya,
                'coin_flow'     => 'out',
            ]);

            // Kurangi koin user
            $user->decrement('coin', $totalBiaya);
        });

        return redirect()->route('sawit.berkebun')
            ->with('success', "Berhasil menanam {$quantity} sawit! -{$totalBiaya} coin. Tunggu 1 menit untuk panen.");
    }

    /*
    |--------------------------------------------------------------------------
    | POST /berkebun/panen  →  Proses Panen
    |--------------------------------------------------------------------------
    */
    public function panen(Request $request)
    {
        $user = Auth::user();

        // Hitung total yang bisa dipanen dulu untuk validasi max
        $totalBisaDipanen = $user->sawitPlants()->ready()
            ->sum(DB::raw('quantity - quantity_harvested'));

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', "max:{$totalBisaDipanen}"],
        ], [
            'quantity.max' => "Sawit yang bisa dipanen hanya {$totalBisaDipanen} pohon.",
        ]);

        $sisa    = (int) $request->quantity;
        $totalPendapatan = $sisa * self::HARGA_PANEN;

        DB::transaction(function () use ($user, $sisa, $totalPendapatan) {
            $jumlahDipanen = $sisa;

            // Ambil batch ready, urutkan dari yang paling lama (FIFO)
            $plants = $user->sawitPlants()
                ->ready()
                ->orderBy('planted_at')
                ->get();

            foreach ($plants as $plant) {
                if ($sisa <= 0) break;

                $available   = $plant->remaining;
                $panenSekarang = min($available, $sisa);

                $plant->increment('quantity_harvested', $panenSekarang);

                // Jika batch ini sudah habis, tandai harvested
                if ($plant->fresh()->remaining === 0) {
                    $plant->update([
                        'status'              => 'harvested',
                        'fully_harvested_at'  => Carbon::now(),
                    ]);
                } else {
                    $plant->update(['status' => 'ready']);
                }

                $sisa -= $panenSekarang;
            }

            // Catat transaksi
            SawitTransaction::create([
                'user_id'   => $user->id,
                'type'      => 'panen',
                'quantity'  => $jumlahDipanen,
                'amount'    => $totalPendapatan,
                'coin_flow' => 'in',
            ]);

            // Tambah koin user
            $user->increment('coin', $totalPendapatan);
        });

        $qty = (int) $request->quantity;
        return redirect()->route('sawit.berkebun')
            ->with('success', "Panen {$qty} sawit berhasil! +{$totalPendapatan} coin. ");
    }
}