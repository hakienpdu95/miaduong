<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserProfile;
use Exception;

/**
 * Controller for managing users in the backend.
 */
class UserController extends Controller
{
    public function index()
    {
        return view('backend.user.index');
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = Unit::all();
        return view('backend.user.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $userData = [
                'username' => $validated['username'],
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
                'password' => $validated['password'], // Sẽ được hashed tự động qua casts
                'is_active' => true, // Mặc định active
            ];

            $user = User::create($userData);

            // Gán vai trò dựa trên account_type
            $role = Role::where('name', $validated['account_type'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            } else {
                throw new Exception('Vai trò không tồn tại cho loại tài khoản: ' . $validated['account_type']);
            }

            // Tạo profile
            $profileData = [
                'account_type' => $validated['account_type'],
                'unit_id' => $validated['unit_id'],
            ];

            $user->profile()->create($profileData);

            DB::commit();
            return redirect()->route('user.index')->with('success', 'Tài khoản đã được tạo thành công.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo tài khoản: ' . $e->getMessage());
        }
    }
}