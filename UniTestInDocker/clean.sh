#/bin/sh
ContainerName="docker_drwp_unittest"

{ # try
    echo ">> Try to kill and remove container $ContainerName"
    docker kill $ContainerName
    docker rm $ContainerName
} || { # catch
    echo ">> Container is not running..."
}
