<?php


class WPWA_XMLRPC_Client {

    private $xml_rpc_url;
    private $username;
    private $password;

    public function __construct( $xml_rpc_url, $username, $password ) {
        $this->xml_rpc_url  = $xml_rpc_url;
        $this->username     = $username;
        $this->password     = $password;
    }

    public function api_request( $request_method, $params ) {

        $request = xmlrpc_encode_request($request_method, $params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_URL, $this->xml_rpc_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $results = curl_exec($ch);

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);


        if ($errorno != 0) {
            return array("error" => $error);
        }

        if ($response_code != 200) {
            return array("error" => "Request Failed : $results");
        }

        return xmlrpc_decode($results);
    }



    function getLatestProjects() {
        $params = array( 0, $this->username, $this->password, array( "post_type" => "wpwa_project" ) );
        return $this->api_request("wp.getPosts", $params);
    }

    function getLatestServices() {
        $params = array( 0, $this->username, $this->password, array( "post_type" => "wpwa_services" ) );
        return $this->api_request("wp.getPosts", $params);
    }

    function subscribeToDevelopers($developer_id, $api_token) {
        $params = array( "username" => $this->username, "password" => $this->password, "developer" => $developer_id, "token" => $api_token);

        return $this->api_request("wpwa.subscribeToDevelopers", $params);
    }

    function getDevelopers() {
        $params = array();
        return $this->api_request("wpwa.getDevelopers", $params);
    }

    function apiDoc() {
        $params = array();
        return $this->api_request("wpwa.apiDoc", $params);
    }

}

$wpwa_api_client = new WPWA_XMLRPC_Client("http://www.yoursite.com/xmlrpc.php", "username", "password");
$projects = $wpwa_api_client->getLatestProjects();
$services = $wpwa_api_client->getLatestServices();


$wpwa_api_client = new WPWA_XMLRPC_Client("http://www.yoursite.com/xmlrpc.php", "username", "password");

$apiDoc = $wpwa_api_client->apiDoc();
$developers = $wpwa_api_client->getDevelopers();



$subscribe_status = $wpwa_api_client->subscribeToDevelopers("developer id", "api token");



exit;
?>
