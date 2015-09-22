<?php


    /**
     * run()
     *
     * @return
     */
    function runAction() {
        echo 'api demo run<br>-----------------------------------------------------------<br>';

        $rs = Com_Tools_ApiDemo::funcList(__CLASS__);

        $docRs = Com_Tools_ApiDemo::funcDoc(
                        array(
                            'models_Account_Interface'
                        )
        );

        echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />"
        . "<table>";

        $bgcolor = "#e6e6e6";
        foreach ($rs as $value) {

            $paramsStr = isset($docRs[$value['name']]) ? "<a href=\"/apiDemo/showCode?c={$docRs[$value['name']]}\" target=\"_blank\">参数说明</a>" : '无';
            $bgcolor = ($bgcolor == "#e6e6e6") ? "#ffffff" : '#e6e6e6';
            echo "<tr bgcolor=\"{$bgcolor}\">"
            . "<td><a href=/apiDemo/{$value['name']} target=\"_blank\">" . $value['name'] . '</a> </td>'
            . '<td> ' . $value['title'] . " </td>"
            . "<td><a href=\"/apiDemo/showCode?c={$value['code']}\" target=\"_blank\">demo</a></td>"
            . "<td>{$paramsStr}</td>"
            . "</tr>";
        }
        echo "</table>";
        exit;
    }

    function showCodeAction() {
        $c = $this->get('c');
        $isCode = intval($this->get('t'));
        $codeStr = json_decode(base64_decode($c), 1);

        Cola_Response::charset();
        if ($isCode) {
            echo '{';
        } else {
            echo "/**<br>";
        }

        $codeStr2 = $isCode ? str_replace(chr(13), '<br>', $codeStr) : str_replace(chr(10), '<br>', $codeStr);
        $codeStr2 = str_replace(chr(32), '&nbsp;', $codeStr2);
//        $codeStr2 = str_replace('&nbsp;&nbsp;&nbsp;&nbsp;', '', $codeStr2);

        echo '* ' . ($codeStr2);

        if ($isCode) {
            echo '}';
        } else {
            echo "<br>*/";
        }
        exit;
    }
    
