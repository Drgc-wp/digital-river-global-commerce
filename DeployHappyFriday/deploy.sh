#!/bin/sh
UserHome="/home/ubuntu"
WorkDir="/home/ubuntu/DRWPAutoTest/DeployHappyFriday"
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

DRPluginRoot="/opt/lampp/htdocs/wordpress/wp-content/plugins/digital-river-global-commerce/"

echo "Delete old DR plugin in $DRPluginRoot"
sudo rm -rf $DRPluginRoot
echo "Copy new DR plugin into $DRPluginRoot"
sudo cp -r $ProjectPath $DRPluginRoot

sudo chown -R ubuntu $DRPluginRoot
cd $DRPluginRoot && pwd && npm install && gulp adminJS && gulp publicJS

# Cleaning cloned project
rm -rf $ProjectPath
