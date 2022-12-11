<?php

namespace szogyenyid\PhpCicdBuilder\Constants;

class Branch
{
    public const MASTER = 'master';
    public const DEVELOPMENT = 'development';
    public const FEATURE = 'feature/*';
    public const HOTFIX = 'hotfix';
    public const ANY = '**';
}
