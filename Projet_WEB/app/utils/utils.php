<?php
function truncateFilename($filename, $max = 255)
{
    $dotPos = strrpos($filename, '.');

    if ($dotPos === false) {
        // Pas d’extension
        return mb_substr($filename, 0, $max, 'UTF-8');
    }

    $name = mb_substr($filename, 0, $dotPos, 'UTF-8');
    $ext  = mb_substr($filename, $dotPos, null, 'UTF-8');

    $maxNameLength = $max - mb_strlen($ext, 'UTF-8');

    if (mb_strlen($name, 'UTF-8') > $maxNameLength) {
        $name = mb_substr($name, 0, $maxNameLength, 'UTF-8');
    }

    return $name . $ext;
}
?>
