#!/bin/sh
ContainerName="docker_wordpress-testcafe"
DockerDRPluginRoot="/opt/lampp/htdocs/docker_wordpress/wp-content/plugins/digital-river-global-commerce"
DockerTestCafePath="$DockerDRPluginRoot/tests/Testcafe"
TestP1="$DockerTestCafePath/fixtures/P1"
TestP2="$DockerTestCafePath/fixtures/P2"
TestP3="$DockerTestCafePath/fixtures/P3"
TestP3NonAdmin="$TestP3/api-env.js $TestP3/minicart-iPhone6.js $TestP3/searchbox-iPhone6.js"
Timestamp=$(date +"%Y-%m-%d-%H-%M")
LogFileName="report_${Timestamp}.xml"
LogFile="-r xunit:/${LogFileName}"

WorkDir="/home/ubuntu/DRWPAutoTest/TestcafeInDocker"

TestCafeConfig="$DockerTestCafePath/config.js"
TestCafeSiteConfig="$DockerTestCafePath/siteConfig.js"

# run gulp adminJS && gulp publicJS in dockers for minify JS tests
#docker exec $ContainerName sh -c "cd $DockerDRPluginRoot && pwd && npm install && gulp adminJS && gulp publicJS"

# Add testCafe SiteConfig
docker exec $ContainerName /bin/bash -c "sed -i \"s/siteID:     'SITE_ID'/siteID:     'drdod15'/g\" $TestCafeSiteConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/apiKey:     'API_KEY'/apiKey:     '99477953970e432da4d89b982f6bcc49'/g\" $TestCafeSiteConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/apiSecret:  'API_SECRET'/apiSecret:  'a4dccc3558ec4e09ae2879864f900f24'/g\" $TestCafeSiteConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/domainInput:  'DOMAIN_INPUT'/domainInput:  'api.digitalriver.com'/g\" $TestCafeSiteConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/pluginKey:  'PLUGIN_KEY'/pluginKey:  '6eb251a648bc4e6b87b24671262f2e91'/g\" $TestCafeSiteConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/Prod API URL/https:\/\/api.digitalriver.com/g\" $TestCafeConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/NonProd API URL/https:\/\/dispatch-test.digitalriver.com/g\" $TestCafeConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/username: 'username'/username: 'gcwpdemo'/g\" $TestCafeConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/password: 'password'/password: '33a5b9f5'/g\" $TestCafeConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/Dev Site Web Address/http:\/\/10.70.10.35:8080\/docker_wordpress/g\" $TestCafeConfig"
docker exec $ContainerName /bin/bash -c "sed -i \"s/Demo Site Web Address/http:\/\/gcwpdemo.wpengine.com/g\" $TestCafeConfig"

# Change config according to different testing environment
echo ">>>>>> Start testing..."
if [ $1 = "dev" ] ; then
    echo ">> DEV env, run all test in P1, P2, and P3"
    docker exec $ContainerName /bin/bash -c "sed -i \"s/dev: 'username'/dev: 'tracy'/g\" $TestCafeConfig"
    docker exec $ContainerName /bin/bash -c "sed -i \"s/dev: 'password'/dev: 'tracyadmin'/g\" $TestCafeConfig"
    docker exec $ContainerName /bin/bash -c "testcafe chrome:headless $TestP1 $TestP2 $TestP3 $LogFile"
elif [ $1 = "sys" ] ; then
    echo ">> SYS env, run all test in P1, P2, and P3"
    docker exec $ContainerName /bin/bash -c "sed -i \"s/env: 'dev'/env: 'sys'/g\" $TestCafeConfig"
    docker exec $ContainerName /bin/bash -c "testcafe chrome:headless $TestP1 $TestP2 $TestP3 $LogFile"
elif [ $1 = "demo" ] ; then
    echo ">> Demo env, run tests except admin related scripts"
    docker exec $ContainerName /bin/bash -c "sed -i \"s/env: 'dev'/env: 'demo'/g\" $TestCafeConfig"
    docker exec $ContainerName /bin/bash -c "testcafe chrome:headless $TestP1 $TestP2 $TestP3NonAdmin $LogFile"
elif [ $1 = "prod" ] ; then
    echo ">> Prod Env, run tests except admin related scripts"
    docker exec $ContainerName /bin/bash -c "sed -i \"s/env: 'dev'/env: 'prod'/g\" $TestCafeConfig"
    docker exec $ContainerName /bin/bash -c "testcafe chrome:headless $TestP1 $TestP2 $TestP3NonAdmin $LogFile"
else
   echo "None of the condition met"
fi

# Copy logfile from docker to local
docker cp $ContainerName:/${LogFileName} ${WorkDir}/${LogFileName}
