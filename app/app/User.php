<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'location',
    ];

    public static function store(string $name, string $email, string $location): self
    {
        $user = new static;
        $user->name = $name;
        $user->email = $email;
        $user->location = $location;

        $user->save();

        return $user;
    }

}
