<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan halaman Log Report
     */
    public function index(Request $request)
    {
        $defaultMulai = now()->subDays(14)->toDateString();
        $defaultAkhir = now()->toDateString();

        $tanggalMulai   = $request->input('tanggal_mulai', $defaultMulai);
        $tanggalAkhir   = $request->input('tanggal_akhir', $defaultAkhir);
        $selectedModule = $request->input('module');
        $selectedAction = $request->input('action');
        $selectedUser   = $request->input('user_id');

        $isFiltered = $request->has('filter');
        $logs = collect([]);

        if ($isFiltered) {
            $query = ActivityLog::query();

            if ($tanggalMulai) {
                $query->whereDate('created_at', '>=', $tanggalMulai);
            }
            if ($tanggalAkhir) {
                $query->whereDate('created_at', '<=', $tanggalAkhir);
            }
            if ($selectedModule) {
                $query->where('module', $selectedModule);
            }
            if ($selectedAction) {
                $query->where('action', $selectedAction);
            }
            if ($selectedUser) {
                $query->where('user_id', $selectedUser);
            }

            $logs = $query->orderBy('id', 'desc')->get();
        }

        $userList = User::orderBy('name')->get();
        $moduleList = [
            'Autentikasi',
            'Master User',
            'Master Karyawan',
            'Master Barang',
            'Master Pekerjaan',
            'Master Lokasi Subcon',
            'Formulir Pengerjaan',
        ];
        $actionList = [
            'LOGIN',
            'LOGOUT',
            'LOGIN_FAILED',
            'VALIDATION_FAILED',
            'ACCESS_DENIED',
            'CREATE',
            'UPDATE',
            'DELETE',
            'TOGGLE_STATUS',
        ];

        return view('pages.log-report', compact(
            'logs',
            'isFiltered',
            'tanggalMulai',
            'tanggalAkhir',
            'selectedModule',
            'selectedAction',
            'selectedUser',
            'userList',
            'moduleList',
            'actionList'
        ));
    }
}
