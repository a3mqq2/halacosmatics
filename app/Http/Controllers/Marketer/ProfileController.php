<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMarketerPasswordRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $marketer = Auth::guard('marketer')->user();
        $about    = Setting::get('about');
        $policy   = Setting::get('policy');

        return view('marketer.profile', compact('marketer', 'about', 'policy'));
    }

    public function updatePassword(UpdateMarketerPasswordRequest $request)
    {
        $marketer = Auth::guard('marketer')->user();

        if (! Hash::check($request->current_password, $marketer->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.'])->withFragment('password-section');
        }

        $marketer->update(['password' => $request->password]);

        return back()->with('password_success', 'تم تحديث كلمة المرور بنجاح.')->withFragment('password-section');
    }
}
