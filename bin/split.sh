#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="8.x"

function split()
{
    SHA1=`./bin/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH

remote auth git@github.com:illuminate/auth.git
remote broadcasting git@github.com:illuminate/broadcasting.git
remote bus git@github.com:illuminate/bus.git
remote cache git@github.com:illuminate/cache.git
remote collections git@github.com:illuminate/collections.git
remote config git@github.com:illuminate/config.git
remote console git@github.com:illuminate/console.git
remote container git@github.com:illuminate/container.git
remote contracts git@github.com:illuminate/contracts.git
remote cookie git@github.com:illuminate/cookie.git
remote database git@github.com:illuminate/database.git
remote encryption git@github.com:illuminate/encryption.git
remote events git@github.com:illuminate/events.git
remote filesystem git@github.com:illuminate/filesystem.git
remote hashing git@github.com:illuminate/hashing.git
remote http git@github.com:illuminate/http.git
remote log git@github.com:illuminate/log.git
remote macroable git@github.com:illuminate/macroable.git
remote mail git@github.com:illuminate/mail.git
remote notifications git@github.com:illuminate/notifications.git
remote pagination git@github.com:illuminate/pagination.git
remote pipeline git@github.com:illuminate/pipeline.git
remote queue git@github.com:illuminate/queue.git
remote redis git@github.com:illuminate/redis.git
remote routing git@github.com:illuminate/routing.git
remote session git@github.com:illuminate/session.git
remote support git@github.com:illuminate/support.git
remote testing git@github.com:illuminate/testing.git
remote translation git@github.com:illuminate/translation.git
remote validation git@github.com:illuminate/validation.git
remote view git@github.com:illuminate/view.git

split 'src/WpStarter/Auth' auth
split 'src/WpStarter/Broadcasting' broadcasting
split 'src/WpStarter/Bus' bus
split 'src/WpStarter/Cache' cache
split 'src/WpStarter/Collections' collections
split 'src/WpStarter/Config' config
split 'src/WpStarter/Console' console
split 'src/WpStarter/Container' container
split 'src/WpStarter/Contracts' contracts
split 'src/WpStarter/Cookie' cookie
split 'src/WpStarter/Database' database
split 'src/WpStarter/Encryption' encryption
split 'src/WpStarter/Events' events
split 'src/WpStarter/Filesystem' filesystem
split 'src/WpStarter/Hashing' hashing
split 'src/WpStarter/Http' http
split 'src/WpStarter/Log' log
split 'src/WpStarter/Macroable' macroable
split 'src/WpStarter/Mail' mail
split 'src/WpStarter/Notifications' notifications
split 'src/WpStarter/Pagination' pagination
split 'src/WpStarter/Pipeline' pipeline
split 'src/WpStarter/Queue' queue
split 'src/WpStarter/Redis' redis
split 'src/WpStarter/Routing' routing
split 'src/WpStarter/Session' session
split 'src/WpStarter/Support' support
split 'src/WpStarter/Testing' testing
split 'src/WpStarter/Translation' translation
split 'src/WpStarter/Validation' validation
split 'src/WpStarter/View' view
