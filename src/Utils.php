<?php

namespace cjrasmussen\BlueskyApi;

class Utils
{
    public static function getRecordKeyFromRecordUri(string $uri): ?string
    {
        $explode = explode('/', $uri);

        return end($explode);
    }

    public static function getRecordKeyFromRecord(object $record): ?string
    {
        if (isset($record->uri)) {
            return self::getRecordKeyFromRecordUri($record->uri);
        }

        return null;
    }
}
