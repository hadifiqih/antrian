<?php

namespace App\Http\Controllers;

use App\Models\SocialRecord;
use Illuminate\Http\Request;
use App\Models\SocialAccount;

class SocialRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'akun' => 'required',
            'platform' => 'required',
            'followers' => 'required|integer',
        ]);

        try {
            $socialAccount = SocialAccount::where('id', $validated['akun'])->first();

            if (!$socialAccount) {
                return response()->json(['message' => 'Akun tidak ditemukan'], 404);
            }

            $socialRecord = new SocialRecord;
            $socialRecord->social_account_id = $socialAccount->id;
            $socialRecord->jumlah_followers = $validated['followers'];
            $socialRecord->save();

            // Update followers di tabel social_account
            $socialAccount->update_followers = $validated['followers'];
            $socialAccount->save();

            return response()->json(['message' => 'Data berhasil disimpan'], 200);
        } catch (\Exception $e) {
            \Log::error('Error saving social record: ' . $e->getMessage());
            return response()->json(['message' => 'Data gagal disimpan'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialRecord $socialRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SocialRecord $socialRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialRecord $socialRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialRecord $socialRecord)
    {
        //
    }
}
