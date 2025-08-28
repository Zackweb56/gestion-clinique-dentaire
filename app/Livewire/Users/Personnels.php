<?php

namespace App\Livewire\Users;

use Flux\Flux;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Helpers\FlashHelper;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class Personnels extends Component
{
    // User management properties
    public $search = '';
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRole = '';
    public $userId = null;
    public $isEditing = false;
    public $showPassword = false;
    public $selectedRoleFilter = '';

    // Role management properties
    public $roleSearch = '';
    public $roleName = '';
    public $selectedPermissions = [];
    public $roleId = null;
    public $isRoleEditing = false;
    public $isRoleInUse = false;
    public $loadingGroup = null;

    public $selectedRoleForPermissions = null;

    // Pagination settings
    public $perPage = 5;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->userId),
            ],
            'selectedRole' => 'required'
        ];

        if (!$this->isEditing) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        return $rules;
    }

    protected function roleRules()
    {
        return [
            'roleName' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
            'selectedPermissions' => 'array',
        ];
    }

    protected $messages = [
        // User messages
        'name.required' => 'Le nom est obligatoire.',
        'name.string' => 'Le nom doit être une chaîne de caractères.',
        'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'email.required' => 'L\'email est obligatoire.',
        'email.email' => 'L\'email doit être une adresse email valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'selectedRole.required' => 'Le rôle est obligatoire.',
        
        // Role messages
        'roleName.required' => 'Le nom du rôle est requis.',
        'roleName.unique' => 'Ce rôle existe déjà.',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        // Users query
        $users = User::with(['roles'])
            ->where('id', '!=', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
                
                if ($query->count() == 0) {
                    $query->orWhereHas('roles', fn($q) => 
                        $q->where('name', 'like', '%' . $this->search . '%')
                    );
                }
            })
            ->when($this->selectedRoleFilter, function ($query) {
                $query->whereHas('roles', fn($q) => 
                    $q->where('name', $this->selectedRoleFilter)
                );
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'usersPage');

        // Roles query
        $roles = Role::with(['permissions'])
            ->when($this->roleSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->roleSearch . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'rolesPage');

        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        // Get recent logins for activity feed (last 5 users who logged in)
        $recentLogins = User::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.users.personnels', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
            'recentLogins' => $recentLogins,
            'allRoles' => Role::pluck('name')->all() // For role filter
        ]);
    }

    // Password generation
    public function generatePassword()
    {
        $length = 12;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        $this->password = $password;
        $this->showPassword = true;
    }

    // User Management Methods
    public function createUser()
    {
        $this->resetForm();
        Flux::modal('create-user')->show();
    }

    public function editUser($userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        
        if ($user->hasRole('Administrateur') && !Auth::user()->hasRole('Administrateur')) {
            FlashHelper::danger('Les administrateurs ne peuvent pas être modifiés.');
            return;
        }
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRole = $user->roles->first()?->name ?? '';
        $this->isEditing = true;
        
        Flux::modal('edit-user')->show();
    }

    public function storeUser()
    {
        $this->validate();

        DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $user->assignRole($this->selectedRole);

            FlashHelper::success('Utilisateur ajouté avec succès.');
        });

        $this->closeUserModal();
        $this->resetForm();
    }

    public function updateUser()
    {
        $this->validate();

        $user = User::findOrFail($this->userId);
        
        if ($user->hasRole('Administrateur') && !Auth::user()->hasRole('Administrateur')) {
            FlashHelper::danger('Les administrateurs ne peuvent pas être modifiés.');
            return;
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            $user->syncRoles([$this->selectedRole]);

            FlashHelper::success('Utilisateur mis à jour avec succès.');
        });

        $this->closeUserModal();
        $this->resetForm();
    }

    public function confirmDeleteUser($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->hasRole('Administrateur') && !Auth::user()->hasRole('Administrateur')) {
            FlashHelper::danger('Les administrateurs ne peuvent pas être supprimés.');
            return;
        }

        $this->userId = $userId;
        Flux::modal('delete-confirmation-user')->show();
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->userId);
        
        if ($user->hasRole('Administrateur') && !Auth::user()->hasRole('Administrateur')) {
            FlashHelper::danger('Les administrateurs ne peuvent pas être supprimés.');
            return;
        }

        $user->delete();

        FlashHelper::success('Utilisateur supprimé avec succès.');

        $this->closeUserModal();
        $this->resetForm();
    }

    // Role Management Methods
    public function createRole()
    {
        $this->resetRoleForm();
        Flux::modal('create-role')->show();
    }

    public function editRole($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        
        if ($role->name === 'Administrateur') {
            FlashHelper::danger('Le rôle d\'administrateur ne peut pas être modifié.');
            return;
        }
        
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isRoleEditing = true;
        
        Flux::modal('edit-role')->show();
    }

    public function storeRole()
    {
        $this->validate($this->roleRules(), $this->messages);

        DB::transaction(function () {
            $role = Role::create(['name' => $this->roleName]);
            
            $validPermissions = Permission::whereIn('name', $this->selectedPermissions)->pluck('name');
            $role->syncPermissions($validPermissions);

            FlashHelper::success('Rôle créé avec succès.');
        });

        $this->closeRoleModal();
        $this->resetRoleForm();
    }

    public function updateRole()
    {
        $this->validate($this->roleRules(), $this->messages);

        $role = Role::findOrFail($this->roleId);
        
        if ($role->name === 'Administrateur') {
            FlashHelper::danger('Impossible de mettre à jour le rôle d\'administrateur.');
            return;
        }

        DB::transaction(function () use ($role) {
            $role->update(['name' => $this->roleName]);
            
            $validPermissions = Permission::whereIn('name', $this->selectedPermissions)->pluck('name');
            $role->syncPermissions($validPermissions);

            FlashHelper::success('Rôle mis à jour avec succès.');
        });

        $this->closeRoleModal();
        $this->resetRoleForm();
    }

    public function confirmDeleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        if ($role->name === 'Administrateur') {
            FlashHelper::danger('Le rôle d\'administrateur ne peut pas être supprimé.');
            return;
        }
        
        $this->roleId = $roleId;
        $this->isRoleInUse = $role->users()->count() > 0;
        Flux::modal('delete-confirmation-role')->show();
    }

    public function deleteRole()
    {
        $role = Role::findOrFail($this->roleId);
        
        if ($role->name === 'Administrateur') {
            FlashHelper::danger('Le rôle d\'administrateur ne peut pas être supprimé.');
            return;
        }

        if ($role->users()->count() > 0) {
            FlashHelper::danger('Impossible de supprimer le rôle assigné aux utilisateurs.');
            return;
        }
        
        $role->delete();

        FlashHelper::success('Rôle supprimé avec succès.');

        $this->closeRoleModal();
        $this->resetRoleForm();
    }

    // Permission Management Methods
    public function toggleGroupPermissions($group, $checked)
    {
        $this->loadingGroup = $group;
        
        try {
            $groupPermissions = Permission::where('group', $group)->pluck('name')->toArray();
            
            if ($checked) {
                $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissions));
            } else {
                $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissions);
            }
        } finally {
            $this->loadingGroup = null;
        }
    }

    public function showRolePermissions($roleId)
    {
        $this->selectedRoleForPermissions = Role::with('permissions')->findOrFail($roleId);
        Flux::modal('role-permissions')->show();
    }

    // Utility Methods
    public function resetForm()
    {
        $this->reset([
            'name',
            'email',
            'password',
            'selectedRole',
            'userId',
            'isEditing',
            'showPassword'
        ]);
        $this->resetValidation();
    }

    public function resetRoleForm()
    {
        $this->reset([
            'roleName',
            'selectedPermissions',
            'roleId',
            'isRoleEditing',
            'isRoleInUse'
        ]);
        $this->resetValidation();
    }

    public function closeUserModal()
    {
        Flux::modal('create-user')->close();
        Flux::modal('edit-user')->close();
        Flux::modal('delete-confirmation-user')->close();
    }

    public function closeRoleModal()
    {
        Flux::modal('create-role')->close();
        Flux::modal('edit-role')->close();
        Flux::modal('delete-confirmation-role')->close();
    }

    // Search and pagination methods
    public function updatedSearch()
    {
        $this->resetPage('usersPage');
    }

    public function updatedRoleSearch()
    {
        $this->resetPage('rolesPage');
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}