<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\Validator;
use App\Models\Utilisateur;

class EmailVerifyRequest extends FormRequest
{

    public $user;
    /**
     * Class constructor.
     */
    public function __construct()
    {

    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->user = Utilisateur::find($this->route('id'));

        if (! hash_equals((string) $this->user->getKey(), (string) $this->route('id'))) {
            return false;
        }

        if (! hash_equals(sha1($this->user->getEmailForVerification()), (string) $this->route('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function fulfill()
    {
        if (!$this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();

            event(new Verified($this->user));
        }
    }

    public function withValidator(Validator $validator)
    {
        return $validator;
    }
}
