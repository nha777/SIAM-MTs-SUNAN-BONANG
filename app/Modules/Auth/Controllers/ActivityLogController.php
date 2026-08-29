<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Models\AuditLog;
use App\Modules\Base\Traits\BaseApiResponse;

class ActivityLogController extends Controller
{
    use BaseApiResponse;

    public function index(Request $request)
    {
        Gate::authorize('activity_log.view');
        
        $search = $request->input('search');
        $event = $request->input('event');
        
        $query = AuditLog::with('user')->latest();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('auditable_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($event) {
            $query->where('event', $event);
        }

        $logs = $query->paginate(15)->withQueryString();

        if ($request->wantsJson()) {
            return $this->successResponse($logs, 'Activity logs retrieved successfully');
        }

        return view('auth::activity-logs.index', compact('logs'));
    }

    public function show(Request $request, $id)
    {
        Gate::authorize('activity_log.view');
        
        $log = AuditLog::with('user')->findOrFail($id);
        
        if ($request->wantsJson()) {
            return $this->successResponse($log, 'Activity log retrieved successfully');
        }

        return view('auth::activity-logs.show', compact('log'));
    }
}
