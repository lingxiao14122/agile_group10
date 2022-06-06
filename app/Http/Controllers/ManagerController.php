<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function trackingInTransit() {
        // returns courier information and in-transit parcel count
        // , except courier with no in-transit parcel
        $table = DB::table('users')
        ->select('users.id', 'users.first_name', 'users.last_name')
        ->selectRaw('count(*) AS total')
        ->where('role_id', Role::ROLE_COURIER)
        ->where('parcels.status', Parcel::STATUS_IN_TRANSIT)
        ->join('parcels', 'users.id', 'parcels.courier_id')
        ->groupBy('users.id', 'users.first_name', 'users.last_name')
        ->get();

        return view('manager.tracking_in_transit', ['table' => $table]);
    }

    public function trackingInTransitSingle($courier_id) {
        $parcels = Parcel::where('courier_id', $courier_id)
            ->where('status', Parcel::STATUS_IN_TRANSIT)
            ->get();
        $courier_name = User::where('id', $courier_id)
            ->get()
            ->first()
            ->first_name;
        // ddd(get_defined_vars());
        return view('manager.tracking_in_transit_single', ['parcels' => $parcels, 'courier_name' => $courier_name]);
    }
}
