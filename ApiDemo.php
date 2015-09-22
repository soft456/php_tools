<?php

/**
 * 接口测试工具类
 *
 * @author soft456@gmail.com
 * @datetime 2015-03-27
 *
 * @copyright  Copyright (c) 2015.
 */
class Com_Tools_ApiDemo {

    /**
     * 系统方法，不显示
     * @var array 
     */
    protected static $_systemMethod = array(
        'run', 'runAction', 'showCodeAction', 'test', '__construct', '__call', 'get', 'post',
        'param', 'view', 'template', 'display', 'displayExt', 'defaultTemplate', 'redirect',
        'abort', 'isAjax', 'ajaxReturn', '__set', '__get', 'showCode', 'before', 'setCommon',
        'after', 'buildSearch', 'ajax_return', 'script', 'log'
    );

    /**
     *  获取类的所有方法
     * 
     * @param string $className 要获取其方法的类名
     */
    public static function funcList($className) {
        $class = new ReflectionClass($className); //建立类的反射类
        $codeStr = self::_getClassSourse($class);

        $methodRs = $class->getMethods(ReflectionMethod::IS_PUBLIC); // + ReflectionMethod::IS_PRIVATE + ReflectionMethod::IS_PROTECTED

        $ret = array();
        foreach ($methodRs as $value) {
            if (in_array($value->name, self::$_systemMethod)) {
                continue;
            }

            $matches = array();
            $doc = $value->getDocComment();
            $rex = '/' . $value->name . '.*?\{(.*?)\}/is';
            preg_match_all($rex, $codeStr, $matches, PREG_SET_ORDER);

            $ret[] = array(
                'name' => str_replace('Action', '', $value->name),
                'title' => ((bool) preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false) ? $value->name : trim(trim($comment[1]), '*'),
                'code' => isset($matches[0][1]) ? rawurlencode(base64_encode(json_encode($matches[0][1]))) : ''
            );
        }

        return $ret;
    }

    /**
     *  获取类的所有方法的文档
     * 
     * @param string $className 要获取文档的类名
     */
    public static function funcDoc($className) {

        //如果是数组，则循环获取所有注释
        if (is_array($className)) {
            $ret = array();
            foreach ($className as $key => $value) {
                $ret = array_merge($ret, self::funcDoc($value));
            }
            return $ret;
        }
        $class = new ReflectionClass($className); //建立类的反射类
        $methodRs = $class->getMethods(ReflectionMethod::IS_PUBLIC); // + ReflectionMethod::IS_PRIVATE + ReflectionMethod::IS_PROTECTED

        $ret = array();
        foreach ($methodRs as $value) {
            if (in_array($value->name, self::$_systemMethod)) {
                continue;
            }

            $comment = array();
            $doc = $value->getDocComment();
            $key = str_replace('Action', '', $value->name);
            $docStr = ((bool) preg_match('#^/\*\*(.*)\*/#s', $doc, $comment) === false) ? $value->name : trim(trim($comment[1]), '*');
            $ret[$key] = rawurlencode(base64_encode(json_encode($docStr)));
        }

        return $ret;
    }

    /**
     *  获取源代码
     * 
     * @param obj $class 反射类实例
     * @return string
     */
    private static function _getClassSourse($class) {
        $path = $class->getFileName();
        $lines = @file($path);
        $from = $class->getStartLine();
        $to = $class->getEndLine();
        $len = $to - $from + 1;
        return implode(array_slice($lines, $from - 1, $len));
    }

}
