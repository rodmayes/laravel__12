<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use App\Http\Requests\PermissionIndexRequest;
use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:permission.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permission.read', ['only' => ['index', 'show']]);
        $this->middleware('permission:permission.update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permission.delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PermissionIndexRequest $request): Response
    {
        $permissions = Permission::query();
        if ($request->has('search')) {
            $permissions->where('name', 'LIKE', "%" . $request->search . "%");
            $permissions->orWhere('guard_name', 'LIKE', "%" . $request->search . "%");
        }

        // Ordenación múltiple
        if ($request->filled('sort')) {
            // El campo sort viene como JSON: '[{"field":"name","order":"asc"},{"field":"created_at","order":"desc"}]'
            $sortArray = json_decode($request->sort, true);

            if (is_array($sortArray)) {
                foreach ($sortArray as $sort) {
                    if (isset($sort['field'], $sort['order'])) {
                        // Por seguridad, validamos que el campo sea permitido
                        if (in_array($sort['field'], ['id', 'name', 'guard_name', 'created_at', 'updated_at'])) {
                            $permissions->orderBy($sort['field'], $sort['order']);
                        }
                    }
                }
            }
        }

        return Inertia::render('Permission/Index', [
            'title'         => 'Permissions',
            'filters'       => $request->all(['search', 'field', 'order']),
            'permissions'   => $permissions->paginate($request->perPage ?? 10),
        ]);
    }

    public function store(PermissionStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::create([
                'name'          => $request->name
            ]);
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->givePermissionTo([$request->name]);
            DB::commit();
            return back()->with('success', $permission->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' .  $th->getMessage());
        }
    }

    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        DB::beginTransaction();
        try {
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->revokePermissionTo([$permission->name]);
            $permission->update([
                'name'          => $request->name
            ]);
            $superadmin->givePermissionTo([$permission->name]);
            DB::commit();
            return back()->with('success',  $permission->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' .  $th->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        DB::beginTransaction();
        try {
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->revokePermissionTo([$permission->name]);
            $permission->delete();
            DB::commit();
            return back()->with('success', $permission->name. ' deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error deleting ' . $permission->name . $th->getMessage());
        }
    }
}
