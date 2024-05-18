<?php

namespace Modules\System\Http\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Feature;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class FeatureController extends AdminController
{
    public function index()
    {
        $this->content['roles'] = Role::all();
        return view('system::feature', $this->content);
    }

    public function map(Request $request)
    {
        Role::where('id', $request->post('role_id'))
            ->update([
                'permissions' => join(',', $request->post('features'))
            ]);
        return $this->responseJson(null, 'Pemetaan fitur telah tersimpan');
    }

    public function source($role)
    {
        $feature = [];

        $mapped = Role::where('id', $role)->pluck('permissions')->first();
        $mapped = preg_split('/,/', $mapped);
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $name = $route->getName();
            if (
                !is_null($name) &&
                !preg_match('/ignition|telescope/', $name) &&
                !in_array($name, ['login', 'logout', 'authenticate', 'forgot'])
            ) array_push($feature, (object)[
                'name' => $name,
                'selected' => in_array($name, $mapped)
            ]);
        }

        return $this->responseJson(
            [
                'options' => $feature,
                'origin' => $mapped
            ]
        );
    }
}
