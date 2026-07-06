<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::withCount('workReports')->latest()->get()]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', 'in:admin,user']]);

        abort_if($user->is($request->user()) && $data['role'] !== 'admin', 422, 'Role admin Anda sendiri tidak dapat diturunkan.');
        $user->update($data);

        return back()->with('success', 'Role pengguna berhasil diperbarui.');
    }
}
