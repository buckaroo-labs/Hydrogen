<?php
	class curlAPI {

		protected $ch;
		protected $userAgent;
		protected $authHeader;
		protected $responseBody;
		protected $debug;

		protected $ratelimit;
		protected $responseHeaders;
		protected $pageNum;
		protected $perPage;
		protected $itemCount;
		protected $pageCount;
		protected $apiBase;
		protected $apiName;
		protected $URL;

		private function debug($message) {
			if ($this->debug) echo $message . "\n";
		}
		public function setRateLimit($limit) {
			$this->ratelimit=$limit;
		}
		public function getItemCount() {
			return $this->itemCount;
		}
		public function getURL() {
			return $this->URL;
		}
		public function __construct($userAgent,$authHeader,$per_page=50,$debug=false) {
			$this->debug=$debug;
			$this->ratelimit=60;
			$this->perPage=$per_page;
			$this->userAgent=$userAgent;
			$this->authHeader=$authHeader;
			$this->ch = curl_init();
			$reqheaders = [	$this->userAgent,	$this->authHeader];
			$this->responseHeaders=[];
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $reqheaders);
			curl_setopt($this->ch, CURLOPT_HEADER, 1);
			curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, 
				[$this, '_handleHeader']);
			
		}
		private function _handleHeader($curl, $header_line) {
								
			$len = strlen($header_line);
			$header = explode(':', $header_line, 2);
			if (count($header) < 2) // ignore invalid headers
			return $len;

			$this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
			return $len;
					
		}

		public function setURL($url) {
			$this->URL=$url;
			curl_setopt($this->ch, CURLOPT_URL, $url);
		}

		public function setAPIBase ($base) {
			$this->apiBase=$base;
		}

		public function setAPIName($apiName) {
			$this->apiName=$apiName;
		}

		public function getID($idNumber) {
			$this->setURL($this->apiBase . "/" . $this->apiName . "/" . $idNumber);
			return $this->exec();
		}

		public function setSearchCriteria($criteria='') {
			$default='';
			//$default="?per_page=" . $this->perPage;
			$this->setURL($this->apiBase . "/" . $this->apiName . $default . $criteria);
		}

		public function addSearchCriteria($criteria) {
			//if none set, set with any default
			if(strcmp($this->URL,$this->apiBase . "/" . $this->apiName)==0) {
				$this->setSearchCriteria($criteria);
			} else {
				$this->setURL($this->URL . $criteria);
			}
		}

		public function getRateLimit() {
			return $this->ratelimit;
		}
		public function getResponseHeaders() {
			return $this->responseHeaders;
		}
		public function exec() {
			$this->responseHeaders=[];
			$server_output = curl_exec($this->ch);
			$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
			//$header = substr($server_output, 0, $header_size);
			$this->responseBody = substr($server_output, $header_size);

			$result_json=json_decode($this->responseBody);
			if (isset($result_json->count)) {
				$this->itemCount=$result_json->count;
				$this->pageCount=ceil($this->itemCount/$this->perPage);
			} elseif (isset($result_json->pagination->items)) {
				$this->itemCount=$result_json->pagination->items;
				$this->pageCount=ceil($this->itemCount/$this->perPage);
			}
			
			return $result_json;
		}
	} //end class

?>