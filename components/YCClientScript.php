<?php

/**
 * Class YiiConnectClientScript
 */
class YCClientScript extends CClientScript
{

    /**
     * Registers a javascript file.
     * @param string $url URL of the javascript file
     * @param integer $position the position of the JavaScript code. Valid values include the following:
     * <ul>
     * <li>CClientScript::POS_HEAD : the script is inserted in the head section right before the title element.</li>
     * <li>CClientScript::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
     * <li>CClientScript::POS_END : the script is inserted at the end of the body section.</li>
     * </ul>
     * @return YCClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     */
    public function registerScriptFile($url, $position = self::POS_HEAD)
    {
        // do not load these scripts
        $ignores = array();
        foreach ($ignores as $ignore) {
            if (substr($url, strlen($ignore) * -1) === $ignore) {
                return $this;
            }
        }
        return parent::registerScriptFile($url, $position);
    }

    /**
     * Registers a script package that is listed in {@link packages}.
     * @param string $name the name of the script package.
     * @return YCClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     * @see renderCoreScript
     */
    public function registerCoreScript($name)
    {
        // do not load these scripts
        $ignores = array(
            'jquery',
        );
        if (in_array($name, $ignores)) {
            return $this;
        }
        return parent::registerCoreScript($name);
    }

}