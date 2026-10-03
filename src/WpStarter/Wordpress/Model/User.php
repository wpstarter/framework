<?php

namespace WpStarter\Wordpress\Model;

use WP_User;
use WpStarter\Database\Eloquent\Builder;
use WpStarter\Database\Eloquent\MassAssignmentException;
use WpStarter\Wordpress\Exceptions\WpErrorException;
use WpStarter\Wordpress\Model\Concerns\WpUserCustom;

class User extends UserBaseModel
{

    use \WpStarter\Wordpress\Model\Concerns\HasAttributes;
    use WpUserCustom;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table='users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $skipPasswordHash = false;
    /**
     * @var array Attributes supported by wp_insert_user
     */
    protected $wp_fields = [
        'ID',
        'user_pass',
        'user_login',
        'user_nicename',
        'user_url',
        'user_email',
        'user_activation_key',
        'user_registered',
        'display_name',
        'nickname',
        'first_name',
        'last_name',
        'description',
        'rich_editing',
        'syntax_highlighting',
        'comment_shortcuts',
        'admin_color',
        'use_ssl',
        'show_admin_bar_front',
        'locale',
        'role',
        'user_status',

    ];
    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'user_registered';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    public function __construct($attributes = [], $site_id = 0)
    {
        $this->bootIfNotBooted();
        $this->initializeTraits();
        parent::__construct(0, '', $site_id);
        $this->syncOriginal();
        $this->fill($attributes);
    }

    function init($data, $site_id = '')
    {
        $this->setConnection($this->getConnection()->getName());
        if ($data) {
            if (is_array($data)) {
                $data = (object)$data;
            }
            parent::init($data, $site_id);
        } else {
            $this->data = new \stdClass();
            $this->ID = 0;
        }
        $this->readMissingAttributes();
        $this->syncOriginal();
    }

    /***
     * Load main user fields to data object so it won't be lost when save()
     */
    protected function readMissingAttributes()
    {
        if (!empty($this->ID)) {
            $user_id = $this->ID;
            if($allMeta=get_user_meta($user_id)){
                foreach ($allMeta as $key=>$values){
                    if(!isset($this->data->{$key})) {
                        $this->data->{$key} = maybe_unserialize($values[0]);
                    }
                }
            }
        }
    }

    public function fresh($with = [])
    {
        return static::find($this->ID);
    }

    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } else {
                throw new MassAssignmentException(sprintf(
                    'Add [%s] to fillable property to allow mass assignment on [%s].',
                    $key, get_class($this)
                ));
            }
        }
        return $this;
    }

    protected function isWpField($key)
    {
        return in_array($key, $this->wp_fields);
    }

    protected function isAdditionalMeta($key)
    {
        return !$this->isWpField($key);
    }

    /**
     * Save the model to the database.
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }
        if ($this->exists()) {
            $saved = $this->performUpdate(null);
        } else {
            $saved = $this->performInsert(null);
        }
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    protected function performUpdate(Builder $query)
    {

        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        $this->data->ID = $this->ID;

        if (!empty($this->data->user_pass)
            && $this->isDirty('user_pass')
            && !$this->skipPasswordHash
        ) {
            $this->data->user_pass = wp_hash_password($this->data->user_pass);
        }
        $result = wp_insert_user($this->data);
        if (is_wp_error($result)) {
            throw (new WpErrorException($result->get_error_message()))->setWpError($result);
        }
        $this->performUpdateExtra();
        $this->syncChanges();
        $this->fireModelEvent('updated', false);
        return $result;

    }

    protected function performInsert(Builder $query)
    {
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }
        $result = wp_insert_user($this->data);
        if (is_wp_error($result)) {
            throw (new WpErrorException($result->get_error_message()))->setWpError($result);
        }
        $this->ID = $result;
        $this->wasRecentlyCreated = true;
        $this->performUpdateExtra();
        $this->fireModelEvent('created', false);
        return $result;
    }

    protected function finishSave($options)
    {

        $data = static::get_data_by('id', $this->ID);
        $this->init($data, $this->get_site_id());

        $this->fireModelEvent('saved', false);
    }

    protected function performUpdateExtra()
    {
        //List of fields which not handled by wp_insert_user
        $fields = ['user_activation_key', 'user_login', 'user_status'];
        $update = [];
        if (isset($this->data->user_activation_key)) {
            $update['user_activation_key'] = $this->data->user_activation_key;
        }
        if (isset($this->data->user_status)) {
            $update['user_status'] = $this->data->user_status;
        }
        if ($newLogin = $this->maybeUpdateUserLogin()) {
            $update['user_login'] = $newLogin;
        }

        global $wpdb;
        if ($this->ID && $update) {
            $wpdb->update($wpdb->users, $update, ['ID' => $this->ID]);
        }
        $dirty = $this->getDirty();
        foreach ($dirty as $key => $value) {
            if ($this->isAdditionalMeta($key)) {//we need to update additional meta fields which not maintained in wp_insert_user
                if (!is_null($value)) {
                    update_user_meta($this->ID, $key, $value);
                } else {
                    delete_user_meta($this->ID, $key);
                }
            }
        }
    }

    /**
     * This method help to update user login, since user login not able to update with wp_user_insert
     * @param $newLogin
     * @return bool|false|int
     * @throws WpErrorException
     */
    protected function maybeUpdateUserLogin($newLogin = null)
    {
        $oldLogin = (string)$this->getOriginal('user_login');
        if ($newLogin === null) {
            $newLogin = (string)$this->user_login;
        }

        if ($this->ID && $newLogin !== $oldLogin) {
            $user = get_user_by('login', $newLogin);
            if ($user && $user->ID !== $this->ID) {
                $exception = new WpErrorException("User login " . $newLogin . " already exits", 'existing_user_login');
                $exception->setWpError((new \WP_Error('existing_user_login', '', ['dupe_with_id' => $user->ID])));
                throw ($exception);
            }
            return $newLogin;
        }
        return null;
    }

    /***
     * Update user while skip password hashing
     * @param $callback
     */
    public function passwordAlreadyHashed($callback)
    {
        $this->skipPasswordHash = true;
        $callback();
        $this->skipPasswordHash = true;
    }
}
