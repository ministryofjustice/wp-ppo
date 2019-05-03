#!/usr/bin/env bash

docker rmi $(docker images -a --filter=dangling=true -q)

docker rm $(docker ps --filter=status=exited --filter=status=created -q)
