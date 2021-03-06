<?php
if (!defined('MAINWP_WPVIVID_EXTENSION_PLUGIN_DIR')){
    die;
}

include_once 'class-wpvivid-s3.php';

class MainWP_WPvivid_Base_S3 extends Mainwp_Wpvivid_S3{
	var $signVer = 'v2';

    /**
     * Set Signature Version
     *
     * @param string $version
     * @return void
     */
    public function setSignatureVersion($version = 'v2') {
        $this->signVer = $version;
    }

    public function setServerSideEncryption($value = self::SSE_AES256) {
        $this->_serverSideEncryption = $value;
    }
    public function setStorageClass($value = self::STORAGE_CLASS_STANDARD_IA){
        $this -> _storageClass = $value;
    }

    public function initiateMultipartUpload ($bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = self::STORAGE_CLASS_STANDARD) {

        $rest = new MainWP_WPvivid_S3Request('POST', $bucket, $uri, $this->endpoint, $this);
        $rest->setParameter('uploads','');

        if (is_array($requestHeaders) && !empty($requestHeaders))
            foreach ($requestHeaders as $h => $v) $rest->setHeader($h, $v);
        if(is_array($metaHeaders) && !empty($metaHeaders))
            foreach ($metaHeaders as $h => $v) $rest->setAmzHeader('x-amz-meta-'.$h, $v);

        if ($this -> _storageClass !== self::STORAGE_CLASS_STANDARD) // Storage class
            $rest->setAmzHeader('x-amz-storage-class', $this -> _storageClass);
        if ($this -> _serverSideEncryption !== self::SSE_NONE) // Server-side encryption
            $rest->setAmzHeader('x-amz-server-side-encryption', $this -> _serverSideEncryption);

        $rest->setAmzHeader('x-amz-acl', $acl);

        $rest->getResponse();
        if (false === $rest->response->error && 200 !== $rest->response->code) {
            $rest->response->error = array('code' => $rest->response->code, 'message' => 'Unexpected HTTP status');
        }

        if (false !== $rest->response->error) {
            $this->__triggerError(sprintf("Mainwp_WPvivid_S3::initiateMultipartUpload(): [%s] %s",
                $rest->response->error['code'], $rest->response->error['message']), __FILE__, __LINE__);
            return false;
        } elseif (isset($rest->response->body)) {
            if (is_a($rest->response->body, 'SimpleXMLElement')) {
                $body = $rest->response->body;
            } else {
                $body = new SimpleXMLElement($rest->response->body);
            }
            return (string) $body->UploadId;
        }
        return false;
    }

	public function uploadPart ($bucket, $uri, $uploadId, $filePath, $partNumber, $partSize = 5242880) {
		$rest = new MainWP_WPvivid_S3Request('PUT', $bucket, $uri, $this->endpoint, $this);
		$rest->setParameter('partNumber', $partNumber);
		$rest->setParameter('uploadId', $uploadId);

		$fileOffset = ($partNumber - 1 ) * $partSize;
		$fileBytes = min(filesize($filePath) - $fileOffset, $partSize);
		if ($fileBytes < 0) $fileBytes = 0;

		$rest->setHeader('Content-Type', 'application/octet-stream');
		$rest->data = "";

		if ($handle = fopen($filePath, "rb")) {
			if ($fileOffset >0) fseek($handle, $fileOffset);
			$bytes_read = 0;
			while ($fileBytes>0 && $read = fread($handle, max($fileBytes, 131072))) { //128kb
				$fileBytes = $fileBytes - strlen($read);
				$bytes_read += strlen($read);
				$rest->data = $rest->data . $read;
			}
			fclose($handle);
		} else {
			return false;
		}

 		$rest->setHeader('Content-MD5', base64_encode(md5($rest->data, true)));
		$rest->size = $bytes_read;

		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("S3::uploadPart(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return $rest->headers['hash'];
	}

	public function completeMultipartUpload ($bucket, $uri, $uploadId, $parts) {
		$rest = new MainWP_WPvivid_S3Request('POST', $bucket, $uri, $this->endpoint, $this);
		$rest->setParameter('uploadId', $uploadId);

		$xml = "<CompleteMultipartUpload>\n";
		$partno = 1;
		foreach ($parts as $etag) {
			$xml .= "<Part><PartNumber>$partno</PartNumber><ETag>$etag</ETag></Part>\n";
			$partno++;
		}
		$xml .= "</CompleteMultipartUpload>";

		$rest->data = $xml;
		$rest->size = strlen($rest->data);
		$rest->setHeader('Content-Type', 'application/xml');

		$rest = $rest->getResponse();
		if (false === $rest->error && 200 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			if ('InternalError' == $rest->error['code'] && 'This multipart completion is already in progress' == $rest->error['message']) {
				return true;
			}
			$this->__triggerError(sprintf("S3::completeMultipartUpload(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;

	}

	public function abortMultipartUpload ($bucket, $uri, $uploadId) {
		$rest = new MainWP_WPvivid_S3Request('DELETE', $bucket, $uri, $this->endpoint, $this);
		$rest->setParameter('uploadId', $uploadId);

		$rest = $rest->getResponse();
		if (false === $rest->error && 204 !== $rest->code) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}

		if (false !== $rest->error) {
			$this->__triggerError(sprintf("S3::abortMultipartUpload(): [%s] %s",
			$rest->error['code'], $rest->error['message']), __FILE__, __LINE__);
			return false;
		}
		return true;
	}

    public function getObject($bucket, $uri, $saveTo = false, $resume = false) {
        $rest = new MainWP_WPvivid_S3Request('GET', $bucket, $uri, $this->endpoint, $this);
        if (false !== $saveTo) {
            if (is_resource($saveTo)) {
                $rest->fp = $saveTo;
                if (!is_bool($resume)) $rest->setHeader('Range', $resume);
            } else {
                if ($resume && file_exists($saveTo)) {
                    if (false !== ($rest->fp = @fopen($saveTo, 'ab'))) {
                        $rest->setHeader('Range', "bytes=".filesize($saveTo).'-');
                        $rest->file = realpath($saveTo);
                    } else {
                        $rest->response->error = array('code' => 0, 'message' => 'Unable to open save file for writing: '.$saveTo);
                    }
                } else {
                    if (false !== ($rest->fp = @fopen($saveTo, 'wb')))
                        $rest->file = realpath($saveTo);
                    else
                        $rest->response->error = array('code' => 0, 'message' => 'Unable to open save file for writing: '.$saveTo);
                }
            }
        }
        if (false === $rest->response->error) $rest->getResponse();

        if (false === $rest->response->error && ( !$resume && 200 != $rest->response->code) || ( $resume && 206 != $rest->response->code && 200 != $rest->response->code))
            $rest->response->error = array('code' => $rest->response->code, 'message' => 'Unexpected HTTP status');
        if (false !== $rest->response->error) {
            $this->__triggerError(sprintf("Mainwp_WPvivid_S3::getObject({$bucket}, {$uri}): [%s] %s",
                $rest->response->error['code'], $rest->response->error['message']), __FILE__, __LINE__);
            return false;
        }
        return $rest->response;
    }

    public function listObject($bucket, $path)
    {
        $rest = new MainWP_WPvivid_S3Request('GET', $bucket, '', $this->endpoint, $this);
        $rest->setParameter('prefix', $path);
        //$rest->setParameter('delimiter', $path);
        $response = $rest->getResponse();
        if ($response->error === false && $response->code !== 200)
        {
            //$response->error = array('code' => $response->code, 'message' => 'Unexpected HTTP status');
            $ret['result']='failed';
            $ret['error']=$response['message'].' '.$response->code;
            return $ret;
        }

        if ($response->error !== false)
        {
            $ret['result']='failed';
            $ret['error']=sprintf("S3::getBucket(): [%s] %s", $response->error['code'], $response->error['message']);
            return $ret;
        }

        $results = array();

        if (isset($response->body, $response->body->Contents))
        {
            foreach ($response->body->Contents as $c)
            {
                $results[] = array(
                    'name' => (string)$c->Key,
                    'size' => (int)$c->Size,
                );
            }
        }

        $ret['result']='success';
        $ret['data']=$results;
        return $ret;
    }
}