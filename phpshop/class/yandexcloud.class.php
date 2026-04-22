<?php

/*
 * Библиотека работы с YandexGPT API
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 * @todo https://yandex.cloud/ru/docs/foundation-models/concepts/yandexgpt/models
 * @todo https://yandex.cloud/ru/docs/foundation-models/text-generation/api-ref/TextGeneration/completion
 */

class YandexGPT {

    const TextGeneration = "https://llm.api.cloud.yandex.net/foundationModels/v1/completion";
    const GET_TOKEN = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';

    function __construct() {
        $this->PHPShopSystem = new PHPShopSystem();

        $this->TOKEN = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_token');
        $this->FOLDER = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_id');
        $this->API_URL = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_model');
    }

    public function init() {
        if (!empty($this->TOKEN) and ! empty($this->FOLDER))
            return true;
    }

    public function html($text) {
        if (class_exists('Parsedown')) {
            $Parsedown = new Parsedown();
            $text = $Parsedown->text($text);
        }
        return $text;
    }

    public function text($user, $system, $temperature = "0.3", $maxTokens = 1000) {

        $params = [
            "modelUri" => 'gpt://' . $this->FOLDER . '/' . $this->API_URL,
            "completionOptions" => [
                "stream" => false,
                "temperature" => (float) $temperature,
                "maxTokens" => (int) $maxTokens
            ],
            "messages" => [
                [
                    "role" => "user",
                    "text" => (string) PHPShopString::win_utf8($user)
                ],
                [
                    "role" => "system",
                    "text" => (string) PHPShopString::win_utf8($system)
                ],
            ]
        ];

        $result = $this->request(self::TextGeneration, $params);
        return $result;
    }

    private function getIAM() {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::GET_TOKEN,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(["yandexPassportOauthToken" => $this->TOKEN]),
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    private function request($url, $data = []) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->getIAM()['iamToken'],
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

}

/*
 * Библиотека работы с Yandex Search API
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 * @todo https://yandex.cloud/ru/docs/search-api/concepts/response#response-format
 * @todo https://rookee.ru/blog/yazyk-zaprosov-yandex/
 */
class YandexSearch {

    const API_URL = 'https://searchapi.api.cloud.yandex.net/v2/web/search';
    const API_URL_IMG = 'https://searchapi.api.cloud.yandex.net/v2/image/search';

    function __construct() {
        $this->PHPShopSystem = new PHPShopSystem();
        $this->TOKEN = $this->PHPShopSystem->getSerilizeParam('ai.yandexsearch_token');
        $this->FOLDER = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_id');
    }

    public function init() {
        if (!empty($this->TOKEN) and ! empty($this->FOLDER))
            return true;
    }

    private function request($text) {

        $query = [
            "query" => [
                "searchType" => "SEARCH_TYPE_RU",
                "queryText" => $text,
                "familyMode" => "FAMILY_MODE_NONE",
                "page" => "0",
                "fixTypoMode" => "FIX_TYPO_MODE_ON"
            ],
            "sortSpec" => [
                "sortMode" => "SORT_MODE_BY_RELEVANCE",
                "sortOrder" => "SORT_ORDER_DESC"
            ],
            "groupSpec" => [
                "groupMode" => "GROUP_MODE_DEEP",
                "groupsOnPage" => "10",
                "docsInGroup" => "3"
            ],
            "maxPassages" => "1",
            "region" => "225",
            "l10N" => "LOCALIZATION_RU",
            "folderId" => $this->FOLDER,
            "responseFormat" => "FORMAT_XML",
            "userAgent" => ""
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => json_encode($query),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->TOKEN
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    private function request_img($text, $itype, $iorient, $isize, $page, $site = false) {

        $query = [
            "query" => [
                "searchType" => "SEARCH_TYPE_RU",
                "queryText" => $text,
                "familyMode" => "FAMILY_MODE_NONE",
                "page" => (int)$page,
                "fixTypoMode" => "FIX_TYPO_MODE_ON"
            ],
            "site" => (string)$site,
            "docsOnPage" => 20,
            "folderId" => $this->FOLDER,
            "userAgent" => ""
        ];
        
        if(!empty($iorient))
            $query['imageSpec']['orientation']=(string)$iorient;
        
        if(!empty($isize))
            $query['imageSpec']['size']=(string)$isize;
        
        if(!empty($itype))
            $query['imageSpec']['format']=(string)$itype;
        

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => self::API_URL_IMG,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => json_encode($query),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->TOKEN
            ],
        ]);

        $response = curl_exec($curl);
        //echo $response;
        curl_close($curl);

        return $response;
    }

    public function search($text) {
        $response = json_decode($this->request($text), true);
        $xml = simplexml_load_string(base64_decode($response['rawData']));

        if (isset($xml->response->results->grouping->group)) {
            foreach ($xml->response->results->grouping->group as $item) {
                $result[] = ['title' => (string) $item->doc->title, 'url' => (string) $item->doc->url];
            }

            return $result;
        } else
            $result = (string) $xml->response->error[0];
    }

    public function search_img($text, $itype = null, $iorient = null, $isize = null, $page = 0, $site = null) {
        
        $response = json_decode($this->request_img($text, $itype, $iorient, $isize, $page, $site), true);
        $response = str_replace(['file-size', 'image-properties', 'original-width', 'original-height', 'thumbnail-link', 'mime-type','image-link'], ['filesize', 'properties', 'width', 'height', 'thumbnail', 'type','url'], base64_decode($response['rawData']));

        $xml = simplexml_load_string($response);

        if (isset($xml->response->results->grouping->group)) {
            foreach ($xml->response->results->grouping->group as $item) {
   
                $result[] = ['url' => (string) $item->doc->url, 'size' => (string) $item->doc->properties->filesize, 'width' => (string) $item->doc->properties->width, 'height' => (string) $item->doc->properties->height, 'thumbnail' => (string) $item->doc->properties->thumbnail, 'type' => (string) $item->doc->properties->type];
            }
        } else
            $result = (string) $xml->response->error[0];

        return $result;
    }

}
