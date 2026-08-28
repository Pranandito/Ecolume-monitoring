<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{

    public function __construct(
        private Logger $log
    ) {}

    public function addAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string'
        ]);

        $accountValidate = User::where('email', $validated['email'])->first();

        if (!$accountValidate) {
            return "tidak ada akunnya";
        }

        // return $accountValidate;

        $update = User::where('email', $validated['email'])->update([
            'role' => 'admin',
            'role_updated_at' => now()
        ]);


        $data = [
            "added_admin_name" => $accountValidate->name,
            "admin_name" => Auth::user()->name
        ];

        $this->log->create(
            Auth::id(),
            'addAdmin',
            'admin',
            $accountValidate->id,
            $data
        );

        return redirect()->back();
    }
}
