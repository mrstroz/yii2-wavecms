<?php

namespace mrstroz\wavecms\components\helpers;

class FontAwesome
{

    public static function icon($icon)
    {
        return '<i class="fa fa-' . $icon . '" aria-hidden="true"></i>';
    }


}