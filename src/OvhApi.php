<?php

class OvhApi
{
    //FROM : https://github.com/ovh/php-ovh
    public static $roots = array(
       'ovh-eu' => 'https://eu.api.ovh.com/1.0',
       'ovh-ca' => 'https://ca.api.ovh.com/1.0',
       'kimsufi-eu' => 'https://eu.api.kimsufi.com/1.0',
       'kimsufi-ca' => 'https://ca.api.kimsufi.com/1.0',
       'soyoustart-eu' => 'https://eu.api.soyoustart.com/1.0',
       'soyoustart-ca' => 'https://ca.api.soyoustart.com/1.0',
       'runabove-ca' => 'https://api.runabove.com/1.0');

    protected $AK;
    protected $AS;
    protected $CK;
    protected $serviceName;
    protected $timeDrift = 0;
    protected $cliVersion;

	/**
	 * constructor
	 * @param string $_root API service URL (give in $roots array)
	 * @param string $_ak Your app key
	 * @param string $_as Your app secret key
	 * @param boolean $_cliVersion Set it to false if you're in a web page
	 * @throws Exception In web version, you should have session_start() !
	 */
    public function __construct($_root, $_ak, $_as, $_cliVersion = true)
    {
    	if($_cliVersion && is_file("CK"))
    	{
			$myFile = fopen('CK', 'r+');
			$this->CK = substr(fgets($myFile), 0, -1);
			fclose($myFile);
		}
        else {
            if( ! isset($_SESSION))
			{
				throw new Exception("Add session_start() on your script !");
			}

            $this->CK = $_SESSION["ck"];
        }

        // INIT vars
        $this->AK = $_ak;
        $this->AS = $_as;

        $this->ROOT = $_root;
        $this->cliVersion = $_cliVersion;

        // Compute time drift
        $serverTimeRequest = file_get_contents($this->ROOT . '/auth/time');
        if($serverTimeRequest !== FALSE)
            $this->timeDrift = time() - (int)$serverTimeRequest;
        else
            die('ERROR (#0000) : Compute time drift fail !\n');
    }

    /**
     * Call the api
     * @param $_method GET POST DELETE or UPDATE
     * @param $_url The call url wanted
     * @param $_body Parameters
     */
    public function call($_method, $_url, $_body = "")
    {
        $myUrl = $this->ROOT . $_url;
        if($_body != "")
            $_body = json_encode($_body);

        // Compute signature
        $time = time() - $this->timeDrift;
        $toSign = $this->AS.'+'.$this->CK.'+'.$_method.'+'.$myUrl.'+'.$_body.'+'.$time;
        
        $signature = '$1$' . sha1($toSign);

        // Call
        $curl = curl_init($myUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'X-Ovh-Application:' . $this->AK,
            'X-Ovh-Consumer:' . $this->CK,
            'X-Ovh-Signature:' . $signature,
            'X-Ovh-Timestamp:' . $time,
        ));
        if($_body)
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $_body);
        }
        $result = curl_exec($curl);

        if($result === FALSE)
        {
            echo curl_error($curl);
            return NULL;
        }

        return json_decode($result, true);
    }

    public function get($url)
    {
        return $this->call("GET", $url);
    }

    public function put($url, $body)
    {
        return $this->call("PUT", $url, $body);
    }

    public function post($url, $body)
    {
        return $this->call("POST", $url, $body);
    }

    public function delete($url)
    {
        return $this->call("DELETE", $url);
    }

	/**
	 * Permit you to ask credential to allow ask on api
	 * @param string $_ak Your application key
	 * @param string $_root API service URL (give in $roots array)
	 * @param string $_access Choose type of access, like : '{"accessRules": [{"method": "GET","path": "/*"},{"method": "POST","path": "/*"}]}' for example
	 * @param boolean $_cliVersion Set it to false if you're in a web page. Think to add session_start();
	 * @throws Exception
	 */
    public static function getCredential($_ak, $_root, $_access, $_cliVersion = true)
    {
    	$curl = curl_init($_root . "/auth/credential");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'X-Ovh-Application:' . $_ak
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_access);
        $result = curl_exec($curl);
        echo "Voici votre consumerKey : ";
    	echo preg_replace("/^.*(\"consumerKey\":\")([A-Za-z0-9]+).*$/" , "$2" , $result);
    	echo "\nVoici l'url de validation pour activer votre compte : ";
    	echo "https://api.ovh.com/auth/?credentialToken=" . preg_replace("/^.*(credentialToken=)([A-Za-z0-9]+).*$/" , "$2" , $result);
    	echo "\n";

        if($_cliVersion)
    	   system("echo " . preg_replace("/^.*(\"consumerKey\":\")([A-Za-z0-9]+).*$/" , "$2" , $result) . " > CK");
        else  {
            if( ! isset($_SESSION))
			{
				throw new Exception("Add session_start() on your script !");
			}

            $_SESSION["ck"] = preg_replace("/^.*(\"consumerKey\":\")([A-Za-z0-9]+).*$/" , "$2" , $result);
        }

    }
}

?>
