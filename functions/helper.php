<?php
function getImage($imageName)
{
    $path = 'images/';
    $defaultImage = 'foto11.jpeg';

    if (!empty($imageName) && file_exists($path . $imageName)) {
        return $path . $imageName;
    }

    return $path . $defaultImage;
}
