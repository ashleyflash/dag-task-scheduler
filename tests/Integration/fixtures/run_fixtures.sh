#!/usr/bin/env bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cat $DIR/schema.sql $DIR/fixtures.sql | mysql -udaguser -pq3raTVttAcnHpTTHCUwsGLu9
