<?php
/**
 * mxadodb class file for mxADOdb extra
 *
 * Copyright 2013 by Adam Smith adam@ethannewmedia.com
 * Created on 01-20-2014
 *
 * mxADOdb is owned by Pixel Motion, Inc.

 *
 * @package mxadodb
 */


 class mxadodb {
    /** @var $modx modX */
    public $modx;
    /** @var $props array */
    public $props;

    function __construct(&$modx, &$config = array()) {
        $this->modx =& $modx;
        $this->props =& $config;
    }

}