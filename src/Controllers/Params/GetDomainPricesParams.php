<?php

namespace App\Controllers\Params;

use yii\base\Model;

class GetDomainPricesParams extends Model
{
    /** @var string */
    public $search;

    public function rules()
    {
        return [
            ['search', 'required'],
            ['search', 'filter', 'filter'=>'trim'],
            ['search', 'filter', 'filter'=>'mb_strtolower'],
            ['search', 'match', 'pattern' => '/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]){1}$/']
        ];
    }

}
