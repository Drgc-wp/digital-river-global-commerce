#/bin/sh
ContainerName="docker_wordpress-testcafe"

{ # try
    echo ">> Try to kill and remove container $ContainerName"
    docker kill $ContainerName
    docker rm $ContainerName
} || { # catch
    echo ">> Container is not runnint..."
}
