<?php
$user = 'cuoGQpOGlvfo9O2mdM3s2FbBK4ga';
$password = 'qo_4tny4t5dUf_9p7emtf8ajMxIa';
$command = 'curl -k -d "grant_type=client_credentials" -H "Authorization: Basic "'.base64_encode($user.':'.$password).' https://api.mainapi.net/token';
$output = shell_exec($command);
echo "<pre>$output</pre>";
?>