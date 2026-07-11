<?php

namespace App\Modules\Authentication\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Title('Reset Password - Metrica Polls')]
class ResetPassword extends Component
{
    public $token = '';

    #[Url]
    public $email = '';

    public $password = '';
    public $password_confirmation = '';

    public function mount($token)
    {
        $this->token = $token;
        if (empty($this->email)) {
            return redirect()->route('login');
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (!$record || !Hash::check($this->token, $record->token)) {
            $this->addError('email', 'This password reset token is invalid or has expired.');
            return;
        }

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($this->password)
            ]);
        }

        DB::table('password_reset_tokens')->where('email', $this->email)->delete();

        session()->flash('success', 'Your password has been successfully reset. You can now log in.');

        return redirect()->route('login');
    }

    public function render()
    {
        return view('Authentication::livewire.reset-password')
            ->layout('Corporate::layout');
    }
}
