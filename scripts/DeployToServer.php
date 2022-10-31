<?php

namespace MyProject\CICD;

use Soegaeni\PhpCicdBuilder\Script;

class DeployToServer extends Script
{
    public function __construct(
        string $path,
        bool $appendFeatureBranchName = false,
        string $ftpUser = '$FTP_USERNAME',
        string $ftpPassword = '$FTP_PASSWORD',
        string $ftpHost = '$FTP_HOST'
    ) {
        $this->addInitCommand('apk add bash');
        $this->addInitCommand('apk add curl');
        $this->addInitCommand('apk add git');
        $this->addInitCommand('apk add make');
        $this->addInitCommand('apk add openssh');
        # Source: https://github.com/dotsunited/docker-git-ftp/blob/master/Dockerfile
        $this->addInitCommand('
            git clone https://github.com/git-ftp/git-ftp.git /opt/git-ftp
            && cd /opt/git-ftp
            && tag="$(git tag | grep \'^[0-9]*\.[0-9]*\.[0-9]*$\' | tail -1)"
            && git checkout "$tag"
            && make install
            && rm -rf /opt/git-ftp
        ');
        if ($appendFeatureBranchName) {
            $this->addInitCommand('featureBranchName=$(echo $BITBUCKET_BRANCH | cut -d \'/\' -f2)');
        }
        $this->addInitCommand('apk add openssh');
        $this->addInitCommand('git submodule update --init --recursive');
        $this->addCommand('
            git ftp push
            --auto-init
            -u "' . $ftpUser . '"
            -p "' . $ftpPassword . '"
            ftp://' . $ftpHost . '/' . $path . '' . ($appendFeatureBranchName ? '${featureBranchName}/' : '') . '
        ');
    }
}
