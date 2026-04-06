<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        $query->with('roles');
        $query->orderBy('created_at', 'desc');
        $users = $query->paginate(10);
        return response()->json($users, 200);
    }

    public function show(User $user)
    {
        $roles = Role::pluck('name');
        return response()->json(['user' => $user, 'roles' => $roles], 200);
    }

     /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user): Response
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users','email')->ignore($user->id),
            ],
            'password' => ['sometimes','nullable','confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->address = $request->address;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $role = Role::findById($request->role);
        $user->syncRoles($role);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ],200);
    }


}
