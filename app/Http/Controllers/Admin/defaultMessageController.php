<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Counselor;

class defaultMessageController extends AdminBaseController
{
    public function showSettings()
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'counselor' && $user->role !== 'konselor') {
                return redirect()->back()->with('error', 'Access denied');
            }
            
            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return redirect()->back()->with('error', 'Counselor profile not found');
            }

            return view('admin.konseling.default', compact('counselor'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load settings');
        }
    }

    public function updateSettings(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'counselor' && $user->role !== 'konselor') {
                return redirect()->back()->with('error', 'Access denied');
            }
            
            $counselor = Counselor::where('user_id', $user->id)->first();
            if (!$counselor) {
                return redirect()->back()->with('error', 'Counselor profile not found');
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'default_chat_message' => 'nullable|string|max:500',
                'auto_send_welcome' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update counselor settings
            $counselor->update([
                'default_chat_message' => $request->default_chat_message,
                'auto_send_welcome' => $request->has('auto_send_welcome') ? true : false
            ]);

            return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}