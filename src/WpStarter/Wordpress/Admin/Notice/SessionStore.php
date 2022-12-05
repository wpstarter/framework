<?php

namespace WpStarter\Wordpress\Admin\Notice;

use WpStarter\Session\SessionManager;

class SessionStore extends Store
{
    protected $session;
    public function __construct(SessionManager $manager)
    {
        $this->session=$manager;
    }

    function put($notices)
    {
        $this->session->put('admin.notices',$notices);
    }

    function get()
    {
        return $this->session->get('admin.notices',[]);
    }

    function pull(){
        return $this->session->pull('admin.notices',[]);
    }

    function forget()
    {
        return $this->session->forget('admin.notices');
    }
}