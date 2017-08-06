<?php

use App\Models\Cms\Email;
use Illuminate\Database\Seeder;

class EmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('emails')->delete();

        /**
         * Create the password reset email.
         */
        Email::create([
            'name' => 'Password Recovery',
            'identifier' => 'password-recovery',
            'type' => Email::TYPE_PASSWORD_RECOVERY,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Recover your password',
                'message' => '<p>Hello [full_name],</p>\r\n
    <p>You are receiving this email because we received a password reset request for your account.</p>\r\n
    <p><a class="button button-blue" href="[reset_password_url]" target="_blank" rel="noopener noreferrer">Reset Password</a></p>\r\n
    <p>If you did not request a password reset, no further action is required.</p>\r\n
    <hr />\r\n
    <p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the email verification email.
         */
        Email::create([
            'name' => 'Email Verification',
            'identifier' => 'email-verification',
            'type' => Email::TYPE_PASSWORD_RECOVERY,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Verify your email address',
                'message' => 'p>Hello [full_name],</p>\r\n
    <p>You have successfully registered on our site.<br />In order to sign in, you need to verify your email address.</p>\r\n
    <p><a class="button button-blue" href="[email_verification_url]" target="_blank" rel="noopener noreferrer">Verify Your Email Address Now</a></p>\r\n
    <p>Once this email address is verified, you will be able to access your account.</p>\r\n
    <hr />\r\n
    <p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the cart reminder email.
         */
        Email::create([
            'name' => 'Cart Reminder',
            'identifier' => 'cart-reminder',
            'type' => Email::TYPE_USER_CART_REMINDER,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'You have pending products in you cart',
                'message' => '<p>Hello [full_name],</p>\r\n
    <p>Your shopping cart still contains some products in it.</p>\r\n
    <p>&nbsp;</p>\r\n
    <p>[cart_contents]</p>\r\n
    <p>&nbsp;</p>\r\n
    <p>Please <a href="[home_url]" target="_blank" rel="noopener noreferrer">visit the site</a> to&nbsp;manage your ongoing shopping cart.</p>\r\n
    <hr />\r\n
    <p>Thank you!</p>',
            ],
        ]);
    }
}
