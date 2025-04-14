<?php

namespace App\Http\Controllers;

use App\Models\PointVente;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function listUsers()
    {
        // $users = User::with('roles')->paginate(20);
        // return response()->json([
        //     'users'  => $users
        // ]);

        $users = User::with('roles')->get();

        return DataTables::of($users)
            ->addColumn('role', function ($user) {
                // Access the 'roles' relationship as if it were an attribute
                return $user->roles->pluck('name')->implode(', ');
            })
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();

        return view('pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $points = PointVente::all();
        $zones = Zone::all();
        return view('pages.users.create', compact('roles', 'points','zones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'address' => 'required|string',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'point_vente_id' => 'required',
            'zone_id' => 'required',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'point_vente_id' => $request->point_vente_id,
            'zone_id' => $request->zone_id,
            'is_active' => true,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'Agent créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $pointVentes = PointVente::all();
        $pointVentes = PointVente::all();
        $zones = Zone::all();
        $userRoles = $user->roles->pluck('name', 'name')->all();

        return view('pages.users.edit', compact('user', 'roles', 'userRoles', 'pointVentes','zones','userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $user = User::findOrFail($id);
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'phone' => ['required', Rule::unique("users", "phone")->ignore($id)],
            'address' => 'required|string',
            'email' => ['required', Rule::unique("users", "email")->ignore($id)],
            'roles' => 'required',
            'point_vente_id' => 'required',
            'zone_id' => 'required',
        ]);

        $input = $request->all();

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'Modification effectuée avec succès!');
    }

    public function usersByPoint($pointId)
    {
        $users = User::where('point_vente_id', $pointId)->get();
        return response()->json([
            "users" => $users
        ], 200);
    }
}
