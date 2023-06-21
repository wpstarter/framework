<?php

namespace WpStarter\Wordpress\Admin\ListTable;
use WpStarter\Support\Str;

/**
 * @package WpStarter Admin
 * Class used to extend existing posts list table via filters
 *
 */
abstract class AlterPostsListTable
{
    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = '';

    /**
     * Object being shown on the row.
     *
     * @var object|null
     */
    protected $object = null;

    /**
     * Constructor.
     */
    public function __construct() {
        if ( $this->post_type ) {
            add_action( 'manage_posts_extra_tablenav', array( $this, 'maybeRenderBlankState') );
            add_filter( 'view_mode_post_types', array( $this, 'disableViewMode') );
            add_action( 'restrict_manage_posts', array( $this, 'restrictManagePosts') );
            add_filter( 'request', array( $this, 'requestQuery') );
            add_filter( 'post_row_actions', array( $this, 'rowActions'), 100, 2 );
            add_filter( 'default_hidden_columns', array( $this, 'defaultHiddenColumns'), 10, 2 );
            add_filter( 'list_table_primary_column', array( $this, 'listTablePrimaryColumn'), 10, 2 );
            add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', array( $this, 'defineSortableColumns') );
            add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'defineColumns') );
            add_filter( 'bulk_actions-edit-' . $this->post_type, array( $this, 'defineBulkActions') );
            add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'renderColumns'), 10, 2 );
            add_filter( 'handle_bulk_actions-edit-' . $this->post_type, array( $this, 'handleBulkActions'), 10, 3 );
        }
    }

    /**
     * Show blank slate.
     *
     * @param string $which String which tablenav is being shown.
     */
    public function maybeRenderBlankState($which ) {
        global $post_type;

        if ( $post_type === $this->post_type && 'bottom' === $which ) {
            $counts = (array) wp_count_posts( $post_type );
            unset( $counts['auto-draft'] );
            $count = array_sum( $counts );

            if ( 0 < $count ) {
                return;
            }

            $this->renderBlankState();

            echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
        }
    }

    /**
     * Render blank state. Extend to add content.
     */
    protected function renderBlankState() {}

    /**
     * Removes this type from list of post types that support "View Mode" switching.
     * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
     * it, so we want to hide the useless UI from the screen options tab.
     *
     * @param  array $post_types Array of post types supporting view mode.
     * @return array             Array of post types supporting view mode, without this type.
     */
    public function disableViewMode($post_types ) {
        unset( $post_types[ $this->post_type ] );
        return $post_types;
    }

    /**
     * See if we should render search filters or not.
     */
    public function restrictManagePosts() {
        global $typenow;

        if ( $this->post_type === $typenow ) {
            $this->renderFilters();
        }
    }

    /**
     * Handle any filters.
     *
     * @param array $query_vars Query vars.
     * @return array
     */
    public function requestQuery($query_vars ) {
        global $typenow;

        if ( $this->post_type === $typenow ) {
            return $this->queryFilters( $query_vars );
        }

        return $query_vars;
    }

    /**
     * Render any custom filters and search inputs for the list table.
     */
    protected function renderFilters() {}

    /**
     * Handle any custom filters.
     *
     * @param array $query_vars Query vars.
     * @return array
     */
    protected function queryFilters($query_vars ) {
        return $query_vars;
    }

    /**
     * Set row actions.
     *
     * @param array   $actions Array of actions.
     * @param \WP_Post $post Current post object.
     * @return array
     */
    public function rowActions($actions, $post ) {
        if ( $this->post_type === $post->post_type ) {
            return $this->getRowActions( $actions, $post );
        }
        return $actions;
    }

    /**
     * Get row actions to show in the list table.
     *
     * @param array   $actions Array of actions.
     * @param \WP_Post $post Current post object.
     * @return array
     */
    protected function getRowActions($actions, $post ) {
        return $actions;
    }

    /**
     * Adjust which columns are displayed by default.
     *
     * @param array  $hidden Current hidden columns.
     * @param object $screen Current screen.
     * @return array
     */
    public function defaultHiddenColumns($hidden, $screen ) {
        if ( isset( $screen->id ) && 'edit-' . $this->post_type === $screen->id ) {
            $hidden = array_merge( $hidden, $this->defineHiddenColumns() );
        }
        return $hidden;
    }

    /**
     * Set list table primary column.
     *
     * @param  string $default Default value.
     * @param  string $screen_id Current screen ID.
     * @return string
     */
    public function listTablePrimaryColumn($default, $screen_id ) {
        if ( 'edit-' . $this->post_type === $screen_id && $this->getPrimaryColumn() ) {
            return $this->getPrimaryColumn();
        }
        return $default;
    }

    /**
     * Define primary column.
     *
     * @return string
     */
    protected function getPrimaryColumn() {
        return '';
    }

    /**
     * Define hidden columns.
     *
     * @return array
     */
    protected function defineHiddenColumns() {
        return array();
    }

    /**
     * Define which columns are sortable.
     *
     * @param array $columns Existing columns.
     * @return array
     */
    public function defineSortableColumns($columns ) {
        return $columns;
    }

    /**
     * Define which columns to show on this screen.
     *
     * @param array $columns Existing columns.
     * @return array
     */
    public function defineColumns($columns ) {
        return $columns;
    }

    /**
     * Define bulk actions.
     *
     * @param array $actions Existing actions.
     * @return array
     */
    public function defineBulkActions($actions ) {
        return $actions;
    }

    /**
     * Pre-fetch any data for the row each column has access to it.
     *
     * @param int $post_id Post ID being shown.
     */
    protected function prepareRowData($post_id ) {}

    /**
     * Render individual columns.
     *
     * @param string $column Column ID to render.
     * @param int    $post_id Post ID being shown.
     */
    public function renderColumns($column, $post_id ) {
        $this->prepareRowData( $post_id );

        if ( ! $this->object ) {
            return;
        }
        $method='render_' . $column . '_column';
        $method=Str::camel($method);
        if ( is_callable( array( $this, $method ) ) ) {
            $this->$method($this->object);
        }
    }

    /**
     * Handle bulk actions.
     *
     * @param  string $redirect_to URL to redirect to.
     * @param  string $action      Action name.
     * @param  array  $ids         List of ids.
     * @return string
     */
    public function handleBulkActions($redirect_to, $action, $ids ) {
        return esc_url_raw( $redirect_to );
    }
}
