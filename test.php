<?php

use MyProject\CICD\CodingStandard;
use MyProject\CICD\CompileSCSS;
use MyProject\CICD\ComposerInstall;
use MyProject\CICD\DeployToServer;
use MyProject\CICD\MinifyJS;
use MyProject\CICD\PHPStan;
use MyProject\CICD\PHPSyntaxCheck;
use MyProject\CICD\PHPUnit;
use Soegaeni\PhpCicdBuilder\CICD;
use Soegaeni\PhpCicdBuilder\Constants\Branch;
use Soegaeni\PhpCicdBuilder\Constants\Trigger;
use Soegaeni\PhpCicdBuilder\Pipeline;
use Soegaeni\PhpCicdBuilder\Script;
use Soegaeni\PhpCicdBuilder\Step;
use Soegaeni\PhpCicdBuilder\Variables;

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

# --- Build the CI/CD process from this point ---

$cicd = (new CICD("alpine:latest"))
    ->withInitScript(Script::simple("apk update && apk upgrade"))
    ->withPipeline(
        Trigger::CUSTOM,
        (new Pipeline("force-redeploy"))
            ->withVariables($environments)
            ->withStep($unitTests)
            ->withStep($analyze)
            ->withStep($compileAndDeploy('$Environment', false, true))
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
