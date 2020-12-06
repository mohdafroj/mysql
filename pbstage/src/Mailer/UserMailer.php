<?php
namespace App\Mailer;
use Cake\Mailer\Mailer;
class UserMailer extends Mailer
{
	private $transport = 'SendGrid';
	private $from = 'connect@perfumebooth.com'; 
    public function welcome($user)
    {
        $this
            ->to($user->email)
            ->subject(sprintf('Welcome %s', $user->name))
            ->template('welcome_mail', 'custom'); // By default template with same name as method name is used.
    }

    public function resetPassword($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('Admin/User/reset_password')
            ->set($user);
	
    }
}