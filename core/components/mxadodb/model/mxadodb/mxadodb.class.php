<?php
/**
 * mxadodb class file for mxADOdb extra
 *
 * Copyright 2013 by Adam Smith adam@ethannewmedia.com
 * Created on 07-04-2013
 *

 * mxADOdb is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * mxADOdb is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * mxADOdb; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA

 *
 * @package mxadodb
 */

 class mxadodb {

     /** @var $modx modX */
     public $modx;
     /** @var $props array */
     public $props;

     private $dbSettings = array();
     private $dbConns = array();

     public function __construct(&$modx, &$config = array())
     {
         $this->modx =& $modx;
         $this->props =& $config;

         if($this->props['debug']){
             $this->modx->setLogLevel(modX::LOG_LEVEL_DEBUG);
         }

         $this->dbSettings['default']['dsn']   = $this->modx->getOption('mxadodb_dsn',$this->props,'');
         $this->dbSettings['default']['adodb'] = $this->modx->getOption('mxadodb_adodb',$this->props,'{}');
     }

     public function addConnection($conn_settings)
     {

     }

     public function listConnections()
     {
         return array_keys($this->dbConns);
     }

     public function init($conn='default',$settings=null)
     {
         if(isset($this->dbConns[$conn])){
             return $this->dbConns[$conn];
         }
         else{

             if($conn === 'default'){
                 $dsn   = $this->dbSettings['default']['dsn'];
                 $adodb = $this->modx->fromJSON($this->dbSettings['default']['adodb']);
             }
             else if(!empty($conn) && !empty($settings)){ // no point in continuing
                 $settings = $this->modx->fromJSON($settings);
                 $dsn      = !empty($settings['dsn']) ? $settings['dsn'] : null;
                 $adodb    = !empty($settings['adodb']) ? $settings['adodb'] : $this->dbSettings['default']['adodb'];
             }
             else if(!empty($conn) && empty($settings)){ // no point in continuing
                 $this->logError('Connection settings not found for: ' . $conn);
                 return false;
             }

             if ($dsn) {

                 $this->logDebug('We have a DSN connection [' . $dsn . ']');

                 if (!empty($adodb)) {
                     foreach ($adodb as $k => $v) {
                         ${$k} = $v;
                     }
                 }

                 try {

                     require_once MODX_CORE_PATH.'components/mxadodb/model/adodb5/adodb-errorhandler.inc.php';
                     require_once MODX_CORE_PATH.'components/mxadodb/model/adodb5/adodb-exceptions.inc.php';
                     require_once MODX_CORE_PATH.'components/mxadodb/model/adodb5/adodb.inc.php';

                     $new_conn = ADONewConnection($dsn); # persist is optional

                     if($new_conn){
                         $this->dbConns[$conn] = $new_conn;
                     }
                     else{
                         $this->logError('Connection failed for [' . $conn . ']');
                     }

                     $this->logDebug('New Connection made');
                     return $this->dbConns[$conn];
                 } catch (exception $e) {
                     $this->logError('Connection failed for [' . $conn . ']');
                     $this->logError($e->gettrace());
                     return false;
                 }

             } else {
                 $this->logError('DSN missing for connection [' . $conn . ']');
                 return false;
             }
         }
     }

     public function removeConnection($conn)
     {
         if(isset($this->dbConns[$conn])){
             $this->dbConns[$conn]->Close();
             unset($this->dbConns[$conn]);
             $this->logDebug('Closed connection [' . $conn . ']');
             return true;
         }

         $this->logDebug('Connections not found [' . $conn . ']');
         return false;
     }

     public function removeAllConnections()
     {
         if (!empty($this->dbConns)) {
             foreach(array_keys($this->dbConns) as $key){
                 $this->closeConnection($key);
             }
         }
         else{
             $this->logDebug('No Connections to close');
             return false;
         }
     }

     private function logDebug($msg)
     {
         if ($this->modx->getLogLevel() <= modX::LOG_LEVEL_DEBUG) {
             $this->modx->log(modX::LOG_LEVEL_DEBUG, $msg);
         }
     }

     private function logError($msg)
     {
         if ($this->modx->getLogLevel() <= modX::LOG_LEVEL_ERROR) {
             $this->modx->log(modX::LOG_LEVEL_ERROR, $msg);
         }
     }


}