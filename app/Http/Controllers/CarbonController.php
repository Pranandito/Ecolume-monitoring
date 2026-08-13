<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CarbonController extends Controller
{
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "carbon_factor" => 'numeric|required|min:0'
        ]);

        $update = User::where('id', $id)->update(['carbon_factor' => $validated['carbon_factor']]);

        return back();
    }
}
