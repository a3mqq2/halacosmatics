<?php

namespace App\Http\Controllers;

use App\Services\MosafirClient;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private SettingService $settingService,
        private MosafirClient $mosafirClient,
    ) {}

    public function edit()
    {
        $settings = $this->settingService->getAll();

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'about'  => 'nullable|string',
            'policy' => 'nullable|string',
        ]);

        $this->settingService->save([
            'about'  => $request->input('about'),
            'policy' => $request->input('policy'),
        ]);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }

    public function musafirSave(Request $request)
    {
        $request->validate([
            'musafir_phone'    => 'nullable|string|max:20',
            'musafir_password' => 'nullable|string|max:255',
        ]);

        $this->settingService->save([
            'musafir_phone'    => $request->input('musafir_phone'),
            'musafir_password' => $request->input('musafir_password'),
        ]);

        return back()->with('success', 'تم حفظ بيانات شركة المسافر بنجاح.');
    }

    public function musafirLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'musafir_phone'    => 'required|string|max:20',
            'musafir_password' => 'required|string|max:255',
        ]);

        $phone    = $request->input('musafir_phone');
        $password = $request->input('musafir_password');

        $result = $this->mosafirClient->login($phone, $password);

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بشركة المسافر. تحقق من بيانات الدخول.',
            ], 422);
        }

        $this->settingService->save([
            'musafir_token'      => $result['token'],
            'musafir_owner_name' => $result['owner_name'] ?? $result['name'] ?? '',
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'تم الربط بشركة المسافر بنجاح.',
            'owner_name' => $result['owner_name'] ?? $result['name'] ?? '',
            'token'      => $result['token'],
        ]);
    }
}
