<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function datatable(Request $request)
    {
        $columns = ['id', 'username', 'name', 'email', 'created_at'];
        $query = User::select($columns)->with(['profile', 'profile.unit']);

        // Optional: Show soft deleted nếu param ?withTrashed=1
        if ($request->filled('withTrashed')) {
            $query->withTrashed();
        }

        if ($request->filled('filter_username')) {
            $query->where('username', 'like', '%' . $request->filter_username . '%');
        }

        if ($request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $request->filter_name . '%');
        }

        if ($request->filled('filter_account_type')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('account_type', $request->filter_account_type);
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('account_type', function ($user) {
                $type = $user->profile->account_type ?? '';
                return $type === 'warehouse_management' ? 'Quản lý kho' : ($type === 'foreman' ? 'Quản đốc' : '');
            })
            ->addColumn('unit_name', function ($user) {
                return $user->profile->unit->name ?? '';
            })
            ->addColumn('formatted_created_at', function ($user) {
                return $user->created_at->format('d/m/Y H:i'); // Giả sử có accessor formatted_created_at, hoặc format trực tiếp
            })
            ->addColumn('actions', function ($user) {
                return '<a href="' . route('user.edit', $user->id) . '" class="btn btn-sm btn-primary me-1"><i class="fa-light fa-pen-to-square"></i></a>' .
                       '<button class="btn btn-sm btn-danger delete-user" data-id="' . $user->id . '"><i class="fa-light fa-trash"></i></button>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!auth()->user()->can('delete-user')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Tài khoản đã được xóa thành công.']);
    }
}