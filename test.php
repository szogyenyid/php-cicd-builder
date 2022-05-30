<?php

use MyProject\CICD\CodingStandard;
use MyProject\CICD\CompileSCSS;
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

include 'vendor/autoload.php';

# --- Steps ---

$analyze = (new Step("Static Analysis & Coding standards"))
    ->withScript(new PHPSyntaxCheck())
    ->withScript(new PHPStan())
    ->withScript(new CodingStandard());

$unitTests = (new Step("Unit tests"))
    ->withScript(new PHPUnit());

$compileAssets = (new Step("Compile and Deploy"))
    ->withScript(new MinifyJS())
    ->withScript(new CompileSCSS());

function stepWithDeploy(Step $step, ...$params): Step
{
    $s = clone $step;
    return $s->withScript(new DeployToServer(...$params));
}

# --- Build the CI/CD process from this point ---

$cicd = (new CICD("alpine:latest"))
    ->withInitScript(Script::simple("apk update && apk upgrade"))
    ->withPipeline(
        Trigger::CUSTOM,
        (new Pipeline("migrate"))
            ->withStep($unitTests)
            ->withStep($analyze)
            ->withStep(stepWithDeploy($compileAssets, "spartafy.hu/spartafy/"))
    )
    ->withPipeline(
        Trigger::BRANCH,
        (new Pipeline(Branch::FEATURE))
            ->withStep($unitTests)
            ->withStep(stepWithDeploy($compileAssets, "spartafyfeature/", true))
    )
    ->withPipeline(
        Trigger::BRANCH,
        (new Pipeline(Branch::HOTFIX))
            ->withStep($unitTests)
            ->withStep($analyze)
            ->withStep(stepWithDeploy($compileAssets, "spartafyhotfix/"))
    )
    ->withPipeline(
        Trigger::BRANCH,
        (new Pipeline(Branch::MASTER))
            ->withStep($unitTests)
            ->withStep($analyze)
            ->withStep(stepWithDeploy($compileAssets, "spartafypreprod/"))
    )
    ->withPipeline(
        Trigger::PR,
        (new Pipeline(Branch::DEFAULT))
            ->withStep($unitTests)
            ->withStep($analyze)
    )
    ->writeToFile("bitbucket-pipelines.yml");
