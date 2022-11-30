<?php

namespace Soegaeni\PhpCicdBuilder\Constants;

class Trigger
{
    public const DEFAULT = "default";
    public const BRANCH = "branches";
    public const TAG = "tags";
    public const BOOKMARK = "bookmarks";
    public const CUSTOM = "custom";
    public const PR = "pull-requests";
    public const MANUAL = "manual";
}
