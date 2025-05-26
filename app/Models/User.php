<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AuthenticatesWithLdap, HasRoles;

     protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'email',
        'password',
        'cargo_id',
        'tipo_servidor',
        'coordenacao_id',
    ];

   

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getLdapDomain(): ?string
    {
        return $this->domain ?? 'adm';
    }

    /**
     * Get the LDAP GUID of the user.
     *
     * @return string
     */
    public function getLdapGuid(): string 
    {
        return $this->guid;
    }

    /**
     * Set the LDAP GUID of the user.
     *
     * @param string $guid
     * @return void
     */
    public function setLdapGuid($guid): void
    {
        $this->guid = $guid;
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function compesacaoDocente() {
        return $this->hasMany(CompensacaoDocenteNaoEnvolvido::class);
    }

    public function compesacaoTurma() {
        return $this->hasMany(CompensacaoTurmaNaoEnvolvido::class);
    }

    public function coordenador() {
        return $this->hasMany(Coordenacao::class);
    }

    public function coordenacao()
    {   
        return $this->belongsTo(Coordenacao::class);
    }

    public function visitaTecnica()
    {
        return $this->hasMany(VisitaTecnica::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }



    

}