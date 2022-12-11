<?php

namespace MyProject\CICD;

use Soegaeni\PhpCicdBuilder\Script;

class DeployToServer extends Script
{
    public function __construct(
        string $path,
        bool $appendFeatureBranchName = false,
        bool $force = false,
        string $ftpUser = '$FTP_USERNAME',
        string $ftpPassword = '$FTP_PASSWORD',
        string $ftpHost = '$FTP_HOST'
    ) {
        $this->addInitCommand('apk add bash');
        $this->addInitCommand('apk add curl');
        $this->addInitCommand('apk add git');
        $this->addInitCommand('apk add openssh');
        if ($appendFeatureBranchName) {
            $this->addInitCommand('featureBranchName=$(echo $BITBUCKET_BRANCH | cut -d \'/\' -f2)');
        }
        $this->addInitCommand('git submodule update --init --recursive');
        $this->addCommand('curl https://raw.githubusercontent.com/git-ftp/git-ftp/master/git-ftp > /bin/git-ftp');
        $this->addCommand('chmod 755 /bin/git-ftp');
        $this->addCommand('
            git ftp push
            --auto-init
            ' . ($force ? '--force' : '') . '
            -u "' . $ftpUser . '"
            -p "' . $ftpPassword . '"
            ftp://' . $ftpHost . '/' . $path . '' . ($appendFeatureBranchName ? '${featureBranchName}/' : '') . '
        ');
    }
}
