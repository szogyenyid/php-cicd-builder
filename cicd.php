<?php

use MyProject\CICD\CodingStandard;
use MyProject\CICD\CompileSCSS;
use MyProject\CICD\ComposerInstall;
use MyProject\CICD\DeployToServer;
use MyProject\CICD\MinifyJS;
use MyProject\CICD\PHPStan;
use MyProject\CICD\PHPSyntaxCheck;
use MyProject\CICD\PHPUnit;
use szogyenyid\PhpCicdBuilder\CICD;
use szogyenyid\PhpCicdBuilder\Constants\Branch;
use szogyenyid\PhpCicdBuilder\Constants\Trigger;
use szogyenyid\PhpCicdBuilder\Pipeline;
use szogyenyid\PhpCicdBuilder\Script;
use szogyenyid\PhpCicdBuilder\Step;
use szogyenyid\PhpCicdBuilder\Strategies\Bitbucket;
use szogyenyid\PhpCicdBuilder\Variables;

include 'vendor/autoload.php';

# --- Variables ---

$environments = (new Variables("Environment"))
    ->withDefault("spartafyapp/")
    ->withAllowedValues(["spartafyapp/", "spartafyfeature/", "spartafyhotfix/", "spartafypreprod/"]);

# --- Default steps ---

$analyze = (new Step("Static Analysis & Coding standards"))
    ->withScript(new PHPSyntaxCheck())
    ->withScript(new PHPStan())
    ->withScript(new CodingStandard());

$unitTests = (new Step("Unit tests"))
    ->withScript(new PHPUnit());

$compileAndDeploy = function (...$params): Step {
    return (new Step("Compile and Deploy"))
        ->withScript(new MinifyJS())
        ->withScript(new CompileSCSS())
        ->withScript(new ComposerInstall())
        ->withScript(new DeployToServer(...$params));
};

# --- Build the CI/CD process from this point ----

(new CICD("alpine:latest", new Bitbucket()))
    ->withInitScript(Script::simple("apk update && apk upgrade"))
    ->withPipeline(
        Trigger::CUSTOM,
        (new Pipeline("force-redeploy"))
            ->withVariables($environments)
            ->withStep($unitTests)
            ->withStep($analyze)
            ->withStep($compileAndDeploy($environments, false, true))
    )
    ->withPipelines(
        Trigger::BRANCH,
        (new Pipeline(Branch::FEATURE))
            ->withStep($compileAndDeploy("spartafyfeature/", true)),
        (new Pipeline(Branch::HOTFIX))
            ->withStep($analyze)
            ->withStep($unitTests)
            ->withStep($compileAndDeploy("spartafyhotfix/")),
        (new Pipeline(Branch::MASTER))
            ->withStep($analyze)
            ->withStep($unitTests)
            ->withStep($compileAndDeploy("spartafypreprod/"))
            ->withManualStep($compileAndDeploy("spartafyapp/"))
    )
    ->withPipeline(
        Trigger::PR,
        (new Pipeline(Branch::ANY))
            ->withStep($unitTests)
            ->withStep($analyze)
    )
    ->writeToFile("bitbucket-pipelines.yml");
