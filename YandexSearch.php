<?

use Bitrix\Main\UI\PageNavigation;
use \Bitrix\Main\Web\Uri;

class YandexSearch
{
    /**
     * @var string Ссылка на API Яндекс Поиска
     */
    private $baseURL = 'https://catalogapi.site.yandex.net/v1.0?';
    /**
     * @var string[] Массив с параметрами для запроса к яндексу (Query String)
     */
    private $query = [];
    /**
     * @var array Массив с результатами поиска
     */
    private $result = [];
    /**
     * @var PageNavigation Объект постраничной навигации.
     */
    private $pagination;


    /**
     * Конструктор класса YandexSearch
     * @param string $apikey API-ключ для получения результатов поиска в JSON. Выдаётся Яндекс.
     * @param string $searchId ID поисковика. Выдаётся Яндекс.
     * @param string $searchText Строка запроса. Берется из строки поиска.
     * @param string $pageIdentity Идентификатор для пагинации (ключ get-параметра).
     * @param int $per_page Количество элементов на одной странице. Используется при запросе к Яндекс и генерации пагинации.
     *
     * @return YandexSearch
     */
    function __construct(
        string $apikey = '',
        string $searchId = '',
        string $searchText = '',
        string $pageIdentity = 'page',
        int $per_page = 100
    )
    {
        $this->apikey = ($apikey !== '' ? $apikey : false);
        $this->searchId = ($searchId !== '' ? $searchId : false);
        $this->searchText = ($searchText !== '' ? $searchText : false);
        $this->initPagination($pageIdentity, $per_page);
        if ($apikey || $searchId || $searchText) {
            $this->addParams([
                'apikey' => $apikey,
                'searchid' => $searchId,
                'text' => $searchText,
                'page' => $this->pagination->getCurrentPage() - 1,
                'per_page' => $per_page,
                'available' => "true"
            ]);
        }
        $this->getItems();

    }

    public function initPagination($page_identity, $per_page)
    {
        $this->pagination = new \Bitrix\Main\UI\PageNavigation($page_identity);
        $this->pagination->allowAllRecords(true)
            ->setPageSize($per_page)
            ->initFromUri();
    }

    public function getItems(){
        // получаем все get параметры
        $query = explode('&', $_SERVER['QUERY_STRING']);

        // ищем get параметры для запроса
        foreach ($query as $param) {
            [$key, $value] = explode('=', $param, 2);
            $formattedKey = $this->formatString($key);
            $formattedValue = $this->formatString($value);

            if (strripos($formattedKey, "searchFilter")) {
                continue;
            }

            if ($formattedKey === 'how' && mb_strlen($formattedValue) <= 1) {
                continue;
            }
            if ($formattedKey === 'section_id') {
                $formattedKey = 'category_id';
            }
            if ($formattedKey === 'page' && array_key_exists('page',$this->query)) {
                $pageNumber = str_replace('page-', '',$formattedValue);
                $this->query['page'][0] = (int)$pageNumber - 1;
            } else {
                $this->addParams([$formattedKey => $formattedValue]);
            }
        }
        $result = $this->sendInitializedQuery();
        $this->parseResponse($result);
        $this->initMisspellsContent();
        $this->pagination->setRecordCount($this->result['ITEMS_TOTAL']);
    }


    /**
     * addParams - Обертка для добавления или изменения существующих get-параметров в запрос
     * @param $params - Массив параметров "key"=>"value"
     *
     */
    public function addParams($params)
    {
        foreach ($params as $key => $value) {
            if (!$this->query[$key]) {
                $this->query[$key] = [];
            }
            $this->query[$key][] = $value;
        }
    }

    /**
     * sendInitializedQuery - Обертка для отправки get-запроса
     * @return bool
     */
    public function sendInitializedQuery()
    {
        $queryString = [];
        foreach ($this->query as $key => $arrayOfValues) {
            foreach ($arrayOfValues as $value) {
                $queryString[] = http_build_query([$key => $value]);
            }
        }
        $requestURL = $this->baseURL . join('&', $queryString);
        $requestURL = str_replace('%2B', '%20', $requestURL);

        $channel = curl_init($requestURL);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($channel, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($channel);

        $error = curl_errno($channel);
        if ($error) {
            $date = date('Y.m.d');
            $time = date('H:i:s');
            file_put_contents(
                __DIR__ . "/log_{$date}.log",
                "[{$date} {$time}] " . var_export( $error.": ". curl_error($channel), true) . "\n\n",
            FILE_APPEND
            );
        }

        curl_close($channel);

        return $result;
    }

    /**
     * @return string
     */
    public function getCategoryForQuery()
    {
        $request = $this->sendInitializedQuery();
        $parseRequest = json_decode($request, true);
        $categoryList = $parseRequest['categoryList'];
        $this->result['CATEGORIES_LIST'] = $parseRequest['categoryList'];
        $this->result['SMART_FILTER_PARAMS'] = $parseRequest['enumParameters'];
        $categoryColumn = array_column($categoryList, 'found', 'id');
        return array_keys($categoryColumn, max($categoryColumn))[0];
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    public function parseResponse($request)
    {
        $parseRequest = json_decode($request, true);
        $this->result['ITEMS_LIST'] = array_column($parseRequest['documents'], 'id');
        if($this->result['CATEGORIES_LIST'] == NULL){
            $this->result['CATEGORIES_LIST'] = $parseRequest['categoryList'];
        }

        if($this->result['SMART_FILTER_PARAMS'] == NULL){
            $this->result['SMART_FILTER_PARAMS'] = $parseRequest['enumParameters'];
        }

        $this->result['MISSPELL'] = $parseRequest['misspell'];
        $this->result['ITEMS_PER_PAGE'] = $parseRequest['perPage'];
        $this->result['ITEMS_TOTAL'] = $parseRequest['docsTotal'];
        $this->result['PAGINATION'] = $this->pagination;
    }

    /**
     * getTotalCount - Метод для получения максимального количества элементов по данному запросу
     * @return integer
     */
    public function getTotalCount(): int
    {
        return $this->resultIds['itemsTotal'];
    }


    public function formatString($str): string
    {
        $str = trim($str);
        $str = stripslashes($str);
        return htmlspecialchars($str);
    }

    /**
     * Метод создает сообщения об ошибках в поисковом запросе
     * @return void
     */
    private function initMisspellsContent(): void
    {
        if (!empty($this->result['MISSPELL']['reask'])) {
            $text = $this->result['MISSPELL']['reask']['text'];

            $this->result['REASK_LINK'] = $this->getMisspellLink($text);
            $this->result['REASK_TEXT'] = "<p>Запрос был исправлен. Показаны результаты по запросу: «<a href='{$this->result['REASK_LINK']}'>{$text}</a>»</p>";
        }

        if ($this->result['ITEMS_TOTAL'] === 0
            && !empty($this->result['MISSPELL']['misspell']['text'])
            && $this->result['MISSPELL']['misspell']['rule'] === 'Misspell'
        ) {
            $text = $this->result['MISSPELL']['misspell']['text'];

            $this->result['MISSPELL_LINK'] = $this->getMisspellLink($text);
            $this->result['MISSPELL_TEXT'] = "<p>Возможно Вы имели в виду: «<a href='{$this->result['MISSPELL_LINK']}'>{$text}</a>»</p>";
        }
    }

    /**
     * Метод формирует ссылку для сообщения об ошибке поискового запроса
     * @param string $query
     * @return string
     */
    private function getMisspellLink(string $query): string
    {
        return $this->getSiteURL() . '?' . http_build_query(['q' => $query]);
    }

    /**
     * Метод формирует ссылку до страницы поиска
     * @return string
     */
    private function getSiteURL(): string
    {
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
        $urlArray = explode('?', $url);
        if (!$urlArray) {
            return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/catalog/';
        }
        return $urlArray[0];
    }

}
