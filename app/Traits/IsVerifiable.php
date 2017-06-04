<?php

namespace App\Traits;

use Mail;
use Schema;
use Exception;
use ReflectionMethod;
use App\Options\VerifyOptions;
use App\Mail\EmailVerification;
use App\Exceptions\VerificationException;
use Illuminate\Database\Eloquent\Builder;

trait IsVerifiable
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\VerifyOptions file.
     *
     * @var VerifyOptions
     */
    protected static $verifyOptions;

    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function bootIsVerifiable()
    {
        self::checkVerifyOptions();

        self::$verifyOptions = self::getVerifyOptions();
    }

    /**
     * Filter query results to show only verified users.
     *
     * @param Builder $query
     */
    public function scopeOnlyVerified($query)
    {
        $query->where('verified', static::VERIFIED_YES);
    }

    /**
     * Filter query results to show only un-verified users.
     *
     * @param Builder $query
     */
    public function scopeOnlyUnVerified($query)
    {
        $query->where('verified', static::VERIFIED_NO);
    }

    /**
     * Check if the user is verified.
     *
     * @return boolean
     */
    public function isVerified()
    {
        return $this->verified === static::VERIFIED_YES;
    }

    /**
     * Check if the user verification is pending.
     *
     * @return boolean
     */
    public function isPendingVerification()
    {
        return !$this->isVerified() && $this->hasVerificationToken();
    }

    /**
     * Checks if the user has a verification token.
     *
     * @return bool
     */
    public function hasVerificationToken()
    {
        return $this->verification_token !== null;
    }

    /**
     * Generate a verification token and save it for the loaded model.
     * Making the model "pending verification". (has a token, but it's not yet verified)
     *
     * @return mixed
     * @throws VerificationException
     */
    public function generateVerificationToken()
    {
        $this->checkVerificationCompliance();

        if (empty($this->email)) {
            throw new VerificationException(
                'Could not generate an email verification token, because the user does not have any email!'
            );
        }

        $this->verified = static::VERIFIED_NO;
        $this->verification_token = hash_hmac('sha256', str_random(40), config('app.key'));

        return $this->save();
    }

    /**
     * Validate the supplied token against the loaded model.
     *
     * @param string $token
     * @return $this
     * @throws VerificationException
     */
    public function processVerificationToken($token)
    {
        unset($this->{"password"});

        if ($this->verified == static::VERIFIED_YES) {
            return $this;
        }

        if ($this->verification_token != $token) {
            throw new VerificationException(
                'Wrong verification token supplied!'
            );
        }

        $this->verified = static::VERIFIED_YES;
        $this->verification_token = null;

        return $this->save();
    }

    /**
     * Send the verification email containing the verifiable link.
     *
     * @return mixed
     * @throws VerificationException
     */
    public function sendVerificationEmail()
    {
        $this->checkVerificationCompliance();

        return Mail::to($this)->{self::$verifyOptions->enableQueue === true ? 'queue' : 'send'}(
            new EmailVerification('email-verification', $this)
        );
    }

    /**
     * Check if the targeted model is verifiable compliant.
     * That means that the model's database table should include 2 columns:
     *
     * *** The column names are configurable from the VerifyOptions class.
     *
     * verified --- tinyint, default 0.
     * verification_token --- varchar, default null.
     *
     * @return void
     * @throws VerificationException
     */
    private function checkVerificationCompliance()
    {
        if (!Schema::hasColumn($this->getTable(), 'verified')) {
            throw new VerificationException(
                'The model "' . get_class($this) . '" is not verifiable compliant!' . PHP_EOL .
                'Please update the "' . $this->getTable() . '" database table to include the "verified" column.'
            );
        }

        if (!Schema::hasColumn($this->getTable(), 'verification_token')) {
            throw new VerificationException(
                'The model "' . get_class($this) . '" is not verifiable compliant!' . PHP_EOL .
                'Please update the "' . $this->getTable() . '" database table to include the "verification_token" column.'
            );
        }
    }

    /**
     * Verify if the getCrudOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkVerifyOptions()
    {
        if (!method_exists(self::class, 'getVerifyOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getVerifyOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getVerifyOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getVerifyOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}