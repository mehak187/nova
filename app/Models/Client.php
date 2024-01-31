<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Contact;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'has_subscription' => 'boolean',
        'door_access_enabled' => 'boolean',
        'has_mail_notifications' => 'boolean',
        'has_unread_mail_notifications' => 'boolean',
        'credit_alet_sent' => 'boolean',
        'services' => 'json',
        'id_card_expiry_date' => 'date',
        'residence_permit_expiry_date' => 'date',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameReversedAttribute()
    {
        return $this->last_name . ' ' . $this->first_name;
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }

    public function mailNotifications(): HasMany
    {
        return $this->hasMany(MailNotification::class);
    }

    public function balanceChanges(): HasMany
    {
        return $this->hasMany(ClientBalanceChange::class);
    }

    public function doorOpenings()
    {
        return $this->hasMany(DoorOpening::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function creditAlertThresholdReached()
    {
        $totalPurchasedMinutes = $this->purchased_minutes_table + $this->purchased_minutes_office;
        $totalIncludedMinutes = $this->included_minutes_table + $this->included_minutes_office;

        return ($totalPurchasedMinutes > 0 && $totalPurchasedMinutes < 600)
            || ($totalIncludedMinutes > 0 && $totalIncludedMinutes < 300);
    }
}
