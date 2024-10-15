<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Models\Tecnico;

class UserQuery{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function usuarioTecnico($root , array $args){
        return User::with('tecnicos')->findOrFail($args['id']);
    }

}
