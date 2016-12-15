 <?php
/**
 * SimpleCode
 *
 * @copyright   Copyright (c) 2014 - 2015, Atom Media Studio. (http://atommediastudio.com/)
 * @author      Badri Zaki <badrizaki@atommediastudio.com>
 * @link        http://www.simplecode.atommediastudio.com
 * @version     1.0.0
 * @package     SimpleCode
 *
 * An open source application development framework for PHP
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/*
 *---------------------------------------------------------------
 * START SESSION
 *---------------------------------------------------------------
 *
 * Start session for login
 */
	session_start();

/*
 *---------------------------------------------------------------
 * PUBLIC PATH & APP PATH
 *---------------------------------------------------------------
 *
 * Set path public and app
 */
    define('APP_PATH', __DIR__."/app");
	define('VIEW_PATH', __DIR__."/app/views");
    define('BASE_PATH', __DIR__);

/*
 *---------------------------------------------------------------
 * GET SYSTEM AutoLoader
 *---------------------------------------------------------------
 *
 * This is for get system file require
 */
    require_once APP_PATH . "/vendor/autoload.php";

/*
 *---------------------------------------------------------------
 * VARIABLE FLAG PAGE NOT FOUND
 *---------------------------------------------------------------
 *
 * This is for set flag for page, if page not found in routes
 * then this $result = false else true  
 */
    $result = false;

/*
 *---------------------------------------------------------------
 * LOAD FILE CONFIGURATION
 *---------------------------------------------------------------
 *
 * Load file config for using in app
 */
    require_once APP_PATH . "/config/config.php";

/*
 *---------------------------------------------------------------
 * DEVELOPMENT MODE
 *---------------------------------------------------------------
 *
 * if variable development_mode in file config/config.php = 1
 * Show all errors
 */
    if (isset($development_mode) && $development_mode)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }

/*
 *---------------------------------------------------------------
 * SET & GET CLASS APPLICATION
 *---------------------------------------------------------------
 *
 * Load and set class application for routes (url)
 * Two method in this class POST and GET for execution page
 * function NotFound() for page not found 
 */
    require_once APP_PATH . "/system/Application.php";
    $app = new Application();

/*
 *---------------------------------------------------------------
 * FILE ROUTES
 *---------------------------------------------------------------
 *
 * Load file routes.php in config directory
 * $app->get('variable one', 'variable two');
 * variable one for url
 * variable two for get class and method from folder controller
 *   
 * $app->get('url', 'ClassName:method');
 * EXAMPLE :
 *          $app->get('login', 'UserActivity:login'); ## method get
 *          or
 *          $app->post('logout', 'UserActivity:logout'); ## method post
 */
    require_once APP_PATH . "/config/routes.php";

/*
 *---------------------------------------------------------------
 * SET & GET PAGE NOT FOUND
 *---------------------------------------------------------------
 *
 * Set page not found if class or method not found
 */
    if (!$result) $app->NotFound();