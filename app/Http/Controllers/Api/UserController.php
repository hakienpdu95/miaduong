<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function datatable(Request $request)
    {
        $query = User::select(['users.id', 'users.username', 'users.name', 'users.email', 'users.created_at', 'users.is_active'])
            ->with(['profile', 'profile.unit']);

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        if ($request->filled('filter_username')) {
            $query->where('users.username', 'like', '%' . $request->filter_username . '%');
        }

        if ($request->filled('filter_name')) {
            $query->where('users.name', 'like', '%' . $request->filter_name . '%');
        }

        if ($request->filled('filter_account_type')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('account_type', $request->filter_account_type);
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('account_type', function ($user) {
                $type = optional($user->profile)->account_type ?? '';
                return $type === 'warehouse_management' ? 'Quản lý kho' : ($type === 'foreman' ? 'Quản đốc' : '');
            })
            ->addColumn('unit_name', function ($user) {
                return optional(optional($user->profile)->unit)->name ?? '';
            })
            ->addColumn('formatted_created_at', function ($user) {
                return $user->created_at ? $user->created_at->format('d/m/Y H:i') : '';
            })
            ->addColumn('actions', function ($user) {
                $toggleIcon = $user->is_active ? 'fa-lock-open' : 'fa-lock';
                $toggleTitle = $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản';
                return '<a href="' . route('user.edit', $user->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-warning toggle-active me-1" data-id="' . $user->id . '" data-active="' . $user->is_active . '" title="' . $toggleTitle . '"><i class="fa-light ' . $toggleIcon . '"></i></button>' .
                       '<button class="btn btn-sm btn-info reset-password me-1" data-id="' . $user->id . '" title="Reset mật khẩu"><i class="fa-light fa-key"></i></button>' .
                       '<button class="btn btn-sm btn-danger delete-user" data-id="' . $user->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return response()->json(['success' => true, 'message' => 'Trạng thái tài khoản đã được cập nhật.', 'new_active' => $user->is_active]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $defaultPassword = 'checkvn@123'; // Mật khẩu mặc định, có thể thay đổi
        $user->password = Hash::make($defaultPassword);
        $user->save();
        return response()->json(['success' => true, 'message' => 'Mật khẩu đã được reset về mặc định (' . $defaultPassword . ').']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Tài khoản đã được xóa thành công.']);
    }
}