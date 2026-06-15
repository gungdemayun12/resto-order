<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'icon',
        'color',
        'action_url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public static function createForUsers(array $userIds, array $data)
    {
        foreach ($userIds as $userId) {
            static::create(array_merge($data, ['user_id' => $userId]));
        }
    }

    public static function createForRole(string $role, array $data)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            static::create(array_merge($data, ['user_id' => $user->id]));
        }
    }
}
