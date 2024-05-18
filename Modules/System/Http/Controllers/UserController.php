<?php

namespace Modules\System\Http\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends AdminController
{

    public function index()
    {
        $this->content['roles'] = Role::all();
        return view('system::user', $this->content);
    }

    public function datatable(Request $request)
    {
        $param = $request->post('datatable');
        $query = User::query()->with('role');
        return DataTables::of($query)->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required'
        ]);

        $result = null;
        if ($request->post('id') != null) {
            $result = User::where('id', $request->post('id'))
                ->update([
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                    'role_id' => $request->post('role_id'),
                    'password' => Hash::make('password'),
                ]);
        } else {
            $result = User::create([
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'role_id' => $request->post('role_id'),
                'password' => Hash::make('password'),
            ])->id;
        }

        return $this->responseJson($result);
    }

    public function destroy(Request $request)
    {
        $id = $request->post('id');
        $result = User::destroy($id);
        return $this->responseJson(
            $result,
            ($result == 0) ? 'Gagal menghapus data' : 'Data berhasil dihapus',
            ($result == 0) ? 400 : 200,
            ($result == 0) ? 400 : 200
        );
    }

    public function password(Request $request)
    {
        $user = $request->validate([
            'id' => 'required',
            'password' => 'required'
        ]);

        $result = User::where('id', $user['id'])
            ->update([
                'password' => Hash::make($user['password'])
            ]);

        return $this->responseJson(
            $result,
            ($result == 0) ? 'Gagal mengubah password' : 'Berhasil mengubah password',
            ($result == 0) ? 400 : 200,
            ($result == 0) ? 400 : 200
        );
    }
}
