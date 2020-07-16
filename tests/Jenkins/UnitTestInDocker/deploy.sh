#!/bin/sh
WorkDir="/home/ubuntu/DRWPAutoTest/RunInDocker"
CloneTargetProject="git@github.digitalriverws.net:gc-templates/digital-river-global-commerce.git"
ProjectPath="$WorkDir/digital-river-global-commerce"
echo ">>>>>> Start deploying..."
# Clone DR Plugin
{ # try
    echo ">> Cloning Project into $ProjectPath ..."
    git clone $CloneTargetProject $ProjectPath
} || { # catch
    echo ">> removing proect from git..."
    rm -rf $ProjectPath
    echo ">> cloning project from git..."
    git clone $CloneTargetProject $ProjectPath
}

# Switch to develop branch
echo ">> Switch to development branch..."
cd $ProjectPath
git checkout -b development --track origin/development

# Run Docker
ContainerName="docker_drwp_unittest"
DockerImage="tracyw/drwp-unitest"
DockerDRPluginRoot="/digital-river-global-commerce"

echo "RUN docker wordpress-testcafe container"
docker run -itd --name $ContainerName $DockerImage
echo "Delete old DR plugin in Docker container"
docker exec $ContainerName rm -rf $DockerDRPluginRoot
echo "Copy new DR plugin into Docker container"
docker cp $ProjectPath $ContainerName:$DockerDRPluginRoot

# Cleaning cloned project
rm -rf $ProjectPath
