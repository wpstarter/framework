<?php

namespace WpStarter\Wordpress\Auth;

use WP_User;
use WpStarter\Contracts\Broadcasting\HasBroadcastChannel;
use WpStarter\Contracts\Queue\QueueableEntity;
use WpStarter\Contracts\Routing\UrlRoutable;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Contracts\Support\CanBeEscapedWhenCastToString;
use WpStarter\Contracts\Support\Jsonable;
use WpStarter\Database\Eloquent\MassAssignmentException;
use WpStarter\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use WpStarter\Exceptions\WpErrorException;
use WpStarter\Wordpress\Auth\Access\Authorizable;
use WpStarter\Wordpress\Auth\Concerns\UserQuery;
use JsonSerializable;
use ArrayAccess;

abstract class User extends WP_User implements
    AuthorizableContract,
    Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use Authorizable;
    use Concerns\HasAttributes,
        Concerns\HasEvents,
        Concerns\HasGlobalScopes,
        Concerns\HasRelationships,
        Concerns\HasTimestamps,
        Concerns\HidesAttributes,
        Concerns\GuardsAttributes;
    use UserQuery;
    /**
     * Indicates if the model was inserted during the current request lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;
    protected $skipPasswordHash=false;
    /**
     * @var array Attributes supported by wp_insert_user
     */
    protected $wp_fields=[
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

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];


    /**
     * The array of trait initializers that will be called on each new instance.
     *
     * @var array
     */
    protected static $traitInitializers = [];

    public function __construct($attributes=[],$site_id=0)
    {
        $this->bootIfNotBooted();
        parent::__construct(0, '', $site_id);
        $this->fill($attributes);
    }

    function init($data, $site_id = '')
    {
        $this->setConnection($this->getConnection()->getName());
        if($data) {
            if(is_array($data)){
                $data=(object)$data;
            }
            parent::init($data, $site_id);
        }else {
            $this->data=new \stdClass();
            $this->ID=0;
        }
        $this->readMissingAttributes();
        $this->syncOriginal();
    }

    /***
     * Load main user fields to data object so it won't be lost when save()
     */
    protected function readMissingAttributes(){
        if(!empty($this->ID)){
            $user_id=$this->ID;

            foreach ($this->wp_fields as $field){
                if(!isset($this->data->{$field})){
                    if(metadata_exists('user',$user_id,$field)) {
                        $this->data->{$field} = get_user_meta($user_id, $field, true);
                    }else{
                        $this->data->{$field} = null;
                    }
                }
            }
            foreach ($this->fillable as $field){
                if(!isset($this->data->{$field})){
                    if(metadata_exists('user',$user_id,$field)){
                        $this->data->{$field}=get_user_meta($user_id,$field,true);
                    }else{
                        $this->data->{$field}=null;
                    }
                }
            }
        }
    }

    public function fresh(){
        return static::find($this->ID);
    }

    public static function make(...$args){
        $instance=new static($args);
        return $instance;
    }

    /**
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        if (! isset(static::$booted[static::class])) {
            static::$booted[static::class] = true;

            //Check for fillable

            if($needRemove=array_intersect($this->wp_fields,$this->fillable)){
                throw new \LogicException('Please remove following fields from fillable: '.join(',',$needRemove));
            }

            $this->fireModelEvent('booting', false);

            static::boot();

            $this->fireModelEvent('booted', false);
        }
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::bootTraits();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        $class = static::class;

        $booted = [];

        static::$traitInitializers[$class] = [];

        foreach (ws_class_uses_recursive($class) as $trait) {
            $method = 'boot'.ws_class_basename($trait);

            if (method_exists($class, $method) && ! in_array($method, $booted)) {
                forward_static_call([$class, $method]);

                $booted[] = $method;
            }

            if (method_exists($class, $method = 'initialize'.ws_class_basename($trait))) {
                static::$traitInitializers[$class][] = $method;

                static::$traitInitializers[$class] = array_unique(
                    static::$traitInitializers[$class]
                );
            }
        }
    }
    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        if (! $this->exists()) {
            return false;
        }
        return $this->fill($attributes)->save($options);
    }
    public function fill($attributes){
        foreach ($attributes as $key=>$value){
            if($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }else{
                throw new MassAssignmentException(sprintf(
                    'Add [%s] to fillable property to allow mass assignment on [%s].',
                    $key, get_class($this)
                ));
            }
        }
        return $this;
    }

    protected function isWpField($key){
        return in_array($key,$this->wp_fields);
    }
    protected function isAdditionalMeta($key){
        return !$this->isWpField($key);
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }
        if ($this->exists()) {
            $saved = $this->performUpdate();
        }else {
            $saved = $this->performInsert();
        }
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }
    protected function performUpdate(){

        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        $this->data->ID=$this->ID;

        if(!empty($this->data->user_pass)
            && $this->isDirty('user_pass')
            && !$this->skipPasswordHash
        ){
            $this->data->user_pass=wp_hash_password($this->data->user_pass);
        }
        $result=wp_insert_user($this->data);
        if(is_wp_error($result)){
            throw (new WpErrorException($result->get_error_message()))->setWpError($result);
        }
        $this->performUpdateExtra();
        $this->syncChanges();
        $this->fireModelEvent('updated', false);
        return $result;

    }
    protected function performInsert(){
        if ($this->fireModelEvent('creating') === false) {
            return false;
        }
        $result=wp_insert_user($this->data);
        if(is_wp_error($result)){
            throw (new WpErrorException($result->get_error_message()))->setWpError($result);
        }
        $this->performUpdateExtra();
        $this->ID=$result;
        $this->wasRecentlyCreated = true;
        $this->fireModelEvent('created', false);
        return $result;
    }
    protected function finishSave($options){

        $data=static::get_data_by('id',$this->ID);
        $this->init( $data, $this->get_site_id() );

        $this->fireModelEvent('saved', false);
    }

    protected function performUpdateExtra(){
        //List of fields which not handled by wp_insert_user
        $fields=['user_activation_key','user_login','user_status'];
        $update=[];
        if(isset($this->data->user_activation_key)) {
            $update['user_activation_key']=$this->data->user_activation_key;
        }
        if(isset($this->data->user_status)){
            $update['user_status']=$this->data->user_status;
        }
        if($newLogin=$this->maybeUpdateUserLogin()){
            $update['user_login']=$newLogin;
        }

        global $wpdb;
        if($this->ID && $update) {
            $wpdb->update($wpdb->users, $update, ['ID' => $this->ID]);
        }

        foreach ($this->data as $key=>$value){
            if($this->isAdditionalMeta($key)){//we need to update additional meta fields which not maintained in wp_insert_user
                if(!is_null($value)) {
                    update_user_meta($this->ID, $key, $value);
                }else{
                    delete_user_meta($this->ID,$key);
                }
            }
        }
    }

    static function create($attributes){
        return (new static())->fill($attributes)->save();
    }

    public function toArray()
    {
        return $this->attributesToArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(),$options);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }


    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Magic method for accessing custom fields.
     *
     * @since 3.3.0
     *
     * @param string $key User meta key to retrieve.
     * @return mixed Value of the given user meta key (if set). If `$key` is 'id', the user ID.
     */
    public function __get( $key ) {
        return $this->getAttribute($key);
    }

    /**
     * Magic method for setting custom user fields.
     *
     * This method does not update custom fields in the database. It only stores
     * the value on the WP_User instance.
     *
     * @since 3.3.0
     *
     * @param string $key   User meta key.
     * @param mixed  $value User meta value.
     */
    public function __set( $key, $value ) {
        $this->setAttribute($key,$value);
    }

    public function __isset($key)
    {
        return parent::__isset($key);
    }


    /**
     * This method help to update user login, since user login not able to update with wp_user_insert
     * @param $newLogin
     * @return bool|false|int
     * @throws WpErrorException
     */
    protected function maybeUpdateUserLogin($newLogin=null){
        $oldLogin=(string)$this->getOriginal('user_login');
        if($newLogin===null){
            $newLogin=(string)$this->user_login;
        }

        if($this->ID && $newLogin!==$oldLogin) {
            $user = get_user_by( 'login', $newLogin );
            if($user && $user->ID !== $this->ID){
                $exception=new WpErrorException("User login ".$newLogin." already exits",'existing_user_login');
                $exception->setWpError((new \WP_Error('existing_user_login','',['dupe_with_id'=>$user->ID])));
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
    public function passwordAlreadyHashed($callback){
        $this->skipPasswordHash=true;
        $callback();
        $this->skipPasswordHash=true;
    }
}
