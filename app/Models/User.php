<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasPanelShield;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    // Relations for created/updated records
    public function createdCountries()
    {
        return $this->hasMany(Country::class, 'created_by');
    }

    public function createdAirlines()
    {
        return $this->hasMany(Airline::class, 'created_by');
    }

    public function createdAirports()
    {
        return $this->hasMany(Airport::class, 'created_by');
    }

    public function createdHotels()
    {
        return $this->hasMany(Hotel::class, 'created_by');
    }

    public function createdConferences()
    {
        return $this->hasMany(Conference::class, 'created_by');
    }

    public function updatedConferences()
    {
        return $this->hasMany(Conference::class, 'updated_by');
    }

    public function createdTravelBookings()
    {
        return $this->hasMany(TravelBooking::class, 'created_by');
    }

    public function createdSessions()
    {
        return $this->hasMany(ConferenceSession::class, 'created_by');
    }

    public function uploadedAttachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }

    public function createdCorrespondences()
    {
        return $this->hasMany(Correspondence::class, 'created_by');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTransactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function managedMediaCampaigns()
    {
        return $this->hasMany(MediaCampaign::class, 'manager_user_id');
    }

    public function createdPressReleases()
    {
        return $this->hasMany(PressRelease::class, 'created_by');
    }

    public function createdCommittees()
    {
        return $this->hasMany(Committee::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_user_id');
    }

    public function createdReviews()
    {
        return $this->hasMany(Review::class, 'created_by');
    }
}
