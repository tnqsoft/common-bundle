<?php

namespace TNQSoft\CommonBundle\Service\Rest;

use TNQSoft\CommonBundle\Service\Rest\ApiSecurityHandle;
use TNQSoft\CommonBundle\Service\Rest\Response;

class ApiHandle
{
    const METHOD_GET       = 'GET';
    const METHOD_POST      = 'POST';
    const METHOD_PUT       = 'PUT';
    const METHOD_PATCH     = 'PATCH';
    const METHOD_DELETE    = 'DELETE';
    const METHOD_COPY      = 'COPY';
    const METHOD_HEAD      = 'HEAD';
    const METHOD_OPTIONS   = 'OPTIONS';
    const METHOD_LINK      = 'LINK';
    const METHOD_UNLINK    = 'UNLINK';
    const METHOD_PURGE     = 'PURGE';

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $url;

    /**
     * @var boolean
     */
    private $ssl;

    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $header;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @var ApiSecurityHandle
     */
    private $security;

    /**
     * __construct
     *
     * @param string $method
     * @param string $url
     * @param array  $params
     * @param array  $raw
     * @param array  $files
     * @param array  $header
     */
    public function __construct($method, $url, $params=array(), $raw=array(), $files=array(), $header=array())
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->params = $params;
        $this->raw = $raw;
        $this->files = $files;
        $this->header = $header;
        $this->parseUrl();
        $this->security = null;
    }

    public function request()
    {
        $oCurl = $this->createClient();
        $response = curl_exec($oCurl);
        $info = curl_getinfo($oCurl);
        curl_close($oCurl);
        $oResponse = new Response($info, $response);

        return $oResponse;
    }

    public function getCurl()
    {
        return $this->createClient();
    }

    public function setSecurity(ApiSecurityHandle $security)
    {
        $this->security = $security;
    }

    protected function createClient()
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_PORT, $this->port);
        curl_setopt($oCurl, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oCurl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($oCurl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($oCurl, CURLOPT_HEADER, true);
        curl_setopt($oCurl, CURLOPT_URL, $this->url);

        if (true === $this->ssl) {
            // Disable SSL check
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }

        // Pass request data
        if (in_array($this->method, array(static::METHOD_POST, static::METHOD_PUT))) {
            if($this->method === static::METHOD_POST) {
                curl_setopt($oCurl, CURLOPT_POST, true);
            }

            //Prepare parameters for upload file
            if(!empty($this->files)) {
                foreach ($this->files as $key => $value) {
                    if ((version_compare(PHP_VERSION, '5.5') >= 0)) {
                        $this->params[$key] = new \CURLFile($value);
                        curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, true);
                    } else {
                        $this->params[$key] = "@".$value;
                    }
                }
                $this->header[] = 'Content-Type: multipart/form-data';
            }

            if(!empty($this->raw) && empty($this->files)) {
                //Post or Put Json body
                $this->header[] = 'Content-Type: application/json';
                $this->header[] = 'Content-Length: '.strlen(json_encode($this->raw));
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, json_encode($this->raw));
            } elseif(!empty($this->files))  {
                //Post Upload
                //curl_setopt($oCurl, CURLOPT_BUFFERSIZE, 128);
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, $this->params);
            } else {
                //Post form
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($this->params));
            }
        }

        //Security
        if($this->security instanceof ApiSecurityHandle) {
            if($this->security->getType() === ApiSecurityHandle::TYPE_HTTP) {
                curl_setopt($oCurl, CURLOPT_USERPWD, $this->security->getUsername() . ':' . $this->security->getPassword());
            } elseif($this->security->getType() === ApiSecurityHandle::TYPE_WSSE) {
                $this->header[] = $this->security->createWsseHeader();
            }
        }

        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $this->header);

        return $oCurl;
    }

    private function parseUrl()
    {
        $parser = parse_url($this->url);

        $this->ssl = false;
        if(strtolower($parser['scheme']) === 'https') {
            $this->ssl = true;
        }

        $this->port = 80;
        if(isset($parser['port'])) {
            $this->port = $parser['port'];
        } else {
            if($this->ssl === true) {
                $this->port = 443;
            }
        }
    }
}
