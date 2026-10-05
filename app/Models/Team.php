<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'team_lead_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'marketing_team',
        'is_b2b',
        'is_postsale',
    ];

    /**
     * Get the team lead that owns the team.
     */
    public function teamLead()
    {
        return $this->belongsTo(User::class , 'team_lead_id');
    }

    /**
     * Get the users for the team.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class , 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class , 'deleted_by');
    }

    /**
     * Get the details for the team.
     */
    public function detail()
    {
        return $this->hasOne(TeamDetail::class);
    }

    /**
     * Scope to exclude marketing teams
     */
    public function scopeNonMarketing($query)
    {
        return $query->where('marketing_team', false)->orWhereNull('marketing_team');
    }

    /**
     * Backward compatible title accessor (legacy code expects title field).
     */
    public function getTitleAttribute(): ?string
    {
        return $this->name;
    }

    /**
     * Override the delete method to set deleted_by
     */
    public function delete()
    {
        $this->deleted_by = \App\Helpers\AuthHelper::getCurrentUserId();
        $this->save();

        return parent::delete();
    }
}
