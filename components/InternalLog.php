<?php
/**
 * InternalLog
 *
 * @author Zain <zain@mrphp.com.au>
 */
class InternalLog
{

    const TIMER_ACTION_START = 1;
    const TIMER_ACTION_STOP = 2;
    const TIMER_ACTION_DISPLAY_SUM = 3;

    /**
     * @var
     */
    static $file_path;
    /**
     * @var
     */
    static $lock_file_path;

    /**
     * @var int
     */
    static $init = 0;
    /**
     * @var string
     */
    static $newline = "\r\n";
    /**
     * @var bool
     */
    static $showLineNumber = true;
    /**
     * @var
     */
    static $debugLine;
    /**
     * @var bool
     */
    static $clearLog = true;
    /**
     * @var bool
     */
    static $ignoreLog = false;
    /**
     * @var int
     */
    static $startMicroTime = 0;
    /**
     * @var int
     */
    static $lastCallMicroTime = 0;
    /**
     * @var int
     */
    static $minExecTime = -1;
    /**
     * @var array
     */
    static $loggedTimes = array();

    static $internalGlobals = array();

    /**
     * @static
     * @return mixed
     */
    public static function firstLine()
    {
        if (self::$init == 1) {
            return;
        }
        self::$file_path = Yii::app()->getRuntimePath() . '/internal_log/log.txt';
        self::$lock_file_path = Yii::app()->getRuntimePath() . '/internal_log/lock';
        self::CreateLogFolder($fileName = self::$file_path);
        if (realpath(self::$file_path)){
            self::$file_path = realpath(self::$file_path);
        }
        if (self::$clearLog) {
            self::clearLog();
        }
        self::$init = 1;
        self::$startMicroTime = microtime(true);
        self::$lastCallMicroTime = self::$startMicroTime;
        self::unLock();
        $msg = '----------------------- ' . date('l jS M y') . '--ls-----------------------------------';
        self::$debugLine += 2;
        if (isset($_SERVER["REQUEST_URI"])) {
            $requestLine = 'REQUEST_URI -> ' . $_SERVER["REQUEST_URI"];
            $msg = $msg . "\r\n" . $requestLine;
        }
        if (isset($_POST)) {
            if (!empty($_POST)) {
                $post = print_r($_POST, true);
                $msg = $msg . "\r\n" . ' POST ' . $post;
            }
        }
        if (isset($_GET)) {
            if (!empty($_GET)) {
                $get = print_r($_GET, true);
                $msg = $msg . "\r\n" . ' GET ' . $get;
            }
        }
        self::log($msg);
        self::$debugLine -= 2;
    }

    /**
     * @static
     * @param string $message
     * @param bool $logCondition
     * @return bool
     */
    public static function log($message = '', $logCondition = true)
    {
        if (self::$ignoreLog)
            return false;
        if (!$logCondition)
            return false;
        if (!getenv('WINDIR')) {
            return false;
        }
        $newline = self::$newline;
        self::firstLine();
        if (file_exists(self::$file_path)) {
            if (!self::isLocked()) {
                self::lock();
                $oldContents = file_get_contents(self::$file_path);
                $codeLine = "";
                if (self::$showLineNumber) {
                    $bt = debug_backtrace();
                    // echo "<br/> debugline [" . self::$debugLine ."] <br/>";
                    // printr($bt);
                    // die;                    
                    $file = str_replace(bp(), '', $bt[self::$debugLine]['file']);
                    $codeLine = '  [' . $file . '] on line ' . $bt[self::$debugLine]['line'];
                }
                // $line=date('l jS m y h:i:s A');
                $currentMicroTime = microtime(true);
                $execTime = $currentMicroTime - self::$lastCallMicroTime;
                $execTime = round($execTime, 3);
                $lapseTime = $currentMicroTime - self::$startMicroTime;
                $lapseTime = round($lapseTime, 3);
                if ($execTime > self::$minExecTime) {
                    $line = date('h:i:s A') . ' ' . $execTime . '/' . $lapseTime . ' ' . $codeLine . $newline . $message;
                    $newContents = $oldContents . $line . $newline . $newline . $newline;
                    file_put_contents(self::$file_path, $newContents);
                }
                self::unLock();
                self::$lastCallMicroTime = microtime(true);
            }
            else {
                echo "<br/> locked [" . self::$lock_file_path . "] <br/>";
            }
        }
        return true;
    }

    /**
     * @static
     * @return bool
     */
    public static function debug_print_backtrace()
    {
        if (self::$ignoreLog)
            return false;
        if (!getenv('WINDIR')) {
            return false;
        }
        ;
        $newline = self::$newline;
        ob_start();
        debug_print_backtrace();
        $trace = ob_get_contents();
        ob_end_clean();

        // $message=$message . $newline . $newline . $trace;
        $message = $trace;
        self::log($message);

    }

    /**
     * @static
     * @return bool
     */
    public static function unLock()
    {
        if (file_exists(self::$lock_file_path)) {
            unlink(self::$lock_file_path);
            return true;
        }
    }

    /**
     * @static
     * @return bool
     */
    public static function lock()
    {
        if (!file_exists(self::$lock_file_path)) {
            file_put_contents(self::$lock_file_path, '');
            return true;
        }
    }

    /**
     * @static
     * @return bool
     */
    public static function isLocked()
    {
        if (file_exists(self::$lock_file_path)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @static
     * @param $fileName
     */
    public static function CreateLogFolder($fileName)
    {
        $dirName = dirname($fileName);
        if (!file_exists($dirName))
            Helper::createDirectory($dirName, 0777, true);
        if (!file_exists($fileName)) {
            file_put_contents($fileName, "");
        }
    }

    /**
     * @static
     *
     */
    public static function clearLog()
    {
        file_put_contents(self::$file_path, "");
    }

    /**
     * @static
     * @param $variable
     * @param bool $variableName
     * @param bool $attributesOnly
     * @return bool
     */
    public static function printr($variable, $variableName = false, $attributesOnly = true)
    {
        if (self::$ignoreLog)
            return false;
        if (!getenv('WINDIR')) {
            return false;
        }
        if ($attributesOnly && (is_array($variable) || is_object($variable))) {
            $variable = getAttributes($variable);
        }
        $output = '[' . print_r($variable, true) . ']';
        if ($variableName) {
            $output = $variableName . ': ' . $output;
        }
        self::$debugLine = 1;
        self::log($output);
        self::$debugLine = 0;
        return true;
    }

    /**
     * @static
     * @param $message
     * @param $category
     * @param $action
     * @param $logMessage
     * @return bool
     */
    public static function logTime($message, $category, $action, $logMessage)
    {
        if (self::$ignoreLog)
            return false;
        if (!getenv('WINDIR')) {
            return false;
        }
        ;
        $currentMicroTime = microtime(true);
        if (!isset(self::$loggedTimes[$category])) {
            if ($action != self::TIMER_ACTION_START) {
                //trigger error
                trigger_error("some issue in internal log");
            }
            self::$loggedTimes[$category] = array(
                'lastCallMicroTime' => false,
                'total' => 0,
                'end' => false,
            );
        }
        if ($action == self::TIMER_ACTION_START) {
            self::$loggedTimes[$category]['lastCallMicroTime'] = $currentMicroTime;
            // echo "<br/>category[$category] setting lastCallMicroTime to [". self::$loggedTimes[$category]['lastCallMicroTime'] ."] <br/>";
        }
        elseif ($action == self::TIMER_ACTION_STOP) {
            $lapsedTime = $currentMicroTime - self::$loggedTimes[$category]['lastCallMicroTime'];
            self::$loggedTimes[$category]['total'] += $lapsedTime;
            $lapsedTime = round($lapsedTime, 4);
            $totalTime = round(self::$loggedTimes[$category]['total'], 4);
            $summaryMessage = "lapsedTime is [$lapsedTime] total is [" . $totalTime . "]";
            $message = $message . ': ' . $summaryMessage;
            // echo "<br/> category[$category] lapsedTime is [$lapsedTime] total is [" . self::$loggedTimes[$category]['total'] ."] lastCallMicroTime is [" . self::$loggedTimes[$category]['lastCallMicroTime'] ."] currentMicroTime is [$currentMicroTime] <br/>";
            // echo "<br/> category[$category] lapsedTime is [$lapsedTime] total is [" . self::$loggedTimes[$category]['total'] ."]  <br/>";
        }
        elseif ($action == self::TIMER_ACTION_DISPLAY_SUM) {
            self::$debugLine = 1;
            $totalTime = round(self::$loggedTimes[$category]['total'], 4);
            $summaryMessage = $message . ': Total time spent on [' . $category . '] is ' . $totalTime;
            self::log($summaryMessage);
            self::$debugLine = 0;
        }

        if ($logMessage) {
            self::$debugLine = 1;
            self::log($category . ': ' . $message);
            self::$debugLine = 0;
        }
    }
}