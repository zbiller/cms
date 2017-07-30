<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabeticallyByName($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Sort the query alphabetically by code.
     *
     * @param Builder $query
     */
    public function scopeAlphabeticallyByCode($query)
    {
        $query->orderBy('code', 'asc');
    }

    /**
     * @param $amount
     * @param $from
     * @param $to
     * @param int $precision
     * @return float
     */
    public static function convert($amount, $from, $to, $precision = 2)
    {
        $amount = (float)$amount;
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from == $to) {
            return round($amount, $precision);
        }

        $url = 'http://www.google.com/finance/converter?a=' . urlencode($amount) . '&from=' . urlencode($from) . '&to=' . urlencode($to);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

        $result = curl_exec($ch);

        curl_close($ch);

        $data = explode("bld>", $result);
        $data = explode($to, $data[1]);

        return round($data[0], $precision);
    }
}