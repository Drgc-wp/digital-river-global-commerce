#!/bin/sh
LogFileName="/digital-river-global-commerce/junit.xml"
JestTestPath="/digital-river-global-commerce/tests/jest"

WorkDir="/home/ubuntu/DRWPAutoTest/UniTestInDocker"
ContainerName="docker_drwp_unittest"
JestConfigFile="/digital-river-global-commerce/package.json"

echo ">>>>>> Start testing..."
if [ $1 = "unit" ] ; then
    echo ">> Start to Unit Test..."
    docker exec $ContainerName sh -c "cd $JestTestPath && pwd && npm install"
    docker exec $ContainerName sh -c "sed -i 's/\"jest\": {/\"jest\": {\n    \"reporters\": [ \"default\", \"jest-junit\" ],/g' $JestConfigFile"
    docker exec $ContainerName sh -c "cd $JestTestPath && npm run test:jest"
else
   echo "None of the condition met"
fi

# Copy log file from docker to happy-friday
sleep 1
echo ">>>>>> Copying log file..."
docker cp $ContainerName:$LogFileName $WorkDir
