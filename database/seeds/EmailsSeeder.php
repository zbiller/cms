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
                'message' => '<p>Hello [full_name],</p><p>You are receiving this email because we received a password reset request for your account.</p><p><a class="button button-blue" href="[reset_password_url]" target="_blank" rel="noopener noreferrer">Reset Password</a></p><p>If you did not request a password reset, no further action is required.</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the email verification email.
         */
        Email::create([
            'name' => 'Email Verification',
            'identifier' => 'email-verification',
            'type' => Email::TYPE_EMAIL_VERIFICATION,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Verify your email address',
                'message' => '<p>Hello [full_name],</p><p>You have successfully registered on our site.<br />In order to sign in, you need to verify your email address.</p><p><a class="button button-blue" href="[email_verification_url]" target="_blank" rel="noopener noreferrer">Verify Your Email Address Now</a></p><p>Once this email address is verified, you will be able to access your account.</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the cart reminder email.
         */
        Email::create([
            'name' => 'Cart Reminder',
            'identifier' => 'cart-reminder',
            'type' => Email::TYPE_CART_REMINDER,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'You have pending products in you cart',
                'message' => '<p>Hello [full_name],</p><p>Your shopping cart still contains some products in it.</p><p>&nbsp;</p><p>[cart_contents]</p><p>&nbsp;</p><p>Please <a href="[home_url]" target="_blank" rel="noopener noreferrer">visit the site</a> to&nbsp;manage your ongoing shopping cart.</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the order created email.
         */
        Email::create([
            'name' => 'Order Created',
            'identifier' => 'order-created',
            'type' => Email::TYPE_ORDER_CREATED,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Thank you for your purchase',
                'message' => '<p>Hello [full_name],</p><p>Thank you for your purchase!</p><p>We will process your order as soon as possible and notify you of it\'s status.</p><p>Meanwhile, you can verify your order\'s details below:</p><p>Order ID: <strong>[order_id]<br /></strong>Order Status: <strong>[order_status]<br /><br /></strong></p><p>[order_contents]</p><p>&nbsp;</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the order completed email.
         */
        Email::create([
            'name' => 'Order Completed',
            'identifier' => 'order-completed',
            'type' => Email::TYPE_ORDER_COMPLETED,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Your order is completed',
                'message' => '<p>Hello [full_name],</p><p>Your order with the ID&nbsp;<strong>[order_id]</strong> is now&nbsp;<strong>completed</strong>.</p><p>Please review your order\'s details below.</p><p>&nbsp;</p><p>[order_contents]</p><p>&nbsp;</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the order completed email.
         */
        Email::create([
            'name' => 'Order Failed',
            'identifier' => 'order-failed',
            'type' => Email::TYPE_ORDER_FAILED,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Your order has failed',
                'message' => '<p>Hello [full_name],</p><p>Your order with the ID&nbsp;<strong>[order_id]</strong>&nbsp;has <strong>failed</strong>.</p><p>Please review your order\'s details below.</p><p>&nbsp;</p><p>[order_contents]</p><p>&nbsp;</p><hr /><p>Thank you!</p>',
            ],
        ]);

        /**
         * Create the order canceled email.
         */
        Email::create([
            'name' => 'Order Canceled',
            'identifier' => 'order-canceled',
            'type' => Email::TYPE_ORDER_CANCELED,
            'metadata' => [
                'from_name' => null,
                'from_email' => null,
                'reply_to' => null,
                'attachment' => null,
                'subject' => 'Your order has been canceled',
                'message' => '<p>Hello [full_name],</p><p>Your order with the ID&nbsp;<strong>[order_id]</strong>&nbsp;has been <strong>canceled</strong>.</p><p>Please review your order\'s details below.</p><p>&nbsp;</p><p>[order_contents]</p><p>&nbsp;</p><hr /><p>Thank you!</p>',
            ],
        ]);
    }
}
