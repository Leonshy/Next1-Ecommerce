<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::latest('created_at');

        if ($admin = $request->input('admin')) {
            $query->where('admin_email', 'like', "%{$admin}%");
        }

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($resource = $request->input('resource')) {
            $query->where('resource_type', 'like', "%{$resource}%");
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('admin.settings.audit', compact('logs'));
    }
}
