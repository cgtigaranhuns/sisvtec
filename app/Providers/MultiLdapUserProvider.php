<?php

namespace App\Providers;

use Illuminate\Support\Str;
use App\Models\AdmUser;
use App\Models\LabsUser;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;

class MultiLdapUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        $user = User::where('id', $identifier)->first();
        
        return $user && $user->getRememberToken() && hash_equals($user->getRememberToken(), $token)
            ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            return null;
        }
    
        // Obtém a conexão selecionada no formulário (padrão: 'adm')
        $connection = $credentials['connection'] ?? 'adm';
        
        \Log::debug("Tentando autenticar na conexão: " . $connection);
    
        // Busca o usuário na conexão especificada
        $ldapUser = $this->findLdapUser($credentials['username'], $connection);
        
        if (!$ldapUser) {
            \Log::debug("Usuário não encontrado na conexão: " . $connection);
            return null;
        }
    
        return $this->getOrCreateLocalUser($ldapUser, $credentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (!$user instanceof LdapAuthenticatable) {
            return false;
        }
    
        // Usa a conexão especificada no formulário
        $connection = $credentials['connection'] ?? 'adm';
        
        return $this->authenticateInLdap($user, $credentials, $connection);
    }
protected function findLdapUser($username, $connection)
{
    \Log::info("Iniciando busca LDAP", [
        'username' => $username,
        'base' => $connection
    ]);
    if ($connection === 'adm') {
        $model = AdmUser::class;
        $baseDn = 'cn=Users,dc=adm,dc=garanhuns,dc=ifpe';
        $query = $model::query()
            ->in($baseDn)
            ->where('samaccountname', '=', $username);
            $user = $query->first();

        if ($user) {
            \Log::info("Usuário encontrado", [
                'dn' => $user->getDn(),
                'base' => $connection
            ]);
            return $user;
        }

    } else {
        $model = LabsUser::class;
        $baseDn = 'ou=Discentes,dc=labs,dc=garanhuns,dc=ifpe';
        \Log::debug("Buscando usuário na conexão: {$connection}");
        $query = (new $model)->on($connection); // Cria instância com conexão correta
    
        $query2 = $query->where('samaccountname', '=', $username)
                    ->in($baseDn);
                    $user = $query2->first();
                  //   ->orWhere('userprincipalname', '=', $username);
          
        if ($user) {
            \Log::info("Usuário encontrado", [
                'dn' => $user->getDn(),
                'base' => $connection
            ]);
            return $user;
        }
    } 
   
}
    
protected function authenticateInLdap($user, $credentials, $connection)
{
    \Log::info("Iniciando autenticação LDAP", [
        'username' => $credentials['username'],
        'connection' => $connection
    ]);

    $ldapUser = $this->findLdapUser($credentials['username'], $connection);
    
    if (!$ldapUser) {
        \Log::warning("Usuário LDAP não encontrado");
        return false;
    }

    \Log::debug("Tentando autenticar com DN", ['dn' => $ldapUser->getDn()]);
    $result = $ldapUser->getConnection()->auth()->attempt(
        $ldapUser->getDn(),
        $credentials['password']
    );

    \Log::info("Resultado da autenticação", ['sucesso' => $result]);
    return $result;
}
protected function getOrCreateLocalUser($ldapUser, $credentials)
{
    \Log::info("Processando usuário local", [
        'username' => $credentials['username'],
        'email' => $ldapUser->getFirstAttribute('mail')
    ]);

    $user = User::where('email', $ldapUser->getFirstAttribute('mail'))
        ->orWhere('username', $credentials['username'])
        ->first();
        
    if ($user) {
        \Log::debug("Usuário local existente encontrado", ['id' => $user->id]);
        
        // Atualiza os dados do usuário existente com as informações do LDAP
        $updated = false;
        
        if ($user->name !== $ldapUser->getFirstAttribute('description')) {
            $user->name = $ldapUser->getFirstAttribute('description');
            $updated = true;
        }
        
        $ldapEmail = $ldapUser->getFirstAttribute('mail') ?? $credentials['username'] . '@garanhuns.ifpe';
        if ($user->email !== $ldapEmail) {
            $user->email = $ldapEmail;
            $updated = true;
        }
        
        if ($user->username !== $credentials['username']) {
            $user->username = $credentials['username'];
            $updated = true;
        }
        
        if ($updated) {
            $user->save();
            \Log::info("Dados do usuário atualizados", ['id' => $user->id]);
        } else {
            \Log::debug("Nenhuma alteração necessária nos dados do usuário", ['id' => $user->id]);
        }
        
        return $user;
    }
    
    \Log::info("Criando novo usuário local");
    $newUser = new User();
    $newUser->name = $ldapUser->getFirstAttribute('description');
    $newUser->email = $ldapUser->getFirstAttribute('mail') ?? $credentials['username'] . '@garanhuns.ifpe';
    $newUser->username = $credentials['username'];
    $newUser->password = bcrypt(Str::random(16));
    $newUser->save();
    
    \Log::info("Novo usuário criado com sucesso", ['id' => $newUser->id]);
    return $newUser;
}
}