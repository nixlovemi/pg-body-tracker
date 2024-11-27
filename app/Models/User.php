<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\ValidatePassword;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    // relations
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, 'Usuário', 'id', 'ID');
        $validation->addField('name', ['required', 'string', 'min:3', 'max:255'], 'Nome');
        $validation->addEmailField('email', 'E-mail', ['required', 'string', 'min:3', 'max:255']);
        $validation->addField('password', ['required', 'string', 'min:8', 'max:255', function ($attribute, $value, $fail) {
            $ValidadePwd = new ValidatePassword($value);
            $retValidate = $ValidadePwd->validate();
            if (true === $retValidate->isError()) {
                $fail($retValidate->getMessage());
            }
        }], 'Senha');
        $validation->addField('role', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::USER_ROLES)) {
                $fail("O campo \"Cargo\" contém um valor inválido!");
            }
        }], 'Cargo');

        return $validation->validate();
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
    // ===============
}
