<?php

trait AccountActivationTrait
{
    /**
     * @param $user
     * @param $email
     * @param $first_name
     * @param $last_name
     */
    protected function sendActivationEmail($user, $email, $first_name, $last_name)
    {
        // Let's get the activation code
        $activationCode = $user->getActivationCode();

        $receiver = $email;
        $subject = 'Account activation';
        $data = [
            'code' => $activationCode,
            'username' => $first_name . ' ' . $last_name
        ];

        // please queue this...
        // configure app to use amazon SQs, or just a plain DB for laravel jobs
        // I cant do this right now
        Mail::send('emails.auth.register', compact('data'), function ($m) use ($receiver, $subject) {
            $m->to($receiver);
            $m->subject($subject);
        });
    }

    /**
     * @param $code
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function activateUserAccount($code){

        $user = null;
        try{
            $user = Sentry::findUserByActivationCode($code);
        } catch (Exception $e){

            return Redirect::route('login.get');
        }

        try{
            if($user->attemptActivation($code)){

                $this->flash('success', 'Your account has been activated. Please login to continue');

                return Redirect::route('login.get');
            }
        } catch (\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e){

            $this->flash('error', 'Your account is already activated');

            return Redirect::back();
        }

    }
}