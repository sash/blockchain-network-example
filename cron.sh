#!/usr/bin/env bash

set -e

printenv | sed 's/^\(.*\)$/export \1/g' > /tmp/env.sh

echo 'Starting cron'
cron -f