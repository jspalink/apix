<?php

/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix\View\ViewModel;

use Apix\View\ViewModel,
    Apix\Response;

class Error extends ViewModel
{

    protected $_layout = 'error';

    public $id = null;

    public function title()
    {
        return 'Error ' . $this->code;
    }

    public function id()
    {
        return Response::getStatusPrases($this->code, false);
    }

    public function description()
    {
        return Response::getStatusPrases($this->code, true);
    }

    // deals with groups definitions
    public function groups()
    {
        $titles = array(
            'message'    => 'Message',
            'code'       => 'Code'
        );
        $groups = array();
        foreach ($titles as $key => $title) {
            if (isset($this->{$key})) {
                $groups[] = array('title' => $title, 'items' => (array) $this->{$key});
            }
        }

        return $groups;
    }

}
