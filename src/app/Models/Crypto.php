<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
{
    use HasFactory;

    protected $table = 'cryptos';

    protected $fillable = ['symbol', 'name'];

    protected $casts = [
        'percent_change' => 'float',
    ];

    public function numberFormat($attribute)
    {
        return number_format((float)rtrim($this->$attribute, '0'), 2, ',', '.');
    }

    public function _dailyEarn()
    {
        return number_format(round(-1000 + (1000 / $this->start * $this->price), 2), 2, ',', '.');
    }

    public function link()
    {
        echo "<a target='_blank' href='https://www.binance.com/en/trade/". strtoupper($this->name)."_USDT?layout=pro'>$this->name</a>";
    }
}
