<?php

namespace mrstroz\wavecms\components\helpers;

class FontAwesome
{

    public static function icon($icon)
    {
        return '<i class="fas fa-fw fa-' . $icon . '" aria-hidden="true"></i>';
    }


}