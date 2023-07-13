<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $_REQUEST;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function existEmail()
    {
        $email = $this->request->input('email');

        $user = User::where('email', $email)
                    ->first();

        $response = "";
        ($user) ? $response = "exist" : $response = "not_exists";

        return response()->json([
            'response' => $response
        ]);
    }

    public function activationCode($token)
    {
        $user = User::where('activation_token', $token)->first();

        if(!$user)
        {
            return redirect()->route('login')->with('danger', 'This token doesn\'t match any user.');
        }

        if($this->request->isMethod('post'))
        {

            $code = $user->activation_code;

            $activation_code = $this->request->input('activation-code');

            if($activation_code != $code)
            {
                return back()->with([
                    'danger' => 'This activation code is invalid!',
                    'activation_code' => $activation_code
                ]);
            }
            else
            {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_verified' => 1,
                        'activation_code' => '',
                        'activation_token' => '',
                        'email_verified_at' => new \DateTimeImmutable,
                        'updated_at' => new \DateTimeImmutable
                    ]);

                    return redirect()->route('login')->with('success', 'Your email address has been verified');

            }

        }

        return view('auth.activation_code', [
            'token' => $token
        ]);
    }

    /***
     * vérifier si l'utilisateur a déjà activé son compte ou pas
     * avant d'être authentifié
     */
    public function userChecker()
    {
        $activation_token = Auth::user()->activation_token;
        $is_verified = Auth::user()->is_verified;

        if($is_verified != 1)
        {
            Auth::logout();
            return redirect()->route('app_activation_code', ['token' => $activation_token] )
                                ->with('warning', 'Your account is not activate yet, please check your mail-box and activate
                                your account or resend the confirmation message.');
        }
        else
        {
            return redirect()->route('app_dashboard');
        }
    }

    public function resendActivationCode($token)
    {
        $user = User::where('activation_token', $token)->first();
        $email = $user->email;
        $name = $user->name;
        $activation_token = $user->activation_token;
        $activation_code = $user->activation_code;

        $emailsend = new EmailService;
        $subject = "Activat your Account";
        $message = view('mail.configuration_email')
                        ->with([
                            'name' => $name,
                            'activation_code' => $activation_code,
                            'activation_token' => $activation_token
                        ]);

        $emailsend->sendEmail($subject, $email, $name, true, $activation_code, $activation_token);

        return redirect()->route('app_activation_code',
                ['token'=>$token])
                ->with('success','Your have just resend the new activation code');


    }

    public function activationAccountLink($token)
    {
        $user = User::where('activation_token', $token)->first();
        if(!$user)
        {
            return redirect()->route('login')->with('danger', 'This token doesn\'t match any user.');
        }

        DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'is_verified' => 1,
                        'activation_code' => '',
                        'activation_token' => '',
                        'email_verified_at' => new \DateTimeImmutable,
                        'updated_at' => new \DateTimeImmutable
                    ]);

        return redirect()->route('login')->with('success', 'Your email address has been verified');

    }

    public function ActivationAccoutChangeEmail($token)
    {
        $user = User::where('activation_token', $token)->first();


        if($this->request->isMethod('post'))
        {
            $new_email = $this->request->input('new-email');
            $user_existe = User::where('email', $new_email)->first();


            if($user_existe)
            {
                return back()->with([
                    'danger' => 'This address email is already used! Please enter another email address',
                    'new_email' => $new_email
                ]);
            }
            else
            {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'email' => $new_email,
                        'updated_at' => new \DateTimeImmutable
                    ]);

                $activation_code = $user->activation_code;
                $activation_token = $user->activation_token;
                $name = $user->name;

                $emailsend = new EmailService;
                $subject = "Activat your Account";

                $emailsend->sendEmail($subject, $new_email, $name, true, $activation_code, $activation_token);

                return redirect()->route('app_activation_code',[
                    'token'=>$token])
                ->with('success','Your have just resend the new activation code');



            }
        }
        else
        {
            return view('auth.activation_account_changemail',[
                'token' => $token
            ]);
        }

    }

    public function forgotPassword()
    {
        //si la requête est de type post
        if($this->request->isMethod('post'))
        {
            $email = $this->request->input('email-send');
            $user = DB::table('users')->where('email', $email)->first();
            $message = null;

            if($user)
            {
                $full_name = $user->name;
                //on va générer un token pour la reinitialisation du mot de passe de l'utilisateur
                $activation_token = md5(uniqid()) . $email . sha1($email);

                $emailrestpwd = new EmailService;
                $subject = "Reset your password";
                $emailrestpwd->resetPassword($subject, $email, $full_name,true, $activation_token);

                DB::table('users')
                        ->where('email', $email)
                        ->update(['activation_token' =>$activation_token]);


                $message = 'We have just sent the request to reset your password, please check your mail-box';
                return back()->withErrors(['email-success' => $message])
                            ->with('old_email', $email)
                            ->with('success', $message);

            }
            else
            {
                $message = 'The email you entered does not exist';
                return back()->withErrors(['email-error' => $message])
                            ->with('old_email', $email)
                            ->with('danger', $message);
            }
        }
        return view('auth.forgot_password');
    }

    public function changePassword($token)
    {
        if($this->request->isMethod('post'))
        {
            $new_password = $this->request->input('new-password');
            $new_password_confirm = $this->request->input('new-password-confirm');
            $passwordLength = strlen($new_password);
            $message =  null;

            if($passwordLength > 8)
            {
                $message = 'Your passwords must be identicall';
                if($new_password == $new_password_confirm)
                {
                    $user = DB::table('users')->where('activation_token', $token)->first();

                    if($user)
                    {
                        $id_user = $user->id;
                        DB::table('users')
                            ->where('id', $id_user)
                            ->update([
                                'password' => Hash::make($new_password),
                                'updated_at' => new \DateTimeImmutable,
                                 'activation_token' => ''
                            ]);

                        return redirect()->route('login')->with('success', 'New password saved successfully!');
                    }
                    else
                    {
                        return back()->with('danger', 'This token does not match nay user');
                    }
                }
                else
                {
                    return back()->withErrors(['password-error-confirm' => $message, 'password-success' => 'success'])
                       ->with('danger', $message)
                       ->with('old-new-password-confirm', $new_password_confirm)
                       ->with('old-new-password', $new_password);
                }


            }
            else
            {
                $message = "Your password must be at least 8 characters!";
                return back()->withErrors(['password-error' => $message])
                       ->with('danger', $message)
                       ->with('old-new-password', $new_password);
            }
        }

        return view('auth.change_password', [
            'activation_token' => $token
        ]);
    }

}
