<?php

use WpStarter\Notifications\DatabaseNotification;
use WpStarter\Notifications\DatabaseNotificationCollection;

use function PHPStan\Testing\assertType;

class CustomNotification extends DatabaseNotification
{
    //
}

/**
 * @extends DatabaseNotificationCollection<int, CustomNotification>
 */
class CustomNotificationCollection extends DatabaseNotificationCollection
{
    //
}

$databaseNotificationsCollection = DatabaseNotification::all();
assertType('WpStarter\Database\Eloquent\Collection<int, WpStarter\Notifications\DatabaseNotification>', $databaseNotificationsCollection);

$customNotificationsCollection = CustomNotification::all();
assertType('WpStarter\Database\Eloquent\Collection<int, CustomNotification>', $customNotificationsCollection);
