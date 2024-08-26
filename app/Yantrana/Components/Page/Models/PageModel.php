<?php

/**
 * Page.php - Model file
 *
 * This file is part of the Page component.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\Components\Page\Models;

use App\Yantrana\Base\BaseModel;

class PageModel extends BaseModel
{
    /**
     * @var string - The database table used by the model.
     */
    protected $table = 'pages';

    /**
     * @var array - The attributes that should be casted to native types.
     */
    protected $casts = [
        'type' => 'integer',
    ];

    /**
     * @var array - The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',        'show_in_menu',        'content',        'type',
    ];
}
