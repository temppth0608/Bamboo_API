<?php
function pushApns($deviceToken, $alert) {
    //$badge = 1;
    $body = array();
    $body['aps'] = array( 'alert' => $alert );
    //$body['aps']['badge'] = $badge;
    $body['aps']['sound'] = "default";

    $cert = '/home/ubuntu/pem/apns.pem';
    $url = 'ssl://gateway.sandbox.push.apple.com:2195'; 

    $context = stream_context_create();
    stream_context_set_option( $context, 'ssl', 'local_cert', $cert );
    $fp = stream_socket_client( $url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $context );
    if( !$fp ) {
        echo 'Failed to connect.' . PHP_EOL;
        exit( 1 );
    }
    $payload = json_encode( $body );
    $message = chr( 0 ) . pack( 'n', 32 ) . pack( 'H*', $deviceToken ) . pack( 'n', strlen($payload ) ) . $payload;
    print 'send message:' . $payload . PHP_EOL;
    fwrite( $fp, $message );
    fclose( $fp );
}

function echoJson($json_array) {
	echo stripslashes(json_encode($json_array,JSON_UNESCAPED_UNICODE));
	//$output = json_readable_encode($json_array);
	//echo $output;
}

function json_readable_encode($in, $indent = 0, $from_array = false)
{
    $_myself = __FUNCTION__;
    $_escape = function ($str)
    {
        return preg_replace("!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str);
    };

    $out = '';

    foreach ($in as $key=>$value)
    {
        $out .= str_repeat("\t", $indent + 1);
        $out .= "\"".$_escape((string)$key)."\": ";

        if (is_object($value) || is_array($value))
        {
            $out .= "\n";
            $out .= $_myself($value, $indent + 1);
        }
        elseif (is_bool($value))
        {
            $out .= $value ? 'true' : 'false';
        }
        elseif (is_null($value))
        {
            $out .= 'null';
        }
        elseif (is_string($value))
        {
            $out .= "\"" . $_escape($value) ."\"";
        }
        else
        {
            $out .= $value;
        }

        $out .= ",\n";
    }

    if (!empty($out))
    {
        $out = substr($out, 0, -2);
    }

    $out = str_repeat("\t", $indent) . "{\n" . $out;
    $out .= "\n" . str_repeat("\t", $indent) . "}";

    return $out;
}

?>